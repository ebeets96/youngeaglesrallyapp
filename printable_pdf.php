<?php
	//Printable PDF
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	$admin = $session->getAdmin();
	if(isset($_REQUEST['submitted'])){
		require("includes/pdf_js.php");
		$pdf = new PDF_AutoPrint('l','mm','letter');
		$pdf->SetMargins(0,0,0);
		$youngeagle = $_REQUEST['number'];
		$pilot = new Pilot($conn);
		$pilot->load($_REQUEST['pilot']);
		$child_data = mysqli_fetch_assoc($conn->query("SELECT * FROM `registrants` WHERE `assigned_num`='$youngeagle'"));
		$pdf->AddPage('landscape');
		$pdf->SetFont('Arial','B',40);
		//Young Eagle's name
		$pdf->Text($admin->getSetting("ye-x"),$admin->getSetting("ye-y"),$child_data['first_name'].' '.$child_data['last_name']);
		$pdf->SetFont('Arial','I',14);
		//Date
		$pdf->Text($admin->getSetting("date-x"),$admin->getSetting("date-y"),date("F j, Y"));
		//Airplane Type
		$pdf->Text($admin->getSetting("airplane-x"),$admin->getSetting("airplane-y"),$pilot->getAircraftType());
		//Airport
		$pdf->Text($admin->getSetting("airport-x"),$admin->getSetting("airport-y"),$rally->getAirport());
		$pdf->AutoPrint(true);
		$pdf->Output();
		if($child_data['flew']==1){
			$message = $conn->real_escape_string($child_data['first_name'].' '.$child_data['last_name'] . "'s certificate was reprinted");
			$conn->query("INSERT INTO `recent` (`id`, `time`, `message`, `fa_icon`) VALUES (NULL, '" . time() . "', '$message', 'fa-print')");
			$conn->close();
		} else{
			$conn->query("UPDATE `registrants` SET `fly_time` = '" . time() . "', `flew` = '1', `noshow` = '0',`pilot` = '" . $pilot->getName() . "' WHERE `assigned_num` = $youngeagle;");
			$message = $conn->real_escape_string($child_data['first_name'].' '.$child_data['last_name'] . "'s certificate was printed");
			$conn->query("INSERT INTO `recent` (`id`, `time`, `message`, `fa_icon`) VALUES (NULL, '" . time() . "', '$message', 'fa-print')");
			$conn->close();
		}
	}
	
	