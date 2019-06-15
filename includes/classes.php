<?php
	function time_ago($tm,$rcs = 0) {
		   $cur_tm = time();
		   $dif = $cur_tm-$tm;
		   $pds = array('second','minute','hour','day','week','month','year','decade');
		   $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		   for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
		
		   $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
		   if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
		   return $x;
	}
	
	class Session {
		protected $conn,$current_rally,$admin;	
		
		public function __construct($conn){
			session_start();
			$this->conn = $conn;
			if(isset($_SESSION["current_rally_id"]) && isset($_SESSION["current_admin_id"])){
				$this->current_rally = new Rally($conn,$this->conn->real_escape_string($_SESSION["current_rally_id"]));
				$this->admin = new Admin($conn,null,$this->conn->real_escape_string($_SESSION["current_admin_id"]));
			}
		}
		
		public function login ($email,$password){
			try {
				$this->admin = new Admin($this->conn,$email);
				$first_rally_in_list = $this->admin->getAuthorizedRallies();
				$this->current_rally = new Rally($this->conn,$first_rally_in_list[0]->getRallyId());
			} catch (Exception $e) {
				return $e->getMessage();
			}
			if($this->admin->comparePassword($password)){
				session_regenerate_id();
				$_SESSION['current_rally_id'] = $this->current_rally->getRallyId();
				$_SESSION['current_admin_id'] = $this->admin->getId();
				session_write_close();
				return true;
			}
			return "Username and password did not match.";
		}
		
		public function getCurrentRally (/*$conn=null*/){
			/*if(isset($conn))
				$this->current_rally->updateConn($conn);*/
			return $this->current_rally;
		}
		
		public function setCurrentRally (Rally $rally){
			$this->current_rally = $rally;
			$_SESSION['login_data'] = $this;
		}
		
		public function getAdmin (){
			return $this->admin;
		}
		
		public function outputHeader ($pagetitle){
			$header_template = new Template("includes/head.inc.tpl",$this->getAdmin(),$this->current_rally->getRallyId());
			$header_template->set("sitetitle",$this->getCurrentRally()->getTitle());
			$header_template->set("pagetitle",$pagetitle);
			$header_template->set("username",$this->getAdmin()->getName());
			echo $header_template->output();
		}
		
		public function outputPrimaryHeader ($pagetitle){
			$header_template = new Template("includes/primaryhead.inc.tpl",$this->getAdmin(),$this->current_rally->getRallyId());
			$header_template->set("sitetitle",$this->getCurrentRally()->getTitle());
			$header_template->set("pagetitle",$pagetitle);
			$header_template->set("username",$this->getAdmin()->getName());
			echo $header_template->output();
		}
		
		/*
			If logged in, returns the id of the admin. Otherwise auto-redirects to login page.
		*/
		public function auth(){
			date_default_timezone_set("America/Chicago");
			if(!isset($_SESSION["current_rally_id"]) || !isset($_SESSION["current_admin_id"])) {
				header('location: login.php');
				exit();
			}
			$verify_admin_query = $this->conn->query("SELECT * FROM `rallies` INNER JOIN `rally_admins` ON rallies.id=rally_admins.rally_id WHERE rally_admins.admin_id=" . $this->getAdmin()->getId() . " AND rally_admins.rally_id=" . $this->getCurrentRally()->getRallyId());
			if($verify_admin_query->num_rows!=1) {
				header('location: login.php');
				exit();
			}
		}
		
		public function logout(){
			unset($_SESSION['login_data']);
		}
	}
	
	class Template {
		protected $file_contents, $admin, $currentrallyid;
		protected $values = array();
	  
		public function __construct($file,$admin=null,$currentrallyid=null) {
			if (!file_exists($file)) {
				return "Error loading template file ($file).";
			}
			$this->file_contents = file_get_contents($file);
			$this->admin = $admin;
			$this->currentrallyid = $currentrallyid;
			$this->rallyLoop();
		}
		
		public function set($key, $value) {
			$this->values[$key] = $value;
		}
		
		public function rallyLoop(){
			preg_match_all("/\[foreach @rally\][\s\S]+\[\/foreach]/", $this->file_contents,$matches);
			if(count($matches[0])==0)
				return;
			$foreach_output = $matches[0][0];
			$replacements["[foreach @rally]"] = "";
			$replacements["[/foreach]"] = "";
			$foreach_output = str_replace(array_keys($replacements), array_values($replacements), $foreach_output);
			$new_replacement = "";
			$authorized = $this->admin->getAuthorizedRallies();
			foreach($authorized as $rally){
				if($rally->getRallyId() != $this->currentrallyid){
					$new_replacement .= str_replace(array("[@rallytitle]","[@rallyid]","[@rallydate]"),array($rally->getTitle(),$rally->getRallyId(),$rally->getRallyDate()),$foreach_output) . '<li class="divider"></li>';
				}
			}
			$this->file_contents = str_replace($matches[0][0],$new_replacement,$this->file_contents);
		}
		
		public function output() {
			foreach ($this->values as $key => $value) {
				$tagToReplace = "[@$key]";
				$this->file_contents = str_replace($tagToReplace, $value, $this->file_contents);
			}
			return $this->file_contents;
		}
	}
	
	class Rally {
		protected $conn,$rally_id,$airport,$title,$rallydate;
		
		public function __construct ($conn,$rally_id=null){
			$this->updateConn($conn);
			if(!empty($rally_id)){
				$this->rally_id = $this->conn->real_escape_string($rally_id);
				$rally_query = $this->conn->query("SELECT * FROM `rallies` WHERE `id`=$this->rally_id");
				$settings_array = $rally_query->fetch_assoc();
				$this->airport = $settings_array['airport'];
				$this->title = $settings_array['title'];
				$this->rallydate = $settings_array['rally_date'];
			}
		}
		
		protected function dangerAlert ($message){
			return "<div class=\"alert alert-danger\">" . $message . "</div>";
		}
		
		protected function successAlert ($message){
			return "<div class=\"alert alert-success\">" . $message . "</div>";
		}
		
		public function getPilotList($id=null){
			if(!isset($id)) {
				$query = $this->conn->query("SELECT * FROM `pilots` INNER JOIN `rally_pilots` ON rally_pilots.pilot_id=pilots.id WHERE rally_pilots.rally_id=$this->rally_id");
				$pilotlist = new PilotList();
				while($row=$query->fetch_assoc()){
					$pilotlist->add( new Pilot($this->conn,$row) );
				}
				return $pilotlist;
			} else {
				$pilot = new Pilot($this->conn);
				$pilot->load($id);
				return $pilot;
			}
		}
		
		public function addPilot(Pilot $pilot){
			//create link in rally_pilots table to this rally
			$this->conn->query("INSERT INTO `rally_pilots` (`rally_id`, `pilot_id`) VALUES ('" . $this->getRallyId() . "', '" . $pilot->getId() . "')");
		}
		
		public function getYoungEaglesList($id=null){
			if(!isset($id)) {
				$query = $this->conn->query("SELECT * FROM `registrants` INNER JOIN `rally_registrants` ON `rally_registrants`.`registrant_id`=`registrants`.`id` WHERE `rally_registrants`.`rally_id`=$this->rally_id ORDER BY `registrants`.`assigned_num` ASC");
				$youngeaglelist = new YoungEagleList();
				while($row=$query->fetch_assoc()){
					$youngeaglelist->add(new YoungEagle($this->conn,$row));
				}
				return $youngeaglelist;
			} else {
				$pilot = new Pilot($this->conn);
				$pilot->load($id);
				return $pilot;
			}
		}
		
		public function addYoungEagle(YoungEagle $youngeagle){
			$this->conn->query("INSERT INTO `rally_registrants` (`rally_id`, `registrant_id`) VALUES ('" . $this->getRallyId() . "', '" . $youngeagle->getId() . "')");
			return true;
		}

		public function updateConn($conn){
			$this->conn = $conn;	
		}
		public function getRallyId(){
			return $this->rally_id;
		}
		
		public function setAirport ($airport){
			$this->airport = $airport;	
		}
		public function getAirport (){
			return $this->airport;	
		}
		//Title Functions
		public function getTitle() {
			return $this->title;
		}
		public function setTitle ($title){
			$this->title = $title;
		}
		//Rally Date Functions
		public function getRallyDate() {
			return $this->rallydate;
		}
		public function setRallyDate($value){
			$this->rallydate = $value;
		}
		//Submit Functions
		protected function readyForMySQL(){
			$flag = true;
			if(trim($this->airport)==""){
				$this->setting_errors['airport_name'] = "Airport field cannot be empty";
				$flag=false;
			}
			if(trim($this->rallydate)==""){
				$this->setting_errors['rally_date'] = "Rally date field cannot be empty";
				$flag=false;
			}
			if(trim($this->title)==""){
				$this->setting_errors['title'] = "Title field cannot be empty";
				$flag=false;
			}
			return $flag;
			
			if($flag==true){
				$this->airport = $this->conn->real_escape_string($this->getAirport());
				$this->title = $this->conn->real_escape_string($this->getTitle());
				$this->rallydate = $this->conn->real_escape_string($this->getRallyDate());
			}
		}
		public function updateMySQL (){
			if( $this->readyForMySQL() ){
				$this->conn->query("UPDATE `rallies` SET `airport`='" . $this->getAirport() . ", `title`='" . $this->getTitle() . "', `rally_date`='" . $this->getRallyDate() . "' WHERE `id`='" . $this->getRallyId() . "';");
			} else {
				return false;
			}
		}
		public function insertMySQL (){
			if( $this->readyForMySQL() ){
				$this->conn->query("INSERT INTO `rallies` (`airport`, `title`, `rally_date`) VALUES ('" . $this->getAirport() . "', '" . $this->getTitle() . "', '" . $this->getRallyDate() . "');");
				$this->rally_id = $this->conn->insert_id;
			} else {
				return false;
			}
		}
		//Errors
		public function error($error_name){
			if(isset($this->setting_errors[$error_name])){
				echo $this->dangerAlert( $this->setting_errors[$error_name] );
			}
		}
	}
	
	class Person {
		protected $conn,$id,$first_name,$middle_initial,$last_name,$email;
		
		public function __construct($conn) {
			//Create private instance of mysql connection
			$this->conn = $conn;
		}
		
		protected function dangerAlert ($message){
			return "<div class=\"alert alert-danger\">" . $message . "</div>";
		}
		
		protected function successAlert ($message){
			return "<div class=\"alert alert-success\">" . $message . "</div>";
		}
		
		protected function setId($id){
			$this->id=$id;
		}
		
		public function getId() {
			return $this->id;
		}
		
		public function setFirstName($first_name){
			$this->first_name = $first_name;
		}
		
		public function getFirstName(){
			return $this->first_name;
		}
		
		public function setLastName($last_name){
			$this->last_name = $last_name;
		}
		
		public function getLastName(){
			return $this->last_name;
		}
		
		public function setMiddleInitial($mi){
			$this->middle_initial = $mi;
		}
		
		public function getMiddleInitial(){
			return $this->middle_initial;
		}
		
		public function setName($first_name, $last_name, $middle_initial = ""){
			$this->first_name = $first_name;
			$this->middle_initial = $middle_initial;
			$this->last_name = $last_name;
		}
		
		public function getName() {
			$full_name = $this->first_name . " ";
			if(!empty($this->middle_initial))
				$full_name .= $this->middle_initial . " ";
			$full_name .= $this->last_name;
			return $full_name;
		}
		
		//Email Functions
		public function getEmail (){
			return $this->email;
		}
		
		public function setEmail($email){
			$this->email = $email;
		}
	}
	
	class Admin extends Person {
		protected $level, $password, $authorized_rallies, $settings;
		
		public function __construct ($conn,$email=null,$id=null){
			parent::__construct($conn);
			if(isset($email)){
				$admin_query = $this->conn->query("SELECT * FROM `admins` WHERE `email`='$email'");
				if($admin_query->num_rows == 1){
					$admin_data = $admin_query->fetch_assoc();
					$this->setId($admin_data['id']);
					$this->setFirstName($admin_data['first_name']);
					$this->setMiddleInitial($admin_data['middle_initial']);
					$this->setLastName($admin_data['last_name']);
					$this->setEmail($admin_data['email']);
					$this->setLevel($admin_data['level']);
					$this->setPassword($admin_data['password']);
					$authorized_query = $this->conn->query("SELECT * FROM `rally_admins` WHERE `admin_id`='".$this->getId()."'");
					$this->authorized_rallies = array();
					while($row=$authorized_query->fetch_assoc()){
						$this->authorized_rallies[] = new Rally($this->conn,$row['rally_id']);
					}
					return true; 
				} else {
					throw new Exception('Admin was unable to be properly created.');
				}
			}
			if(isset($id)){
				$admin_query = $this->conn->query("SELECT * FROM `admins` WHERE `id`='$id'");
				if($admin_query->num_rows == 1){
					$admin_data = $admin_query->fetch_assoc();
					$this->setId($admin_data['id']);
					$this->setFirstName($admin_data['first_name']);
					$this->setMiddleInitial($admin_data['middle_initial']);
					$this->setLastName($admin_data['last_name']);
					$this->setEmail($admin_data['email']);
					$this->setLevel($admin_data['level']);
					$this->setPassword($admin_data['password']);
					$this->setSetting("ye-x",$admin_data['ye_x']);
					$this->setSetting("ye-y",$admin_data['ye_y']);
					$this->setSetting("date-x",$admin_data['date_x']);
					$this->setSetting("date-y",$admin_data['date_y']);
					$this->setSetting("airplane-x",$admin_data['airplane_x']);
					$this->setSetting("airplane-y",$admin_data['airplane_y']);
					$this->setSetting("airport-x",$admin_data['airport_x']);
					$this->setSetting("airport-y",$admin_data['airport_y']);
					$authorized_query = $this->conn->query("SELECT * FROM `rally_admins` WHERE `admin_id`='".$this->getId()."'");
					$this->authorized_rallies = array();
					while($row=$authorized_query->fetch_assoc()){
						$this->authorized_rallies[] = new Rally($this->conn,$row['rally_id']);
					}
					return true; 
				} else {
					throw new Exception('Admin was unable to be properly created.');
				}
			}
		}
		
		public function setSetting($key,$value){
			$this->settings[$key] = $value;
		}
		
		public function getSetting($key=null){
			if($key==null)
				return $this->settings;
			return $this->settings[$key];	
		}
		
		public function resetSettings (){
			$this->setSetting("ye-x","60");
			$this->setSetting("ye-y",'100');
			$this->setSetting("date-x","40");
			$this->setSetting("date-y","133");
			$this->setSetting("airplane-x","130");
			$this->setSetting("airplane-y","133");
			$this->setSetting("airport-x","43");
			$this->setSetting("airport-y","143");
		}
		
		public function create($array){
			if(!filter_var($array['email'], FILTER_VALIDATE_EMAIL)){
				return "Please enter a valid email address.";
			} else if($password!=$confirm){
				return "Passwords did not match";
			}
			$this->setId($array['id']);
			$this->setFirstName($array['first_name']);
			$this->setMiddleInitial($array['middle_initial']);
			$this->setLastName($array['last_name']);
			$this->setEmail($array['email']);
			$this->setLevel($array['level']);
			$this->setPassword($array['password']);
		}
		
		public function setLevel ($level){
			$this->level = $level;	
		}
		
		public function getLevel (){
			return $this->level;	
		}
		
		public function getAuthorizedRallies (){
			return $this->authorized_rallies;
		}
		
		public function addRally (Rally $rally){
			$this->authorized_rallies[] = $rally;
			$this->conn->query("INSERT INTO `rally_admins` (`rally_id`, `admin_id`) VALUES ('" . $rally->getRallyId() . "', '" . $this->getId() . "');");
		}
		
		private function setPassword ($password){
			$this->password = $password;
		}
		
		public function comparePassword ($password){
			return $password == $this->password;	
		}
		
		private function readyForMySQL(){
			if(trim($this->getSetting("airplane-x"))==""){
				$this->setting_errors['airplane'] = "Airplane Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("airplane-y"))==""){
				$this->setting_errors['airplane'] = "Airplane Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("airport-x"))==""){
				$this->setting_errors['airport'] = "Airport Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("airport-y"))==""){
				$this->setting_errors['airport'] = "Airplane Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("date-x"))==""){
				$this->setting_errors['date'] = "Date Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("date-y"))==""){
				$this->setting_errors['date'] = "Date Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("ye-x"))==""){
				$this->setting_errors['ye'] = "Young Eagle Name Location field cannot be empty";
				$flag=false;
			}
			if(trim($this->getSetting("ye-y"))==""){
				$this->setting_errors['ye'] = "Young Eagle Name Location field cannot be empty";
				$flag=false;
			}
		}
		
		public function updateMySQL(){
			$admin_query = $this->conn->query("UPDATE `admins` SET `ye_x` = '" . $this->getSetting("ye-x"). "', `ye_y` = '" .$this->getSetting("ye-y") . "', `date_x` = '" . $this->getSetting("date-x") . "', `date_y` = '" . $this->getSetting("date-y") . "', `airplane_x` = '" . $this->getSetting("airplane-x") . "', `airplane_y` = '" . $this->getSetting("airplane-y") . "', `airport_x` = '" . $this->getSetting("airport-x") . "', `airport_y` = '" . $this->getSetting("airport-y") . "' WHERE `admins`.`id` = " . $this->getId());
		}
	}
	
	class Pilot extends Person {
		protected $eaa_number,$eaa_chapter,$aircraft_type,$error_array;
		
		public function __construct($conn,$pilot_array=null) {
			parent::__construct($conn);
			if(isset($pilot_array)){
				$this->id = $pilot_array['id'];
				$this->first_name = $pilot_array['first_name'];
				$this->middle_initial = $pilot_array['middle_initial'];
				$this->last_name = $pilot_array['last_name'];
				$this->eaa_number = $pilot_array['eaa_number'];
				$this->eaa_chapter = $pilot_array['eaa_chapter'];
				$this->aircraft_type = $pilot_array['aircraft_type'];
				$this->email = $pilot_array['email'];
				$this->error_array = array();
			}
		}
		
		public function load ($pilot_id){
			//Clean the pilot_id
			$this->id = $this->conn->real_escape_string($pilot_id);
			//Query for the pilot info
			$pilot = $this->conn->query("SELECT * FROM `pilots` WHERE `id`='$pilot_id'");
			$pilot_array = $pilot->fetch_assoc();
			$this->first_name = $pilot_array['first_name'];
			$this->middle_initial = $pilot_array['middle_initial'];
			$this->last_name = $pilot_array['last_name'];
			$this->eaa_number = $pilot_array['eaa_number'];
			$this->eaa_chapter = $pilot_array['eaa_chapter'];
			$this->aircraft_type = $pilot_array['aircraft_type'];
			$this->email = $pilot_array['email'];
			$this->error_array = array();
		}
		
		//EAA Number Functions
		public function getEAANumber (){
			return $this->eaa_number;
		}
		
		public function setEAANumber($eaa_number){
			$this->eaa_number = $eaa_number;
		}
		
		//EAA Chapter Functions
		public function getEAAChapter (){
			return $this->eaa_chapter;
		}
		
		public function setEAAChapter($eaa_chapter){
			$this->eaa_chapter = $eaa_chapter;
		}
		
		//Aircraft Functions
		public function getAircraftType (){
			return $this->aircraft_type;
		}
		
		public function setAircraftType($aircraft){
			$this->aircraft_type = $aircraft;
		}
		
		//returns false if there are error flags
		protected function readyForMySQL (){
			$flag = true;
			if(empty($this->first_name)){
				$this->error_array['first_name'] = "First name field cannot be empty";
				$flag=false;
			}
			if(empty($this->last_name)){
				$this->error_array['last_name'] = "Last name field cannot be empty";
				$flag=false;
			}
			if(empty($this->eaa_number)){
				$this->error_array['eaa_number'] = "EAA number field cannot be empty";
				$flag=false;
			}
			if(empty($this->email)){
				$this->error_array['email'] = "Email field cannot be empty";	
				$flag=false;
			} else if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
				$this->error_array['email'] = "A valid email address is required";	
				$flag=false;
			}
			if(empty($this->aircraft_type)){
				$this->error_array['aircraft'] = "Aircraft field cannot be empty";
				$flag=false;
			}
			if($flag==true){
				$this->id = $this->conn->real_escape_string($this->id);
				$this->first_name = $this->conn->real_escape_string($this->first_name);
				$this->last_name = $this->conn->real_escape_string($this->last_name);
				$this->middle_initial = $this->conn->real_escape_string($this->middle_initial);
				$this->eaa_number = $this->conn->real_escape_string($this->eaa_number);
				$this->eaa_chapter = $this->conn->real_escape_string($this->eaa_chapter);
				$this->aircraft_type = $this->conn->real_escape_string($this->aircraft_type);
				$this->email = $this->conn->real_escape_string($this->email);
			}
			return $flag;
		}
		
		public function insertMySql (){
			if( $this->readyForMySQL() ){
				$this->conn->query("INSERT INTO `pilots` (`last_name`, `first_name`, `middle_initial`, `eaa_number`, `eaa_chapter`, `aircraft_type`,`email`) VALUES ('" . $this->getLastName() . "', '" . $this->getFirstName() . "', '" . $this->getMiddleInitial() . "', '" . $this->getEAANumber() . "', '" . $this->getEAAChapter() . "', '" . $this->getAircraftType() . "', '" . $this->getEmail() . "')");
				$this->setId( $this->conn->insert_id );
				return true;
			} else {
				return false;
			}
		}
		
		public function updateMySQL (){
			if( $this->readyForMySQL() ){
				$this->conn->query("UPDATE `pilots` SET `last_name`='" . $this->last_name . "', `first_name`='" . $this->first_name . "', `middle_initial`='" . $this->middle_initial . "', `eaa_number`='" . $this->eaa_number . "', `eaa_chapter`=" . $this->eaa_chapter . ", `aircraft_type`='" . $this->aircraft_type . "', `email`='" . $this->email . "' WHERE `id`='" . $this->id . "'");
				$this->conn->query("INSERT INTO `recent` (`id`, `time`, `message`, `fa_icon`) VALUES (NULL, '" . time() . "', 'Pilot was updated', 'fa-upload')");
				return true;
			} else {
				return false;
			}
		}
		
		//Errors
		public function error($error_name){
			if(isset($this->error_array[$error_name])){
				echo $this->dangerAlert( $this->error_array[$error_name] );
			}
		}
	}
	
	class YoungEagle extends Person {
		protected $assigned_num,$reg_time,$fly_time,$gender,$city,$state,$birth,$participation,$telephone,$flew,$pilot,$noshow,$error_array;

		public function __construct ($conn,$youngeagle_array=null){
			parent::__construct($conn);
			if(isset($youngeagle_array)){
				$this->setId($youngeagle_array['id']);
				$this->setAssignedNum($youngeagle_array['assigned_num']);
				$this->setRegTime($youngeagle_array['reg_time']);
				$this->setFlyTime($youngeagle_array['fly_time']);
				$this->setLastName($youngeagle_array['last_name']);
				$this->setFirstName($youngeagle_array['first_name']);
				$this->setMiddleInitial($youngeagle_array['middle_initial']);
				$this->setGender($youngeagle_array['gender']);
				$this->setCity($youngeagle_array['city']);
				$this->setState($youngeagle_array['state']);
				$this->setBirth($youngeagle_array['birth']);
				$this->setParticipation($youngeagle_array['participation']);
				$this->setTelephone($youngeagle_array['telephone']);
				$this->setEmail($youngeagle_array['email']);
				$this->flew = $youngeagle_array['flew'];
				$this->setPilot($youngeagle_array['pilot']);
				$this->noshow = $youngeagle_array['noshow'];
				$this->error_array = array();
			}
		}

		public function load ($id){
			//Clean the pilot_id
			$this->id = $this->conn->real_escape_string($id);
			//Query for the pilot info
			$youngeagle = $this->conn->query("SELECT * FROM `registrants` WHERE `id`='" . $this->getId() . "'");
			$youngeagle_array = $youngeagle->fetch_assoc();
			$this->setAssignedNum($youngeagle_array['assigned_num']);
			$this->setRegTime($youngeagle_array['reg_time']);
			$this->setFlyTime($youngeagle_array['fly_time']);
			$this->setLastName($youngeagle_array['last_name']);
			$this->setFirstName($youngeagle_array['first_name']);
			$this->setMiddleInitial($youngeagle_array['middle_initial']);
			$this->setGender($youngeagle_array['gender']);
			$this->setCity($youngeagle_array['city']);
			$this->setState($youngeagle_array['state']);
			$this->setBirth($youngeagle_array['birth']);
			$this->setParticipation($youngeagle_array['participation']);
			$this->setTelephone($youngeagle_array['telephone']);
			$this->setEmail($youngeagle_array['email']);
			$this->flew = $youngeagle_array['flew'];
			$this->setPilot($youngeagle_array['pilot']);
			$this->noshow = $youngeagle_array['noshow'];
			$this->error_array = array();
		}

		public function create($assigned_num,$reg_time,$last_name,$first_name,$mi,$gender,$city,$state,$birth,$participation,$telephone,$email,$flew=0,$fly_time=NULL,$pilot=NULL,$noshow=0){
			//Clean the pilot_id
			$this->id = NULL;
			//Query for the pilot info
			$this->assigned_num = $assigned_num;
			$this->reg_time = $reg_time;
			$this->fly_time = $fly_time;
			$this->last_name = $last_name;
			$this->first_name = $first_name;
			$this->mi = $mi;
			$this->gender = $gender;
			$this->city = $city;
			$this->state = $state;
			$this->birth = $birth;
			$this->participation = $participation;
			$this->telephone = $telephone;
			$this->email = $email;
			$this->flew = $flew;
			$this->pilot = $pilot;
			$this->noshow = $nowshow;
			$this->error_array = array();
		}
		
		public function delete(){
			$this->conn->query("DELETE FROM `registrants` WHERE `id` = '" . $this->getId() . "'");
			$this->conn->query("DELETE FROM `rally_registrants` WHERE `registrant_id` = '" . $this->getId() . "'");
		}
		
		public function setAssignedNum($assigned_num){
			$this->assigned_num = $assigned_num;
		}
		
		public function getAssignedNum(){
			return $this->assigned_num;
		}
		
		public function setRegTime($reg_time){
			$this->reg_time = $reg_time;
		}
		
		public function getRegTime($formatted=false){
			if($formatted=true){
				if ($this->reg_time >= strtotime("today 00:00")) {
					return "Today at " . date("g:i A", $this->reg_time);
				} else if ($this->reg_time >= strtotime("yesterday 00:00")) {
					return "Yesterday at " . date("g:i A", $this->reg_time);
				} else if ($this->reg_time >= strtotime("-6 day 00:00")) {
					return date("l \\a\\t g:i A", $this->reg_time);
				} else { 
					return date("M j, Y", $this->reg_time);
				}
			}
			return $this->reg_time;
		}

		public function setFlyTime($fly_time){
			$this->fly_time = $fly_time;
		}
		
		public function getFlyTime($formatted=false){
			if($formatted=true){
				if ($this->fly_time >= strtotime("today 00:00")) {
					return "Today at " . date("g:i A", $this->fly_time);
				} else if ($this->fly_time >= strtotime("yesterday 00:00")) {
					return "Yesterday at " . date("g:i A", $this->fly_time);
				} else if ($this->fly_time >= strtotime("-6 day 00:00")) {
					return date("l \\a\\t g:i A", $this->fly_time);
				} else { 
					return date("M j, Y", $this->fly_time);
				}
			}
			return $this->fly_time;
		}
		
		public function setGender($gender){
			$this->gender = $gender;
		}
		
		public function getGender(){
			return $this->gender;
		}
		
		public function setCity($city){
			$this->city = $city;
		}
		
		public function getCity(){
			return $this->city;
		}
		
		public function setState($state){
			$this->state = $state;
		}
		
		public function getState(){
			return $this->state;
		}
		
		public function setBirth($birth){
			$this->birth = $birth;
		}
		
		public function getBirth(){
			return $this->birth;
		}
		
		public function getAge(){
			$age = 0;
			$dob = strtotime($this->getBirth());
			while( time() > $dob = strtotime('+1 year', $dob))
			{
					++$age;
			}
			return $age;
		}
		
		public function setParticipation($participation){
			$this->participation = $participation;
		}
		
		public function getParticipation(){
			return $this->participation;
		}
		
		public function setTelephone($telephone){
			$this->telephone = $telephone;
		}
		
		public function getTelephone(){
			return $this->telephone;
		}
		
		public function setPilot($pilot_id){
			$this->pilot = $pilot_id;
		}
		
		public function getPilot(){
			return $this->pilot;
		}
		
		public function setFlew($flew){
			$this->flew=$flew;
		}
		
		public function getFlew(){
			return $this->flew;
		}
		
		public function setNoshow($noshow){
			$this->noshow = $noshow;
		}
		
		public function getNoshow(){
			return $this->noshow;
		}
		
		//returns false if there are error flags
		protected function readyForMySQL (){
			$flag = true;
			if(empty($this->getFirstName())){
				$this->error_array['first_name'] = "First name field cannot be empty";
				$flag=false;
			}
			if(empty($this->getLastName())){
				$this->error_array['last_name'] = "Last name field cannot be empty";
				$flag=false;
			}
			if(empty($this->getGender())){
				$this->error_array['gender'] = "Gender field cannot be empty";
				$flag=false;
			}
			if(empty($this->getCity())){
				$this->error_array['city'] = "City field cannot be empty";
				$flag=false;
			}
			if(empty($this->getState())){
				$this->error_array['state'] = "State field cannot be empty";
				$flag=false;
			}
			if(empty($this->getAssignedNum())){
				$this->error_array['assigned_num'] = "Assigned number field cannot be empty";
				$flag=false;
			}
			if(empty($this->getBirth())){
				$this->error_array['birth'] = "Birthday field cannot be empty";
				$flag=false;
			}
			if($this->getParticipation()!=0 && $this->getParticipation()!=1){
				$this->error_array['participation'] = "Previous participation field cannot be empty";
				$flag=false;
			}
			if(empty($this->getTelephone())){
				$this->error_array['telephone'] = "Telephone field cannot be empty";
				$flag=false;
			}
			if(empty($this->getEmail())){
				$this->error_array['email'] = "Email field cannot be empty";	
				$flag=false;
			} else if(!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)){
				$this->error_array['email'] = "A valid email address is required";	
				$flag=false;
			}
			if($this->getAge()>17 || $this->getAge()<8){
				$this->error_array['age'] = "Child must be between the ages of 8 and 17.";	
				$flag=false;
			}
			
			if($flag==true){
				$this->setAssignedNum( $this->conn->real_escape_string( $this->getAssignedNum() ));
				$this->setFirstName( $this->conn->real_escape_string( $this->getFirstName() ));
				$this->setLastName( $this->conn->real_escape_string( $this->getLastName() ));
				$this->setMiddleInitial( $this->conn->real_escape_string( $this->getMiddleInitial() ));
				//left off here
				$this->setBirth( $this->conn->real_escape_string( $this->getBirth() ));
				$this->email = $this->conn->real_escape_string($this->email);
			} else {
					
			}
			return $flag;
		}
		
		public function insertMySql (){
			if( $this->readyForMySQL() ){
				$this->conn->query("INSERT INTO `registrants` (`reg_time`,`assigned_num`,`last_name`,`first_name`,`middle_initial`,`gender`,`city`,`state`,`birth`,`participation`,`telephone`,`email`) VALUES ('" . $this->getRegTime() . "', '" . $this->getAssignedNum() . "', '" . $this->getLastName() . "', '" . $this->getFirstName() . "','" . $this->getMiddleInitial() . "','" . $this->getGender() . "', '" . $this->getCity() . "', '" . $this->getState() . "', '" . $this->getBirth() . "', '" . $this->getParticipation() . "', '" . $this->getTelephone() . "', '" . $this->getEmail() . "')");
				$this->setId( $this->conn->insert_id );
				return true;
			} else {
				return false;
			}
		}
		
		public function updateMySQL (){
			if( $this->readyForMySQL() ){
				$this->conn->query("UPDATE `registrants` SET `assigned_num`='" . $this->getAssignedNum() . "', `last_name`='" . $this->getLastName() . "', `first_name`='" . $this->getFirstName() . "',`middle_initial`='" . $this->getMiddleInitial() . "',`gender`='" . $this->getGender() . "', `city`='" . $this->getCity() . "', `state`='" . $this->getState() . "', `birth`='" . $this->getBirth() . "', `participation`='" . $this->getParticipation() . "', `telephone`='" . $this->getTelephone() . "', `email`='" . $this->getEmail() . "' WHERE `id`=" . $this->getId());
				return true;
			} else {
				return false;
			}
		}
		
		//Errors
		public function error($error_name){
			if(isset($this->error_array[$error_name])){
				echo $this->dangerAlert( $this->error_array[$error_name] );
			}
		}
	}
	
	class YoungEagleList {
		private $flownarray,$notflownarray,$noshowarray;
		public function __construct (){
			$numargs = func_num_args();
    		$arg_list = func_get_args();
			$this->flownarray = array();
			$this->notflownarray = array();
			$this->noshowarray = array();
    		for ($i = 0; $i < $numargs; $i++) {
				$this->add($arg_list[$i]);
    		}
		}
		
		public function add($youngeagle) {
			if($youngeagle->getNoShow() == true)
        		$this->noshowarray[]=$youngeagle;
			else if($youngeagle->getFlew() == false)
				$this->notflownarray[]=$youngeagle;
			else
				$this->flownarray[]=$youngeagle;
		}
		
		public function getYoungEagles (){
			$temp = array_merge($this->flownarray,$this->noshowarray,$this->notflownarray);
			return $temp;
		}
		
		public function getNotFlown (){
			return $this->notflownarray;	
		}
		
		public function getFlown (){
			return $this->flownarray;
		}
		
		public function getNoShow (){
			return $this->noshowarray;
		}
		
		public function numberOfYoungEagles (){
			return count($this->flownarray) + count($this->notflownarray) + count($this->noshowarray);
		}
		
		public function numberFlown (){
			return count($this->flownarray);
		}
		
		public function numberNotFlown (){
			return count($this->notflownarray);
		}
	}
	
	class PilotList {
		private $pilots;
		public function __construct (){
			$numargs = func_num_args();
    		$arg_list = func_get_args();
			$this->pilots = array();
    		for ($i = 0; $i < $numargs; $i++) {
				$this->add($arg_list[$i]);
    		}
		}
		
		public function add($pilot) {
        	$this->pilots[]=$pilot;
		}
		
		public function getPilots (){
			return $this->pilots;
		}
		
		public function getNumberOfPilots (){
			return count($this->pilots);
		}
	}