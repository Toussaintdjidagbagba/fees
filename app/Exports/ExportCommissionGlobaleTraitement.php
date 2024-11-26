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

class ExportCommissionGlobaleTraitement implements FromCollection, WithHeadings,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){
        return session('commissionglobale');
    }
    public function  headings():array{
        return [
            'Code',
            'Nom',
            'Prenom',
            'Niveau	',
            'Commission	<En tant apporteur>',
            'Encadrement',
            'Fixe',
            'Bonus	', 
            'Montant Brut', 
            'AIB',
            'Solde restant après AIB',
            'Restant mois dernière',
            'Solde après ajout du mois dernier',
            'Prélèvement <Différentes défalcations> ',
            'Solde restant après prélèvement <Différentes défalcations>',
            'Prélèvement NAF	', 
            'Solde restant après Prélèvement NAF',
            'Avance en cours',
            'Echéance avance',
            'Avance payer',
            'Solde restant après Avance payer',
            'Prélèvement Carec',
            'Solde restant après Prélèvement Carec',
            'Prélèvement Amical',
            'Solde restant après Prélèvement Amical',
            'Nette',
            'Periode',
        ];
    }
}