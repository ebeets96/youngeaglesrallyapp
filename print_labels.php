<?php
	//Printable PDF
	require "includes/dbConnect.php";
	require "includes/classes.php";
	$session = new Session($conn);
	$session->auth();
	$rally = $session->getCurrentRally();
	if(isset($_REQUEST['id'])){
		require("includes/pdf_js.php");
		$pdf = new PDF_AutoPrint('l','mm','letter');
		$pdf->SetMargins(0,0,0);
		$pilot = new Pilot($conn);
		$pilot->load($_REQUEST['id']);
		//Load Setting
		$settings = $conn->query("SELECT * FROM `rallies` WHERE `id`=".$rally->getRallyId());
		$settings_array = $settings->fetch_assoc();
		
		$pdf->AddPage('portrait');
		$row_h = 25.5;
		$col_w = 70;
		$origin_x = 10;
		$origin_y = 19;
		$pdf->SetFont('Arial','',10);
		for($r=0;$r<10;$r++){
			for($c=0;$c<4;$c++){
			//Name
			$pdf->Text($origin_x+$c*$col_w,$origin_y+$r*$row_h,"Name: " . $pilot->getName());
			//EAA Chapter
			$pdf->Text($origin_x+$c*$col_w,$origin_y+$r*$row_h+5,"EAA #: " . $pilot->getEAANumber() . "   Chapter: " . $pilot->getEAAChapter() );
			//Date
			$pdf->Text($origin_x+$c*$col_w,$origin_y+$r*$row_h+10,"Date: " . $settings_array['rally_date']);
			//Plane
			$pdf->Text($origin_x+$c*$col_w,$origin_y+$r*$row_h+15, $pilot->getAircraftType());
			}
		}
		$pdf->AutoPrint(true);
		$pdf->Output();
	}
	
	