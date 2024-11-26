<?php

/**
 * Created by PhpStorm.
 * User: EMMAN
 * Date: 03/05/2023
 * Time: 17:53
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportReclamation implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allrecl');
    }
    
    public function  headings():array{
        return [
            'CODE APPORTEUR',
            'APPORTEUR',
            'POLICE',
            'QUITTANCE',
            'CLIENT',
            'TYPE DE RECLAMATION',
            'RECLAMATION',
            'ETAT',
            'OBSERVATION',
            'DECLARER LE',
            'DATE DE REPONSE',
            'UTILISATEUR',
        ];
    }
}