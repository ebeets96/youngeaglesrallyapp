<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	
	date_default_timezone_set("America/Chicago");
	$submitted = isset($_POST['updated']);
	$ye = new YoungEagle($conn);
	if(isset($_REQUEST['id'])){
		$id=$_REQUEST['id'];
		$ye->load($id);
	} else {
		header("Location:database.php");
	}
	if($submitted){
		$ye->setAssignedNum($_POST['number']);
		$ye->setLastName($_POST['last_name']);
		$ye->setFirstName($_POST['first_name']);
		$ye->setMiddleInitial($_POST['middle_initial']);
		$ye->setGender($_POST['gender']);
		$ye->setCity($_POST['city']);
		$ye->setState($_POST['state']);
		$ye->setBirth($_POST['birthday']);
		$ye->setParticipation($_POST['previous_participation']);
		$ye->setTelephone($_POST['phone']);
		$ye->setEmail($_POST['email']);
		if($ye->updateMySQL())
			header("Location:database.php");
	}
	$session->outputHeader("Edit Young Eagle");
?>

<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Edit a Young Eagle</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
				<?php
					$ye->error("age");
				?>
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Edit a Young Eagle
                        </div>
                        <div class="panel-body">
                            <div class="row">
								<form action="edit.php" method="post" role="form">
								<input type="hidden" name="id" value="<?php echo $ye->getId(); ?>">
                                <div class="col-lg-6">
										<?php
											$ye->error("assigned_num");
										?>
										<div class="form-group">
                                            <label>Assigned Number</label>
                                            <input name="number" class="form-control" id="number" value="<?php echo $ye->getAssignedNum(); ?>">
                                        </div>
										<?php
											$ye->error("last_name");
										?>
                                        <div class="form-group">
                                            <label>Last Name of Participant</label>
                                            <input name="last_name" class="form-control" id="last_name" placeholder="Enter last name" value="<?php echo $ye->getLastName(); ?>">
                                        </div>
										<?php
											$ye->error("first_name");
										?>
                                        <div class="form-group">
                                            <label>First Name of Participant</label>
                                            <input name="first_name" class="form-control" id="first_name" placeholder="Enter first name" value="<?php echo $ye->getFirstName(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Middle Initial</label>
                                            <input name="middle_initial" value="<?php echo $ye->getMiddleInitial(); ?>" class="form-control" id="middle_initial" placeholder="MI">
                                        </div>
										<?php
											$ye->error("gender");
										?>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control" id="gender">
                                                <option value="male">Male</option>
                                                <option value="female" <?php if($ye->getGender()=="female"){ echo "selected"; }?>>Female</option>
                                            </select>
                                        </div>
										<?php
											$ye->error("city");
										?>
										<div class="form-group">
                                            <label>City</label>
                                            <input name="city" value="<?php echo $ye->getCity(); ?>" class="form-control" id="city" placeholder="City">
                                        </div>
										<?php
											$ye->error("state");
										?>
										<div class="form-group">
										<label>State</label>
                                            <input name="state" value="<?php echo $ye->getState(); ?>" class="form-control" id="state" placeholder="Use two-letter identifier">
                                        </div>
									</div>
									<div class="col-lg-6">
										<?php
											$ye->error("birth");
										?>
										<div class="form-group">
                                            <label>Date of Birth</label>
                                            <input name="birthday" value="<?php echo $ye->getBirth(); ?>" class="form-control" id="birthday" placeholder="mm/dd/yy">
                                        </div>
										<div class="form-group">
                                            <label>Previous Participation</label>
                                            <select name="previous_participation" class="form-control" id="previous_participation">
                                                <option value="0">No</option>
                                                <option value="1" <?php if($ye->getParticipation()=="1"){ echo "selected"; }?>>Yes</option>
                                            </select>
                                        </div>
										<?php
											$ye->error("telephone");
										?>
										<div class="form-group">
                                            <label>Phone Number</label>
                                            <input name="phone" value="<?php echo $ye->getTelephone(); ?>" class="form-control" id="phone" placeholder="555-555-5555">
                                        </div>
										<?php
											$ye->error("email");
										?>
										<div class="form-group">
                                            <label>Parent's Email Address</label>
                                            <input name="email" value="<?php echo $ye->getEmail(); ?>" class="form-control" id="email" placeholder="Enter email address">
                                        </div>
                                        <input type="submit" name="updated" value="Update Young Eagle" class="btn btn-success center-block">
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