<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$pilots = $conn->query("SELECT `id` FROM `pilots`");
	$session->outputHeader("Print Labels");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Print Labels</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<?php
						if($pilots->num_rows>0){
					?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Pilots
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
											<th width="60">Print</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
											$i = 0;
											while($pilot = $pilots->fetch_assoc()){
												$object = new Pilot($conn);
												$object->load($pilot['id']);
												echo "<tr class=\"";
												if($i % 2 == 1)
													echo "odd";
												else
													echo "even";
												echo "gradeX\">";
												echo "<td>" . $object->getName() . "</td>";
												echo "<td>" . $object->getEAANumber() . "</td>";
												echo "<td>" . $object->getEAAChapter() . "</td>";
												echo "<td>" . $object->getAircraftType() . "</td>";
												echo "<td>" . $object->getEmail() . "</td>";
												echo "<td><a href=\"print_labels.php?id=" . $object->getId() . "\" class=\"pilot btn btn-primary btn-large btn-block\"><i class=\"fa fa-print\"></i></a></td>";
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

<?php
	$footer_template = new Template("includes/footer.inc.tpl");
	echo $footer_template->output();
?>