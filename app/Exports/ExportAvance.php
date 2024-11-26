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

class ExportAvance implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allavances');
    }
    public function  headings():array{
        return [
            'CODE',
            'NOM',
            'PRENOMS',
            'Montant rembourcer au cours du mois',
        ];
    }
}