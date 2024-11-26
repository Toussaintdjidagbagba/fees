<?php

/**
 * Created by PhpStorm.
 * User: EMMAN
 * Date: 20/07/2022
 * Time: 08:32
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportContrat implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allcontrats');
    }
    
    public function  headings():array{
        return [
            'POLICE',
            'PRODUIT',
            'LIBELLE PRODUIT',
            'CODE CLIENT',
            'CLIENT',
            'CODE PAYEUR',
            'PAYEUR',
            'CODE APPORTEUR',
            'APPORTEUR',
            'STATUT',
            'DATE EFFET POLICE',
            'DATE FIN EFFET POLICE',
            'FRACTIONNEMENT',
        ];
    }
}