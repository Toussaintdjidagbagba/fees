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

class ExportCommissionControlleResume implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('commissioncontrlere');
    }
    public function  headings():array{
        return [
            'Code Apporteur',
            'Apporteur',
            'IFU	',
            'Email ',
            'Commission brute',
            'Commission Encadrement	',
            'Autre commission',
            'Bonus',
            'Fixe',
            'A déduire ce mois',
            'Avance en cours',
            'Retenue sur avance',
            'Avance anticiper',
            'Echéance restant',
            'Taux AIB',
            'AIB', 
            'Prelèvement', 
            'Carec',
            'Amical',
            'Naf',
            'Commission Nette	', 
            'Période'
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