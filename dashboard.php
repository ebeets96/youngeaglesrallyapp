<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$pilots = $rally->getPilotList();
	$young_eagles = $rally->getYoungEaglesList();
	$session->outputHeader("Dashboard");
?>
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Dashboard</h1>
		</div> <!-- /.col-lg-12 -->
	</div> <!-- /.row -->   
	<div class="row">
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-users fa-5x"></i>
						</div><!-- /.col-xs-3 -->
						<div class="col-xs-9 text-right">
							<div class="huge"><?php echo $pilots->getNumberOfPilots();?></div>
							<div>Pilots</div>
						</div><!-- /.col-xs-9 -->
					</div> <!-- /.row -->
				</div> <!-- /.panel-heading -->
				<a href="register_pilot.php">
					<div class="panel-footer">
						<span class="pull-left">Register a Pilot</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div><!-- /.panel-footer -->
				</a>
			</div> <!-- /.panel .panel-primary -->
		</div> <!-- /.col-lg-4 -->
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-green">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-paper-plane-o fa-5x"></i>
						</div><!-- /.col-xs-3 -->
						<div class="col-xs-9 text-right">
							<div class="huge"><?php echo $young_eagles->numberOfYoungEagles();?></div>
							<div>Registered Young Eagles</div>
						</div><!-- /.col-xs-9 -->
					</div><!-- /.row -->
				</div> <!-- /.panel-heading -->
				<a href="register.php">
					<div class="panel-footer">
						<span class="pull-left">Register a Young Eagles</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div> <!-- /.panel-footer -->
				</a>
			</div> <!-- /.panel .panel-green -->
		</div> <!-- /.col-lg-4 -->
		<div class="col-lg-4 col-md-6">
			<div class="panel panel-yellow">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-pie-chart fa-5x"></i>
						</div> <!-- /.col-xs-3 -->
						<div class="col-xs-9 text-right">
							<div class="huge"><?php echo $young_eagles->numberFlown(); ?></div>
							<div>Flights Completed</div>
						</div> <!-- /.col-xs-9 -->
					</div> <!-- /.row -->
				</div> <!-- /.panel-heading -->
				<a href="database.php">
					<div class="panel-footer">
						<span class="pull-left">View Rally Database</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div> <!-- /.panel-footer -->
				</a>
			</div> <!-- /.panel .panel-yellow -->
		</div> <!-- /.col-lg-4 -->
	</div><!-- /.row -->
	<div class="row">
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-users fa-fw"></i> Next 10 Young Eagles
				</div><!-- /.panel-heading -->
				<div class="panel-body">
					<div class="list-group">
						<?php
							foreach($young_eagles->getNotFlown() as $ye){
								echo "<span class=\"list-group-item\">";
								echo "<i class=\"fa ";
								if($ye->getGender()=='male')
									echo "fa-male";
								else
									echo "fa-female";
								echo " fa-fw\"></i>" . $ye->getAssignedNum() . " - " . $ye->getName();
								echo "</span>";
							}
						?>
					</div><!-- /.list-group -->
				</div><!-- /.panel-body -->
			</div><!-- /.panel  .panel-default-->
		</div><!-- /.col-lg-6 -->
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-bell fa-fw"></i> Notifications Panel...Coming Soon
				</div><!-- /.panel-heading -->
				<div class="panel-body">
					<div class="list-group">
						<?php
							/*while($alert = $alerts->fetch_assoc()){
								echo "<span class=\"list-group-item\">";
								echo "<i class=\"fa " . $alert['fa_icon'] . " fa-fw\"></i>&nbsp;" . $alert['message'] . "<span class=\"pull-right text-muted small\"><em>" . time_ago($alert['time']) . "</em></span>";
								echo "</span>";
							}*/
						?>
					</div><!-- /.list-group -->
				</div><!-- /.panel-body -->
			</div><!-- /.panel -->
		</div><!-- /.col-lg-6 -->
	</div><!-- /.row -->
<?php
	$footer_template = new Template("includes/footer.inc.tpl",$session->getAdmin());
	echo $footer_template->output();
?>