<?php

/**
 * Created by PhpStorm.
 * User: EMMAN
 * Date: 16/10/2021
 * Time: 11:09
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportCommissionControleDETAIL implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('commissiondetailcontrole');
        
    }
    public function  headings():array{
        return [
            'Code Apporteur',
            'Apporteur',
            'Niveau',
            'Police',
            'Quittance',
            'Code Payeur',
            'Période',
            'Base Commission', 
            'Commission', 
            'Commission Chef EQUIPE', 
            'Commission INSPECTEUR',
            'Commission Coordination', 
            'Commission Coordinateur Général'
        ];
    }

    /**
     * @return array
     */ /*
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:E1'; // All headers
                //$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('001e60');
            },
        ];
    } */
}