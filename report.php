<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$pilots = $conn->query("SELECT * FROM `pilots` INNER JOIN `rally_pilots` ON `rally_pilots`.`pilot_id`=`pilots`.`id` WHERE `rally_pilots`.`rally_id`=" . $rally->getRallyId() . " ORDER BY `first_name` ASC");
	function getAge( $dob, $tdate ){
        $age = 0;
        while( $tdate > $dob = strtotime('+1 year', $dob))
        {
                ++$age;
        }
        return $age;
	}
	$session->outputHeader("Print Final Reports");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Final Reports</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
					<?php
						while($pilot = $pilots->fetch_assoc()){
							$pilot_name = $pilot['first_name'] . " " . $pilot['last_name'];
							$youngeaglesflownbypilot = $conn->query("SELECT * FROM `registrants` WHERE `pilot`='$pilot_name'");
							if($youngeaglesflownbypilot->num_rows > 0){
					?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?php
								echo $pilot_name;
							?>
                        </div>
                        <div class="panel-body">
							<div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="col-xs-6">Full Name</th>
											<th class="col-xs-3">Age</th>
											<th class="col-xs-3">Wait Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
							<?php
								$i = 0;
								while($ye = $youngeaglesflownbypilot->fetch_assoc()){
									echo "<tr class=\"";
										if($i % 2 == 1)
											echo "odd";
										else
											echo "even";
									echo "gradeX\">";
									echo "<td>" . $ye['first_name'] . " " . $ye['last_name'] . "</td>";
									echo "<td>" . getAge(strtotime($ye['birth']),$ye['fly_time']). "</td>";
									echo "<td>" . gmdate("H:i",($ye['fly_time']-strtotime($ye['reg_time']))) . "</td>";
									echo "</tr>";
									$i++;
								}
							?>
									</tbody>
								</table>
							</div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
					<?php
							} else {
								echo "<div class=\"alert alert-info\">" . $pilot_name . " did not fly any young eagles.</div>";
							}
						}
					?>
                    <!-- /.panel -->
				</div>
				<!-- /.panel -->
                <!-- /.col-lg-12 -->
				</div>
            <!-- /.row -->        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
	<script src="resourses/jquery/dist/jquery.min.js"></script> <script>
		var registrants=[];
		$.getJSON("includes/check_name.php", function(data){
			registrants = data;
		});
		if(window.chrome){
				$(".iframe-certificate").hide();
		}
		function showIframe(){
			if(!window.chrome){
				$(".iframe-certificate").show();
			}
		}
		function hideIframe(){
				$(".iframe-certificate").hide();
		}
		$( "#yenumber" ).keyup(function() {
			var number = registrants[$( "#yenumber" ).val()];
			if(number != null)
				$("#yename").val(number);
			else if($("#yenumber").val() == "")
				$("#yename").val("");
			else
				$("#yename").val("This number is not in the database");
		});
		$( "#yenumber2" ).keyup(function() {
			var number = registrants[$( "#yenumber2" ).val()];
			if(number != null)
				$("#yename2").val(number);
			else if($("#yenumber").val() == "")
				$("#yename2").val("");
			else
				$("#yename2").val("This number is not in the database");
		});
		$("#mark_completed").click(function(e){
			$(".iframe-certificate iframe").contents().find("body").html();
		});
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
