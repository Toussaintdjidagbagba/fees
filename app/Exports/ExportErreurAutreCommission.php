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


class ExportErreurAutreCommission implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('autrecommissionerror');
    }
    public function  headings():array{
        return [
            trans('export.apport'),
            trans('export.autre'),
            trans('export.bonus'),
            trans('export.retenue'),
            trans('export.telephone'),
            trans('export.carburant'),
            "Libellé Retenue",
            "Libellé Bonus",
            trans('export.observations'),
        ];
    }

}