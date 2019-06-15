<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	if(!isset($_REQUEST['id'])){
		header("Location: database.php");
	}
	$pilot = new Pilot($conn);
	$pilot->load($_REQUEST['id']);
	if(isset($_POST['update'])){
		$pilot->setName($_POST['first_name'],$_POST['last_name'],$_POST['middle_initial']);
		$pilot->setEAANumber($_POST['eaa_number']);
		$pilot->setEAAChapter($_POST['eaa_chapter']);
		$pilot->setAircraftType($_POST['aircraft']);
		$pilot->setEmail($_POST['email']);
		if($pilot->updateMySQL())
			header("Location: database.php");
	}
	$session->outputHeader("Edit Pilot");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Register a Pilot</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
				<!-- Message -->
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Register a Pilot
                        </div>
                        <div class="panel-body">
                            <div class="row">
								<form role="form" action="edit_pilot.php?id=<?php echo $pilot->getId(); ?>" method="post">
                                <div class="col-lg-6">
										<?php
											$pilot->error("last_name");
										?>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input name="last_name" class="form-control" placeholder="Enter last name" value="<?php echo $pilot->getLastName(); ?>">
                                        </div>
										<?php
											$pilot->error("first_name");
										?>
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input name="first_name" class="form-control" placeholder="Enter first name" value="<?php echo $pilot->getFirstName(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Middle Initial</label>
                                            <input name="middle_initial" class="form-control" placeholder="MI" value="<?php echo $pilot->getMiddleInitial(); ?>">
                                        </div>
										<?php
											$pilot->error("eaa_number");
										?>
										<div class="form-group">
                                            <label>EAA Number</label>
                                            <input name="eaa_number" class="form-control" placeholder="Enter EAA Number" value="<?php echo $pilot->getEAANumber(); ?>">
                                        </div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
                                            <label>EAA Chapter</label>
                                            <input name="eaa_chapter" class="form-control" placeholder="Enter EAA Chapter"  value="<?php echo $pilot->getEAAChapter(); ?>">
                                        </div>
										<?php
											$pilot->error("aircraft");
										?>
										<div class="form-group">
                                            <label>Aircraft Type</label>
                                            <input name="aircraft" class="form-control" placeholder="Enter Aircraft Type"  value="<?php echo $pilot->getAircraftType(); ?>">
                                        </div>
										<?php
											$pilot->error("email");
										?>
										<div class="form-group">
                                            <label>Email Address</label>
                                            <input name="email" class="form-control" placeholder="Enter email address" value="<?php echo $pilot->getEmail(); ?>">
                                        </div>
										<div class="row">
											<div class="col-sm-6">
												<input type="submit" name="update" value="Update Pilot" class="btn btn-success btn-block">
											</div>
											<div class="col-sm-6">
												<a href="database.php" class="btn btn-danger btn-block">Cancel</a>
											</div>
                                    	</div>
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