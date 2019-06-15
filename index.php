<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$session->outputPrimaryHeader("Dashboard");
?>
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">My Rallies</h1>
		</div> <!-- /.col-lg-12 -->
	</div> <!-- /.row -->   
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				 <?php
					$authorized = $session->getAdmin()->getAuthorizedRallies();
					foreach($authorized as $rally){
				?>
				<div class="col-lg-6 col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-users fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">&nbsp;</div>
									<div><?=$rally->getTitle()?></div>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="col-md-4">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
							<div class="col-md-4">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
							<div class="col-md-4">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
								<div class="clearfix"></div>
							</div>
					</div>
				</div>
				<?php
					}
				?>
			</div>
		</div>
	</div>
<?php
	$footer_template = new Template("includes/footer.inc.tpl",$session->getAdmin());
	echo $footer_template->output();
?>