<?php
	require "dbConnect.php";
	require "classes.php";
	$session = new Session($conn);
	$session->auth();
	$id = $conn->real_escape_string($_REQUEST['rally_id']);
	$_SESSION["current_rally_id"] = $id;
	header("Location: ../dashboard.php");