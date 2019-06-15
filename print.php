<?php
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$pilots = $conn->query("SELECT * FROM `pilots` INNER JOIN `rally_pilots` ON `rally_pilots`.`pilot_id`=`pilots`.`id` WHERE `rally_pilots`.`rally_id`=" . $rally->getRallyId() . " ORDER BY `first_name` ASC");
	if(isset($_POST['noshow'])){
		$youngeagle = $_POST['number'];
		$child_data = mysqli_fetch_assoc($conn->query("SELECT * FROM `registrants` WHERE `assigned_num`='$youngeagle'"));
		$conn->query("UPDATE `registrants` SET `flew` = '0', `noshow` = '1',`pilot` = '' WHERE `assigned_num` = $youngeagle;");
			$message = $child_data['first_name'].' '.$child_data['last_name'] . " did not show up";
			$conn->query("INSERT INTO `recent` (`id`, `time`, `message`, `fa_icon`) VALUES (NULL, '" . time() . "', '$message', ' fa-user-times')");
	}
	$session->outputHeader("Print Certificates");
?>
<div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Certificate Printing</h1>
            </div>
            <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-7">
					<?php
						if(isset($_REQUEST['success']))
							echo '<div id="confirmation" class="alert alert-success">' . $_REQUEST['message'] . '</div>';
						if(isset($_REQUEST['fail']))
							echo '<div id="confirmation" class="alert alert-danger">' . $_REQUEST['message'] . '</div>';
					?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Print a Certificate
                        </div>
                        <div class="panel-body">
                                    <form role="form" action="printable_pdf.php" method="post" target="my_iframe" onSubmit="showIframe();">
                                        <div class="form-group">
                                            <label>Number</label>
                                            <input name="number" id="yenumber" class="form-control" placeholder="Enter assigned number" autocomplete="off">
                                        </div>
										<div class="form-group">
                                    		<input class="form-control" placeholder="Young Eagles Name" id="yename" name="ye_name" disabled>
                                		</div>
										<div class="form-group">
                                            <label>Pilot</label>
                                            <select name="pilot" class="form-control" id="pilot">
												<option value="none">No pilot selected</option>
                                                <?php
													while($pilot = $pilots->fetch_assoc()){
														echo "<option value=\"" . $pilot['id'] . "\">" . $pilot['first_name']. " " . $pilot['last_name'] . "</option>";
													}
												?>
                                            </select>
                                        </div>
                                        <input disabled id="print-submit" type="submit" name="submitted" value="Print Certificate and Mark as Complete" class="btn btn-primary">
									</form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
				</div>
				<div class="col-lg-5">
					<div class="panel panel-red">
						<div class="panel-heading">Define a No-show</div>
						<div class="panel-body">
							<div class="alert alert-warning">
								Submit to this list those that chose not to fly, or those that did not show up when their name was called.
							</div>
							<form role="form" id="print-form" action="print.php" method="post">
								<div class="form-group">
									<label>Number</label>
									<input name="number" id="yenumber2" class="form-control" placeholder="Enter assigned number" autocomplete="off">
								</div>
								<div class="form-group">
									<input class="form-control" placeholder="Young Eagles Name" id="yename2" name="ye_name" disabled>
								</div>
								<input type="submit" name="noshow" value="Define as No Show" class="btn btn-danger">
							</form>
						</div>
						<!-- /.panel-body -->
					</div>
				</div>
				<!-- /.panel -->
                <!-- /.col-lg-12 -->
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default iframe-certificate" style="display:none">
							<div class="panel-heading">
								Requested Certificate
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12">
										<div class="embed-responsive embed-responsive-4by3">
											<iframe class="embed-responsive-item" name="my_iframe" src="printable_pdf.php"></iframe>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
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
    	$("#pilot").change(function (){
			if( $(this).find("option:selected").val() != "none")
				$("#print-submit").removeAttr('disabled');
			else
				$("#print-submit").attr('disabled','disabled');
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
