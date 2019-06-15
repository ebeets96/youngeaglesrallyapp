<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$pilots = $rally->getPilotList();
	$young_eagles = $rally->getYoungEaglesList();
	error_log(print_r($young_eagles,true));
	$session->outputHeader("Database");
	$youngeagle_template = new Template("includes/youngeagle.inc.tpl");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Rally Database</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
				<?php
					if($young_eagles->numberNotFlown()>0){
				?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Registered Young Eagles
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
											<th>Registration Time</th>
                                            <th>Number</th>
                                            <th>Full Name</th>
                                            <th>City</th>
                                            <th>Birthday</th>
                                            <th>Previous Participation</th>
											<th>Telephone Number</th>
											<th>Email Address</th>
											<th width="100">Modify</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											foreach($young_eagles->getNotFlown() as $youngeagle){
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . $youngeagle->getRegTime(true) . "</td>";
												echo "<td>" . $youngeagle->getAssignedNum() . "</td>";
												echo "<td>" . $youngeagle->getName() . "</td>";
												echo "<td>" . $youngeagle->getCity() . ", " . $youngeagle->getState() . "</td>";
												echo "<td>" . $youngeagle->getBirth() . "</td>";
												echo "<td>";
												if($youngeagle->getParticipation()==0)
													echo "No";
												else
													echo "Yes";
												echo "</td>";
												echo "<td>" . $youngeagle->getTelephone() . "</td>";
												echo "<td>" . $youngeagle->getEmail() . "</td>";
												$youngeagle_template->set("youngeagleid",$youngeagle->getId());
												echo "<td>" . $youngeagle_template->output() . "</td>";
												$i++;
											}
										?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
						</div>
                        <!-- /.panel-body -->
                    </div>
					<?php
						} else {
					?>
						<div class="alert alert-info" role="alert">There are no registered young eagles.</div>
					<?php
						}
					?>
                </div>
                <!-- /.col-lg-12 -->
				
				<div class="col-lg-12">
					<?php
						if($young_eagles->numberFlown()>0){
					?>
					<div class="panel panel-green">
                        <div class="panel-heading">
                            Flown Young Eagles
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Full Name</th>
                                            <th>City</th>
                                            <th>Birthday</th>
											<th>Telephone Number</th>
											<th>Pilot</th>
											<th>Flight Time</th>
											<th width="100">Modify</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											foreach($young_eagles->getFlown() as $youngeagle){
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . $youngeagle->getAssignedNum() . "</td>";
												echo "<td>" . $youngeagle->getName() . "</td>";
												echo "<td>" . $youngeagle->getCity() . ", " . $youngeagle->getState() . "</td>";
												echo "<td>" . $youngeagle->getBirth() . "</td>";
												echo "<td>" . $youngeagle->getTelephone() . "</td>";
												echo "<td>" . $youngeagle->getPilot(). "</td>";
												echo "<td>" . $youngeagle->getFlyTime(true) . "</td>";
												$youngeagle_template->set("youngeagleid",$youngeagle->getId());
												echo "<td>" . $youngeagle_template->output() . "</td>";
												$i++;
											}
										?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
						</div>
                        <!-- /.panel-body -->
                    </div>
					<?php
						}
					?>
				</div>
				<?php
					if(count($young_eagles->getNoShow())>0){
				?>
				<div class="col-lg-12">
					<div class="panel panel-red">
                        <div class="panel-heading">
                            No-Show Young Eagles
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
											<th>Registration Time</th>
                                            <th>Number</th>
                                            <th>Full Name</th>
                                            <th>City</th>
                                            <th>Birthday</th>
                                            <th>Previous Participation</th>
											<th>Telephone Number</th>
											<th>Email Address</th>
											<th width="100">Modify</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											foreach($young_eagles->getNoShow() as $youngeagle){
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . formatDate($youngeagle->getRegTime()) . "</td>";
												echo "<td>" . $youngeagle->getAssignedNum() . "</td>";
												echo "<td>" . $youngeagle->getName() . "</td>";
												echo "<td>" . $youngeagle->getCity() . ", " . $youngeagle->getState() . "</td>";
												echo "<td>" . $youngeagle->getBirth() . "</td>";
												echo "<td>";
												if($youngeagle->getParticipation()==0)
													echo "No";
												else
													echo "Yes";
												echo "</td>";
												echo "<td>" . $youngeagle->getTelephone() . "</td>";
												echo "<td>" . $youngeagle->getEmail() . "</td>";
												$youngeagle_template->set("youngeagleid",$youngeagle->getId());
												echo "<td>" . $youngeagle_template->output() . "</td>";
											}
										?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
						</div>
                        <!-- /.panel-body -->
                    </div>
				</div>
				<?php
					}
				?>
				
				<a name="pilots"></a>
				<div class="col-lg-12">
					<?php
						if($pilots->getNumberOfPilots()>0){
					?>
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            Registered Pilots
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>EAA Number</th>
                                            <th>EAA Chapter</th>
                                            <th>Aircraft Type</th>
											<th>Email Address</th>
											<th width="100">Modify</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											foreach($pilots->getPilots() as $pilot){
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . $pilot->getName() . "</td>";
												echo "<td>" . $pilot->getEAANumber() . "</td>";
												echo "<td>" . $pilot->getEAAChapter(). "</td>";
												echo "<td>" . $pilot->getAircraftType() . "</td>";
												echo "<td>" . $pilot->getEmail() . "</td>";
												echo "<td><a href=\"edit_pilot.php?id=" . $pilot->getId() . "\" class=\"edit btn btn-primary btn-large\" style=\"color:#FFF;\"><i class=\"fa fa-pencil\"></i></a> <a href=\"includes/delete.php?pilot=" . $pilot->getId() . "\" onclick=\"return confirm('Are you sure you want to delete ' + pilots[" . $pilot->getId() . "] + '?')\" class=\"registered btn btn-danger btn-large\" style=\"color:#FFF;\"><i class=\"fa fa-trash\"></i></a></td>";
												$i++;
											}
										?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
						</div>
                        <!-- /.panel-body -->
					</div>
				</div>
				<?php
					} else {
				?>
					<div class="alert alert-warning" role="alert">There are no registered pilots.</div>
				<?php
					}
				?>
            </div>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
	<script src="resourses/jquery/dist/jquery.min.js"></script> <script>
		var pilots={};
		<?php
			$query = $conn->query("SELECT * FROM `pilots`");
			while($row = mysqli_fetch_assoc($query)){
				echo "pilots[" . $row['id'] . "]" . "=\"" . $row['first_name'] . " " . $row['last_name']. "\";\n";
			}
		?>
		var registrants={};
		<?php
			$query = $conn->query("SELECT * FROM `registrants`");
			$array = array();
			while($row = mysqli_fetch_assoc($query)){
				echo "registrants[" . $row['id'] . "]" . "=\"" . $row['first_name'] . " " . $row['last_name']. "\";\n";
			}
		?>
	</script>
<!-- Bootstrap Core JavaScript -->
    <script src="resourses/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="resourses/metisMenu/dist/metisMenu.min.js"></script>
	
	<!-- Morris Charts JavaScript -->
<script src="resourses/raphael/raphael-min.js"></script>
<script src="resourses/morrisjs/morris.min.js"></script>
<script src="js/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

</body>
</html>
