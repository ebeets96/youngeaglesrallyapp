<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$admin = $session->getAdmin();
	function are_empty(){
		foreach(func_get_args() as $arg) {
			if(empty($arg))
				return true;
		}
		return false;
	}
	$submitted = isset($_POST['updated']);
	if($submitted){
		$rally->setAirport($_POST['airport']);
		$rally->setTitle($_POST['title']);
		$rally->setRallyDate($_POST['rally_date']);
		$rally->updateMySQL();
	}
	$admin_submitted =isset($_POST['new_admin']);
	if($admin_submitted){
		$email = $conn->real_escape_string($_POST['admin_email']);
		$password = $conn->real_escape_string($_POST['admin_password']);
		$confirm = $conn->real_escape_string($_POST['confirm_password']);
		$new_admin = new Admin($conn);
		$new_admin->create($_POST);
		$conn->query("INSERT INTO `admins` (`id`, `email`, `password`, `can_delete`) VALUES (NULL, '$email', '$password', '1')");
	}
	
	if(isset($_GET['delete'])){
		$adminid = $_GET['delete'];
		if($adminid != $_SESSION['SESS_NAME']) {
			$admin = $conn->query("DELETE FROM `admins` WHERE `id`='$adminid'");
			$admin_success = true;
			$message = "Admin was successfully deleted.";
		} else {
			$admin_failure = true;
			$message = "You can not delete yourself from being an admin " . $_SESSION['SESS_NAME'];
		}
	}
	$admins = $conn->query("SELECT * FROM `admins`");
	$session->outputHeader("Settings");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Settings</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<form action="settings.php" method="post" role="form">
            <div class="row">
				<?php
					if(!empty($success)){
				?>
				<div class="alert alert-success">
					Settings were updated.
				</div>
				<?php
					} else if(!empty($failure)){
				?>
					<div class="alert alert-danger">
					<?php echo $message;?>
					</div>
				<?php
					}
				?>
                <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            General Settings
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
										<?php
											$rally->error("title");
										?>
										<div class="form-group">
                                            <label>Website Title</label>
											<input name="title" class="form-control" placeholder="Young Eagles Rally Database Manager" id="number" value="<?php echo $rally->getTitle(); ?>">
                                        </div>
										<?php
											$rally->error("airport_name");
										?>
										<div class="form-group">
                                            <label>Rally Location (Airport Name)</label>
											<input name="airport" class="form-control" placeholder="Airport" id="number" value="<?php echo $rally->getAirport(); ?>">
                                        </div>
										<?php
											$rally->error("rally_date");
										?>
										<div class="form-group">
                                            <label>Rally Date</label>
											<input name="rally_date" class="form-control" placeholder="mm/dd/yyyy" id="number" value="<?php echo $rally->getRallyDate(); ?>">
                                        </div>
								</div>
							</div>
						</div>
					</div>
				</div>
                <!-- /.col-lg-12 -->
            </div>
			</form>
			
			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Admins</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<form action="settings.php" name="new_admin" method="post" role="form" autocomplete="off">
            <div class="row">
				<?php
					if($admin_failure==true){
				?>
					<div class="alert alert-danger">
					<?php echo $message;?>
					</div>
				<?php
					}
				?>
				<?php
					if($admin_success==true){
				?>
					<div class="alert alert-success">
					<?php echo $message;?>
					</div>
				<?php
					}
				?>
                <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            Create Admin
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
									<?php
										if(empty($name)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Name is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>First Name</label>
											<input name="first_name" class="form-control" placeholder="Name" value="<?php echo $name; ?>">
                                        </div>
										<?php
										if(empty($name)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Name is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Middle Initial</label>
											<input name="first_name" class="form-control" placeholder="Name" value="<?php echo $name; ?>">
                                        </div>
										<?php
										if(empty($name)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Name is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Last Name</label>
											<input name="first_name" class="form-control" placeholder="Name" value="<?php echo $name; ?>">
                                        </div>
										<?php
										if(empty($email)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Email address is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Email</label>
											<input name="admin_email" class="form-control" placeholder="Email Address" id="number" value="<?php echo $email; ?>">
                                        </div>
										<?php
										if(empty($password)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Password is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Password</label>
											<input type="password" name="admin_password" placeholder="Password" class="form-control" id="number">
                                        </div>
										<?php
										if(empty($confirm)&&$admin_submitted){
										?>
										<div class="alert alert-danger">
                                			Confirm password is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Confirm Password</label>
											<input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" id="number">
                                        </div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<input type="submit" name="new_admin" value="Add Admin" class="btn btn-success btn-block">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            Admins
                        </div>
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
											<th width="60">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											while($admin= $admins->fetch_assoc()){
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . $admin['email'] . " ";
												echo "</td><td><a href=\"?delete=" . $admin['id'] . "\" class=\"btn btn-danger btn-large btn-block\"";
												if(!$admin['can_delete']==1){
													echo " disabled";
												}
												echo "><i class=\"fa fa-trash\"></i></button></td>";
												$i++;
											}
										?>
                                    </tbody>
                                </table>
                            </div>
                        <!-- /.panel-body -->
                    	</div>
					</div>
                    <!-- /.panel -->
                </div>
			</div>
			</form>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

<?php
	$footer_template = new Template("includes/footer.inc.tpl");
	echo $footer_template->output();
?>