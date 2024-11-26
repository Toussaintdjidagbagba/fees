<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportCommerciaux implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('allcommerciaux');
    }
    public function  headings():array{
        return [
            'CODE',
            'NOM',
            'PRENOMS',
            'SEXE',
            'TELEPHONE',
            'ADRESSE',
            'EMAIL',
            'IFU',
            'NIVEAU',
            'CODE CHEF EQUIPE',
            //'NOM CHEF EQUIPE',
            'CODE CHEF INSPECTION',
            //'NOM CHEF INSPECTION',
            //'CODE CHEF REGION',
            //'NOM CHEF REGION',
            //'CODE CHEF COORDINATION',
            //'NOM CHEF COORDINATION',
            'DATE EFFET',
            'MODE DE REGLEMENT [MOMO, BANQUE, VIREMENT, CHEQUE]',
            'MOYEN DE REGLEMENT',
            'NUMERO DE COMPTE REGLEMENT',
            'FIXE',
            '.',
            '.',
            'LEGENDE',
        ];
    }
}