<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	date_default_timezone_set("America/Chicago");
	$submitted = isset($_POST['submitted']);
	if($submitted){
		echo "Set reg time<br>";
		$_POST['id'] = NULL;
		$_POST['reg_time'] = time();
		$_POST['fly_time'] = NULL;
		$_POST['flew'] = false;
		$_POST['pilot'] = NULL;
		$_POST['noshow'] = false;
		$young_eagle = new YoungEagle($conn,$_POST);
		$success = $young_eagle->insertMySql();
		if($success && $rally->addYoungEagle($young_eagle)){
			$young_eagle = new YoungEagle($conn);	
			$submitted=false;		
		}
	} else {
		$young_eagle = new YoungEagle($conn);
	}
	$session->outputHeader("Register Young Eagle");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Register a Young Eagle</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
				<?php
					if(!empty($success)){
				?>
				<div class="alert alert-success">
					Child was successfully added.
				</div>
				<?php
					}
				?>
				<?php
					if($submitted && ($young_eagle->getAge()<8 || $young_eagle->getAge()>17)){
				?>
				<div class="alert alert-danger">
					Child's birthday did not meet the 8-17 requirement.
				</div>
				<?php
					}
				?>
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Register a Young Eagle
                        </div>
                        <div class="panel-body">
                            <div class="row">
								<form action="register.php" method="post" role="form">
                                <div class="col-lg-6">
										<?php
										if($submitted && empty($young_eagle->getAssignedNum())){
										?>
										<div class="alert alert-danger">
                                			Assigned number is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Assigned Number</label>
                                            <input name="assigned_num" class="form-control" id="number" value="<?php echo $young_eagle->getAssignedNum(); ?>" placeholder="Assigned Number">
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getLastName())){
										?>
										<div class="alert alert-danger">
                                			Last name is required.
                            			</div>
										<?php
										}
										?>
                                        <div class="form-group">
                                            <label>Last Name of Participant</label>
                                            <input name="last_name" class="form-control" id="last_name" placeholder="Enter last name" value="<?php echo $young_eagle->getLastName(); ?>">
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getFirstName())){
										?>
										<div class="alert alert-danger">
                                			First name is required.
                            			</div>
										<?php
										}
										?>
                                        <div class="form-group">
                                            <label>First Name of Participant</label>
                                            <input name="first_name" class="form-control" id="first_name" placeholder="Enter first name" value="<?php echo $young_eagle->getFirstName(); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Middle Initial</label>
                                            <input name="middle_initial" value="<?php echo $young_eagle->getMiddleInitial(); ?>" class="form-control" id="middle_initial" placeholder="MI">
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control" id="gender">
                                                <option value="male">Male</option>
                                                <option value="female" <?php if($young_eagle->getGender()=="female"){ echo "selected"; }?>>Female</option>
                                            </select>
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getCity())){
										?>
										<div class="alert alert-danger">
                                			City is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>City</label>
                                            <input name="city" value="<?php echo $young_eagle->getCity(); ?>" class="form-control" id="city" placeholder="City">
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getState())){
										?>
										<div class="alert alert-danger">
                                			State is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
										<label>State</label>
                                            <input name="state" value="<?php echo $young_eagle->getState(); ?>" class="form-control" id="state" placeholder="Use two-letter identifier">
                                        </div>
									</div>
									<div class="col-lg-6">
										<?php
										if($submitted && empty($young_eagle->getBirth())){
										?>
										<div class="alert alert-danger">
                                			Date of birth is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Date of Birth</label>
                                            <input name="birth" value="<?php echo $young_eagle->getBirth(); ?>" class="form-control" id="birthday" placeholder="mm/dd/yy">
                                        </div>
										<div class="form-group">
                                            <label>Previous Participation</label>
                                            <select name="participation" class="form-control" id="previous_participation">
                                                <option value="0">No</option>
                                                <option value="1" <?php if($young_eagle->getParticipation()=="1"){ echo "selected"; }?>>Yes</option>
                                            </select>
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getTelephone())){
										?>
										<div class="alert alert-danger">
                                			Phone number is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Phone Number</label>
                                            <input name="telephone" value="<?php echo $young_eagle->getTelephone(); ?>" class="form-control" id="phone" placeholder="555-555-5555">
                                        </div>
										<?php
										if($submitted && empty($young_eagle->getEmail())){
										?>
										<div class="alert alert-danger">
                                			Parent's email address is required.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
                                            <label>Parent's Email Address</label>
                                            <input name="email" value="<?php echo $young_eagle->getEmail(); ?>" class="form-control" id="email" placeholder="Enter email address">
                                        </div>
										<?php
										if(empty($_POST['signature'])){
										?>
										<div class="alert alert-danger">
                                			Please verify the parent's signature on the form.
                            			</div>
										<?php
										}
										?>
										<div class="form-group">
											<label class="help-inline">
												<input type="checkbox" <?php if(!empty($_POST['signature'])){ echo "checked"; } ?> name="signature" value="true" class="checkbox-inline"> 
												&nbsp;Check here to confirm that you checked the parent's signature on the registration sheet.
											</label>
                                        </div>
                                        <input type="submit" name="submitted" value="Register Young Eagle" class="btn btn-success center-block">
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
