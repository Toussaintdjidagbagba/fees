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

class ExportCommissionDETAILGlobale implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('commissiondetailglobale');
        /*
        $tabl[0]["codeApp"] = "";
            $tabl[0]["nomApp"] = "";
            $tabl[0]["cat"] = "";
            $tabl[0]["police"] = "";
            $tabl[0]["effet"] = "";
            $tabl[0]["effetfin"] = "";
            $tabl[0]["frac"] = "";
            $tabl[0]["quittance"] = "";
            $tabl[0]["idpayeur"] = "";
            $tabl[0]["payeur"] = "";
            $tabl[0]["client"] = "";
            $tabl[0]["periode"] = "";
            $tabl[0]["codeCom"] = "";
            $tabl[0]["base"] = "";
            $tabl[0]["commission"] = "";
            $tabl[0]["chefequipe"] = "";
            $tabl[0]["chefins"] = "";
            $tabl[0]["chefrg"] = ""; */
            
    }
    public function  headings():array{
        /*return [
            'Police',
            'Produit',
            'Libellé produit',
            'Payeur',
            'Nom et prénoms Client',
            'Code Apporteur',
            'Nom et Prénoms Apporteur',
            'Statut	',
            'Date Effet Début',
            'Date Effet Fin',
            'Quittance',
            'Période',
            'Fractionnement',
            'Base Commission	', 
            'Commission Brute', 
            'Taux AIB', 
            'AIB',
            'Commission Nette',
            'Commission Brute Chef EQUIPE', 
            'Commission Brute INSPECTEUR',
            'Commission Brute Chef Région'
        ];*/
        
        return [
            'Code Apporteur',
            'Nom et Prénoms Apporteur',
            'Niveau	',
            'Produit',
            'Police',
            'Date Effet Début',
            'Date Effet Fin',
            'Fractionnement',
            'Quittance',
            'Payeur',
            'Nom et prénoms Payeur',
            'Nom et prénoms Client',
            'Période',
            'Code Commission',
            'Base Commission	', 
            'Commission', 
            'Commission Chef EQUIPE', 
            'Commission INSPECTEUR',
            'Commission Chef Région'
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