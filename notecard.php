<?php
	require "includes/fpdf.php";
	$pdf = new FPDF('P','mm',array(76.2,127));
	$pdf->SetAutoPageBreak(true, 10);
	$pdf->SetFont('Times','',48);
	$i=1;
	while($i<=300){
		$pdf->AddPage();
		$pdf->Cell(0, 53, $i, "B", 1, 'C');
		$pdf->Cell(0, 53, $i+1, "T", 0, 'C');
		$i+=2;
	}
	$pdf->Output();