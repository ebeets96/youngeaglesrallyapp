<?php
	require "dbConnect.php";
	require "classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	if(isset($_REQUEST['pilots'])){
		$pilots = $rally->getPilotList()->getPilots();
		foreach($pilots as $pilot){
			$array[$pilot->getId()]=$pilot->getName();
		}
		echo json_encode($array);
	} else{
		$registrants = $rally->getYoungEaglesList()->getYoungEagles();
		foreach($registrants as $ye){
			$array[$ye->getId()]=$ye->getName();
		}
		echo json_encode($array);
	}