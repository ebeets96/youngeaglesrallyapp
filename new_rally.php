<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	if(isset($_REQUEST["submitted"])){
		$rally_title = $_REQUEST["title"];
		$rally_location = $_REQUEST["airport"];
		$rally_date = $_REQUEST["rally_date"];	
		$new_rally = new Rally($conn);
		$new_rally->setTitle($rally_title);
		$new_rally->setAirport($rally_location);
		$new_rally->setRallyDate($rally_date);
		$new_rally->insertMySQL();
		$session->getAdmin()->addRally($new_rally);
	}
	$rally = $session->getCurrentRally();
	$session->outputHeader("New Rally");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Create a New Rally</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            New Rally
                        </div>
                        <div class="panel-body">
							<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label>Rally Title</label>
											<input name="title" class="form-control" placeholder="Rally Title" id="number" value="">
										</div>
										<div class="form-group">
											<label>Rally Location (Airport Name)</label>
											<input name="airport" class="form-control" placeholder="Airport" id="number" value="">
										</div>
										<div class="form-group">
											<label>Rally Date</label>
											<input name="rally_date" class="form-control" placeholder="mm/dd/yyyy" id="number" value="">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<input type="submit" name="submitted" value="Create" class="btn btn-success btn-block">
									</div>
									<div class="col-sm-2"></div>
								</div>
							</form>
						</div>
					</div>
				</div>
            </div>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

<?php
	$footer_template = new Template("includes/footer.inc.tpl");
	echo $footer_template->output();
?>
