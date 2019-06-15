<?php
	if(!isset($_GET['go']))
		die();
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$males = array("Trey Baillie","Howard Laskowski","William Vassallo","Luciano Rada","Lonnie Swink","Lamar Miramontes","Tyler Creagh","Otha Shupp","Andreas Mcgehee","Christoper Allsop");
	$females = array("Karly Hershman","Lennie Peet","Delisa Gajewski","Camila Bassett","Penney Atwood","Sabrina Osterberg","Melonie Ludlow","Kittie Auyeung","Jacqueline Minor","Margaret Hurla");
	$cities = array("Sharon Hill","Anchor Point","Osage City","New Salem","Koyuk","Erda","Delta","Rio Verde","South Jacksonville","Flossmoor","Pearl Beach","Encantada","Cleves","College Park","SeaTac","Swanville","Kenney","Finley","Elysian","Cobbtown","Lake Almanor Peninsula","Bluffs","Fort Kent","Royal Center","Guntown");
	$states = array("Illinois","Wisconsin");
	$youngeagle = array();
	for($i=1;$i<=10;$i++){
		$variables['reg_time'] = time();
		$variables['id'] = NULL;
		$variables['fly_time'] = NULL;
		$variables['flew'] = false;
		$variables['pilot'] = NULL;
		$variables['noshow'] = false;
		$variables['assigned_num'] = $i;
		$name = explode(" ",$males[$i-1]);
		$variables['last_name'] = $name[1];
		$variables['first_name'] = $name[0];
		$variables['middle_initial'] = "";
		$variables['gender'] = "male";
		$variables['city'] = $cities[$i];
		$variables['state'] = $states[rand(0,1)];
		$variables['birth'] = date("m/d/y",mt_rand(883612800,1136073600));
		$variables['participation'] = rand(0,1);
		$variables['telephone'] = "262492" . mt_rand(4920000,4929999);
		$variables['email'] = "test@test.com";
		$young_eagle = new YoungEagle($conn,$variables);
		$success = $young_eagle->insertMySql();
		$rally->addYoungEagle($young_eagle);
		echo $young_eagle->getName() . " registered for a flight.<br>";
		$variables['assigned_num'] = 10+$i;
		$name = explode(" ",$females[$i-1]);
		$variables['last_name'] = $name[1];
		$variables['first_name'] = $name[0];
		$variables['middle_initial'] = "";
		$variables['gender'] = "female";
		$variables['city'] = $cities[10+$i];
		$variables['state'] = $states[rand(0,1)];
		$variables['birth'] = date("m/d/y",mt_rand(883612800,1136073600));
		$variables['participation'] = rand(0,1);
		$variables['telephone'] = "262492" . mt_rand(4920000,4929999);
		$variables['email'] = "test@test.com";
		$young_eagle = new YoungEagle($conn,$variables);
		$success = $young_eagle->insertMySql();
		$rally->addYoungEagle($young_eagle);
		echo $young_eagle->getName() . " registered for a flight.<br>";
	}
	
	$pilots = array("Kandi Lachapelle","Elijah Wiggin","Pauletta Mousseau","Alexander Stocks","Maple Blakeman","Anthony Ziemann","Era Grinder","Gretta Maclaren","Jerrell Avendano","Estelle Mazzei");
	$airplanes = array("Cessna C172","Cessna C152","Piper Arrow","Piper Archer","Piper J-3 Cub");
	for($i=1;$i<=5;$i++){
		$name = explode(" ",$pilots[$i-1]);
		$variables['last_name'] = $name[1];
		$variables['first_name'] = $name[0];
		$variables['middle_initial'] = "";
		$variables['eaa_number'] = mt_rand(123555,605782);
		$variables['eaa_chapter'] = mt_rand(11,2222);
		$variables['aircraft_type'] = $airplanes[$i-1];
		$variables['email'] = "test@test.com";
		$pilot = new Pilot($conn,$variables);
		$pilot->insertMySql();
		$rally->addPilot($pilot);
		echo "Pilot ". $pilot->getName() . " was inserted<br>";
	}
	$conn->close();
?>