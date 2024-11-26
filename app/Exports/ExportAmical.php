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

class ExportAmical implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allamical');
    }
    public function  headings():array{
        return [
            'CODE',
            'NOM',
            'PRENOMS',
            'MONTANT',
            'DUREE (mois)',
        ];
    }
}