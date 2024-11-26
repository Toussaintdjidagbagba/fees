<?php
/**
 * Created by PhpStorm.
 * User: EMMAN
 * Date: 18/10/2021
 * Time: 13:32
 */

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ExportErreurCommerciaux implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('commerciauxerror');
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
            'CODE CHEF INSPECTION',
            'DATE EFFET',
            'MODE DE REGLEMENT [MOMO, BANQUE, VIREMENT, CHEQUE]',
            'MOYEN DE REGLEMENT',
            'NUMERO DE COMPTE REGLEMENT',
            'FIXE',
            "Observations",
        ];
    }

}