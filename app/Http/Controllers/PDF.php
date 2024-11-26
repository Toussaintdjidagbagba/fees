<?php

/**
 * 
 * Url = https://github.com/crabbly/fpdf-laravel
 * Doc = http://www.fpdf.org
 * Dev = DJIDAGBAGBA S. T. Emmanuel
 * Ce 26 Octobre 2021
 * NSIAFEES
 * 
 * 
 * 
 * */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Crabbly\Fpdf\Fpdf;

class PDFF extends Fpdf
{
    //
    function Header() {
        
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page ',0,0,'C');
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
    
}

class PDF extends Controller
{
    //
    public function exemple(){
        $pdf = new PDFF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);

        for($i=1;$i<=40;$i++)
            $pdf->Cell(0,10,'Printing line number '.$i,0,1);

        $pdf->Output();
        exit;
    }

}
