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

class ExportAvanceDues implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allavancesdues');
    }
    public function  headings():array{
        return [
            'CODE',
            'NOM',
            'PRENOMS',
            'CHEF EQUIPE',
            'NOM CHEF EQUIPE',
            'PRENOMS CHEF EQUIPE',
            'CHEF INSPECTION',
            'NOM CHEF INSPECTION',
            'PRENOMS CHEF INSPECTION',
            'CHEF REGION',
            'NOM CHEF REGION',
            'PRENOMS CHEF REGION',
            'Montant dues',
			'Echeance',
        ];
    }
}