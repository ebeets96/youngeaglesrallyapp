<?php
	require "dbConnect.php";
	require "classes.php";
	$session = new Session($conn);
	$session->auth();
	if(isset($_REQUEST['pilot'])){
		$pilot = new Pilot($conn);
		$pilot->load($_REQUEST['pilot']);
		$pilot->delete();
		header("Location: ../database.php");
	} else if(isset($_REQUEST['youngeagle'])){
		$youngeagle = new YoungEagle($conn);
		$youngeagle->load($_REQUEST['youngeagle']);
		$youngeagle->delete();
		header("Location: ../database.php");
	}