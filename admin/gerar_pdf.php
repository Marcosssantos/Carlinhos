<?php
require "../vendor/fpdf.php";
$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial","B",16);
$pdf->Cell(0,10,"Proposta C.P. Jardinagem",0,1);
$pdf->Output();
