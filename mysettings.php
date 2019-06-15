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
		$admin->setSetting("ye-x",$_POST['ye-x']);
		$admin->setSetting("ye-y",$_POST['ye-y']);
		$admin->setSetting("date-x",$_POST['date-x']);
		$admin->setSetting("date-y",$_POST['date-y']);
		$admin->setSetting("airplane-x",$_POST['airplane-x']);
		$admin->setSetting("airplane-y",$_POST['airplane-y']);
		$admin->setSetting("airport-x",$_POST['airport-x']);
		$admin->setSetting("airport-y",$_POST['airport-y']);
		$admin->updateMySQL();
	} else if(isset($_POST['reset'])){
		$admin->resetSettings();
		$admin->updateMySQL();
	}
	$admins = $conn->query("SELECT * FROM `admins`");
	$session->outputPrimaryHeader("Settings");
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
                            Print Settings
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
										<?php
											$rally->error("ye");
										?>
										<div class="form-group">
                                            <label>Young Eagle Name Print Location (x,y)</label>
											<div class="input-group">
												<input name="ye-x" class="form-control" placeholder="X" id="number" value="<?php echo $admin->getSetting("ye-x"); ?>">
												<span class="input-group-addon">mm,</span>
												<input name="ye-y" class="form-control" placeholder="Y" id="number" value="<?php echo $admin->getSetting("ye-y"); ?>">
												<span class="input-group-addon" id="basic-addon2">mm</span>
											</div>
                                        </div>
										<?php
											$rally->error("date");
										?>
                                        <div class="form-group">
                                            <label>Date Print Location (x,y)</label>
											<div class="input-group">
												<input name="date-x" class="form-control" placeholder="X" id="number" value="<?php echo $admin->getSetting("date-x"); ?>">
												<span class="input-group-addon">mm,</span>
												<input name="date-y" class="form-control" placeholder="Y" id="number" value="<?php echo $admin->getSetting("date-y"); ?>">
												<span class="input-group-addon">mm</span>
											</div>
                                        </div>
										<?php
											$rally->error("airplane");
										?>
                                        <div class="form-group">
                                            <label>Airplane Print Location (x,y)</label>
											<div class="input-group">
												<input name="airplane-x" class="form-control" placeholder="X" id="number" value="<?php echo $admin->getSetting("airplane-x"); ?>">
												<span class="input-group-addon">mm,</span>
												<input name="airplane-y" class="form-control" placeholder="Y" id="number" value="<?php echo $admin->getSetting("airplane-y"); ?>">
												<span class="input-group-addon">mm</span>
											</div>
                                        </div>
										<?php
											$rally->error("airport");
										?>
										<div class="form-group">
                                            <label>Airport Print Location (x,y)</label>
											<div class="input-group">
												<input name="airport-x" class="form-control" placeholder="X" id="number" value="<?php echo $admin->getSetting("airport-x"); ?>">
												<span class="input-group-addon">mm,</span>
												<input name="airport-y" class="form-control" placeholder="Y" id="number" value="<?php echo $admin->getSetting("airport-y"); ?>">
												<span class="input-group-addon">mm</span>
											</div>
                                        </div>
									</div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
					
                </div>
			</div>
			<div class="row">
					<div class="col-sm-3">
						<input type="submit" name="updated" value="Update Settings" class="btn btn-success btn-block">
					</div>
					<div class="col-sm-3">
						<input type="submit" name="reset" value="Restore Defaults" class="btn btn-warning btn-block">
					</div>
			</div>
                <!-- /.col-lg-12 -->
			</form>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

<?php
	$footer_template = new Template("includes/footer.inc.tpl");
	echo $footer_template->output();
?>