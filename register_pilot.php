<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	if(isset($_POST['submitted'])){
		$new_pilot = new Pilot($conn,$_POST);
		if($new_pilot->insertMySql()){
			$rally->addPilot($new_pilot);
			$success = true;
			$new_pilot = new Pilot($conn);
		}
	} else {
		$new_pilot = new Pilot($conn);
	}
	$session->outputHeader("Register Pilot");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Register a Pilot</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
				<?php
					if(isset($_POST['submitted'])&&$success){
				?>
				<div class="alert alert-success">
					Pilot was successfully added.
				</div>
				<?php
					}
				?>
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Register a Pilot
                        </div>
                        <div class="panel-body">
                            <div class="row">
								<form role="form" action="register_pilot.php" method="post">
                                <div class="col-lg-6">
										<?php
											$new_pilot->error("last_name");
										?>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input name="last_name" class="form-control" placeholder="Enter last name" value="<?php echo $new_pilot->getLastName(); ?>">
                                        </div>
										<?php
											$new_pilot->error("first_name");
										?>
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input name="first_name" class="form-control" placeholder="Enter first name" value="<?php echo $new_pilot->getFirstName(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Middle Initial</label>
                                            <input name="middle_initial" class="form-control" placeholder="MI" value="<?php echo $new_pilot->getMiddleInitial(); ?>">
                                        </div>
										<?php
											$new_pilot->error("eaa_number");
										?>
										<div class="form-group">
                                            <label>EAA Number</label>
                                            <input name="eaa_number" class="form-control" placeholder="Enter EAA Number" value="<?php echo $new_pilot->getEAANumber(); ?>">
                                        </div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
                                            <label>EAA Chapter</label>
                                            <input name="eaa_chapter" class="form-control" placeholder="Enter EAA Chapter"  value="<?php echo $new_pilot->getEAAChapter(); ?>">
                                        </div>
										<?php
											$new_pilot->error("aircraft");
										?>
										<div class="form-group">
                                            <label>Aircraft Type</label>
                                            <input name="aircraft_type" class="form-control" placeholder="Enter Aircraft Type"  value="<?php echo $new_pilot->getAircraftType(); ?>">
                                        </div>
										<?php
											$new_pilot->error("email");
										?>
										<div class="form-group">
                                            <label>Email Address</label>
                                            <input name="email" class="form-control" placeholder="Enter email address" value="<?php echo $new_pilot->getEmail(); ?>">
                                        </div>
                                        <input type="submit" name="submitted" value="Register Pilot" class="btn btn-success center-block">
                                    
                                </div>
								</form>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

<?php
	$footer_template = new Template("includes/footer.inc.tpl");
	echo $footer_template->output();
?>
