<?php
/* 
|
|  Authentification
|
*/
Route::get('/cach',function ()
{
    Artisan::call('Config:cache');
});

////////////////////////Lien test
Route::get('/maint', function ()
{
    return view('vendor.error.501');
});

Route::get('/pdf', 'PDF@exemple');
Route::get('/defalcation', 'HistoriqueController@getetaterreurdefalcation');
Route::get('/updatefees', 'CommissionControllerAutres@renvoieDoc'); 
Route::get('/mail', 'CommissionController@testt');
Route::get('/updatece', 'CommissionController@controleCommercialDETAIl');

Route::get('/updatecere', 'CommissionController@controleCommercialDETAIlResum');
Route::get('/bd', 'HistoriqueController@sethistcommissionExceltraitementerreur');

Route::get('/mod', function ()
{
    
    //$n = abs(intval((strtotime("1-2-2021") - strtotime("01-10-2020")) / 60 / 60 / 24 / 30));
    $date1 = "01-12-2032";
    $date2 = "01-12-2020";
    
    $date1 = '01-25-2021';
                $date2 = '11-20-2021';
                
                $ts1 = strtotime($date1);
                $ts2 = strtotime($date2);
                
                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);
                
                $month1 = date('m', $ts1);
                $month2 = date('m', $ts2);
                
                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
    
    $tab = array();
    array_push($tab, 14);
    array_push($tab, 15);
    array_push($tab, 12);
    
    $tab1 = array();
    array_push($tab1, "14");
    array_push($tab1, "15");
    array_push($tab1, "12");
    
    while ($app = current($tab)) {
        if ($app == 12) {
            $tab1[key($tab)] = "Moi";
           echo   ($tab1[key($tab)]);
        }
        next($tab);
    }
    

});
///////////////////////////fin test 


Route::get('/login', 'AuthController\LoginController@login')->name('log');

Route::get('/modifier-mot-de-passe', 'AuthController\LoginController@passmodif')->name('pas');
Route::post('/loginapi', 'AuthController\LoginController@loginapi')->name('logapi');
Route::post('/login', 'AuthController\LoginController@authenticate');

Route::fallback(function() {
   return view('vendor.error.404');
});


Route::group([
    'middleware' => 'App\Http\Middleware\AuthECOM' 
 
], function(){

    Route::get('/dashboard', 'GestionnaireController@dash')->name('home');
    
    
    
    

    /* 
    |
    |  Tâche utilisateurs
    |
    */
    Route::get('/listusers', 'GestionnaireController@listusers')->name('listU');
    Route::get('/deconnecte', 'AuthController\LoginController@logout')->name('offU');
    Route::get('/delete-users-{id}', 'GestionnaireController@deleteuser')->name('DelU');
    Route::post('/modif-users', 'GestionnaireController@modifyuser')->name('ModifU');
    Route::get('/modif-users-{id}', 'GestionnaireController@getmodifyuser')->name('GetModifU');
    Route::post('/add-users', 'GestionnaireController@adduser')->name('AddU');
    Route::get('/reinitialiser-users-{id}', 'GestionnaireController@reinitialiseruser')->name('ReiniU');
    Route::get('/desactivé-users-{id}', 'GestionnaireController@desactiveuser')->name('ActiverU');
    Route::get('/activé-users-{id}', 'GestionnaireController@activeuser')->name('DesactiverU');
    Route::get('/profil', 'ProfilController@getprofil')->name('GPU');
    Route::post('/profil', 'ProfilController@setprofil')->name('SPU');


    /* 
    |
    |  Tâche Role
    |
    */
    Route::get('/listrole', 'RoleController@listrole')->name('listR');
    Route::get('/delete-roles-{id}', 'RoleController@deleterole')->name('DelR');
    Route::post('/modif-roles', 'RoleController@modifrole')->name('ModifR');
    Route::get('/modif-roles-{id}', 'RoleController@getmodifrole')->name('GetModifR');
    Route::get('/menu-roles-{id}', 'RoleController@getmenurole')->name('GetMenuAttr');
    Route::post('/menu-roles', 'RoleController@setmenurole')->name('MenuAttr');
    Route::post('/add-roles', 'RoleController@addrole')->name('AddR');

    /* 
    |
    |  Tâche Niveau
    |
    */
    Route::get('/listniveaux', 'NiveauController@listniveau')->name('listN');
    Route::get('/delete-niveaux-{id}', 'NiveauController@deleteniveau')->name('DelN');
    Route::post('/modif-niveaux', 'NiveauController@modifniveau')->name('ModifN');
    Route::get('/modif-niveaux-{id}', 'NiveauController@getmodifniveau')->name('GetModifN');
    Route::post('/add-niveau', 'NiveauController@addniveau')->name('AddN');

    /* 
    |
    |  Tâche Produit
    |
    */
    Route::get('/listproduit', 'ProduitController@listprod')->name('listProd');
    Route::get('/delete-produit-{id}', 'ProduitController@deleteprod')->name('DelProd');
    Route::post('/modif-produit', 'ProduitController@modifprod')->name('ModifProd');
    Route::get('/modif-produit-{id}', 'ProduitController@getmodifprod')->name('GetModifProd');
    Route::post('/add-produit', 'ProduitController@addprod')->name('AddProd');

    /*
    |
    |  Tâche Taux
    |
    */
    Route::get('/add-Taux-{prod}', 'TauxController@getlisttaux')->name('listT');
    Route::get('/delete-taux-{id}', 'TauxController@deletetaux')->name('DelT');
    Route::post('/modif-taux', 'TauxController@modiftaux')->name('ModifT');
    Route::get('/modif-taux-{id}', 'TauxController@getmodiftaux')->name('GetModifT');
    Route::post('/add-taux', 'TauxController@addtaux')->name('AddT');

    /* 
    |
    |  Tâche Périodicité
    |
    */
    Route::get('/listperiodicite', 'PeriodiciteController@listperiodicite')->name('listP');
    Route::get('/delete-periodicite-{id}', 'PeriodiciteController@deleteperiodicite')->name('DelP');
    Route::post('/modif-periodicite', 'PeriodiciteController@modifperiodicite')->name('ModifP');
    Route::get('/modif-periodicite-{id}', 'PeriodiciteController@getmodifperiodicite')->name('GetModifP');
    Route::post('/add-periodicite', 'PeriodiciteController@addperiodicite')->name('AddP');

    /* 
    |
    |  Tâche Schéma ou Bareme n'existe plus
    |
    */
    Route::get('/listbareme', 'SchemaController@getlistbareme')->name('listS');
    Route::get('/delete-bareme-{id}', 'SchemaController@deletebareme')->name('DelS');
    Route::post('/modif-bareme', 'SchemaController@modifbareme')->name('ModifS');
    Route::get('/modif-bareme-{id}', 'SchemaController@getmodifbareme')->name('GetModifS');
    Route::post('/add-bareme', 'SchemaController@addbareme')->name('AddS');
    
    /* 
    |
    |  Tâche Réclamation
    |
    */
    Route::get('/listreclamation', 'ReclamationController@getreclamation')->name('listRecl');
    Route::get('/modif-reclamation-{id}', 'ReclamationController@getmodifreclamation')->name('GetModifRecl');
    Route::post('/modif-reclamation', 'ReclamationController@setmodifreclamation')->name('ModifRecl');
    Route::get('/exportreclamation', 'ReclamationController@exportreclamation')->name('EXPTR');
    Route::get('/seachreclamation', 'ReclamationController@seachreclamation')->name('seerecl');
    Route::get('/triereclamation', 'ReclamationController@triereclamation')->name('trierecl');
    Route::get('/delete-reclamation', 'ReclamationController@deletereclamation')->name('DelRecl');
    
    /* 
    |
    |  Tâche Recherche quittance
    |
    */
    Route::get('/listquittance', 'QuittanceController@getquittance')->name('listQuit');
    Route::get('/seachquittance', 'QuittanceController@seachquittance')->name('seequit');
    Route::post('/modif-quittance', 'QuittanceController@setmodifquittance')->name('ModifQuit');
    Route::get('/exportquittance', 'QuittanceController@exportquittance')->name('EXPTQ');
    
    /* 
    |
    |  Tâche Commerciaux
    |
    */
	Route::get('/adhère-coordination-{id}', 'CommerciauxController@getadhcoordination')->name('AdhCoord'); // get insertion dans une coordination
    Route::post('/adhère-coordination', 'CommerciauxController@setadhcoord')->name('SAdhCoord'); // set insertion dans une coordination
    Route::get('/listcommerciaux', 'CommerciauxController@getlistcommerciaux')->name('listC');
    Route::post('/add-commerciaux', 'CommerciauxController@addcommerciaux')->name('AddC');
    Route::post('/modif-commerciaux', 'CommerciauxController@modifcommerciaux')->name('ModifC');
    Route::get('/modif-commerciaux-{id}', 'CommerciauxController@getmodifcommerciaux')->name('gelC');
    Route::get('/adhère-equipe-{id}', 'CommerciauxController@getadhcom')->name('AdhC');
    Route::post('/adhère-equipe', 'CommerciauxController@setadhcom')->name('SAdhC');
    Route::get('/changer-equipe-{id}', 'CommerciauxController@getadhcomc')->name('ChanC');
    Route::get('/addmanageurequipe-{mag}', 'EquipeController@listeqp')->name('AME');
    Route::get('/addmanageurins-{mag}', 'InspectionController@listins')->name('AMI');
    Route::get('/addmanageurrg-{mag}', 'RegionController@listrg')->name('AMG');
    Route::post('/setcompte', 'CommerciauxController@setimputer')->name('SIC');
    Route::post('/setannuleravance', 'CommerciauxController@setannuleravance')->name('SICA');
    Route::post('/setfixe', 'CommerciauxController@setfixe')->name('SICF');
    Route::post('/settelephonie', 'CommerciauxController@settel')->name('SICDT');
    Route::post('/setcarburant', 'CommerciauxController@setcarb')->name('SICDC');
    Route::post('/importercommerciaux', 'CommerciauxController@importerCommerciaux')->name('IC');
    Route::post('/addagence', 'CommerciauxController@setagence')->name('SAE');
    Route::post('/setrembourcement', 'CommerciauxController@setremboursement')->name('SRAC');
    Route::get('/addexistantins-{mag}', 'CommerciauxController@getexistant')->name('GE');
    Route::post('/addexistantins', 'CommerciauxController@setexistant')->name('GES'); 
    Route::post('/statutcom', 'CommerciauxController@sdac')->name('SADC');
    Route::get('/addexistanteqp-{mag}', 'CommerciauxController@getexistant')->name('GE');
    Route::post('/addexistanteqp', 'CommerciauxController@setexistant')->name('GESEquipe');
    Route::get('/addexistantrg-{mag}', 'CommerciauxController@getexistant')->name('GE');
    Route::post('/addexistantrg', 'CommerciauxController@setexistant')->name('GESEquipe');
    Route::get('/retrograder-{mag}', 'CommerciauxController@getretrograder')->name('RECG');
    Route::post('/retrograder', 'CommerciauxController@setretrograder')->name('RECS');
    Route::post('/carec', 'CommerciauxController@setpropreCarec')->name('SPC');
    Route::post('/amical', 'CommerciauxController@setpropreAmical')->name('SPA');
    Route::get('/exporter-commerciaux', 'CommerciauxController@exportationCommerciaux')->name('ECAT'); 
    Route::get('/erreurcommerciaux', 'CommerciauxController@geterrorcommerciaux')->name('ECI');
    Route::get('/exporter-avances', 'CommerciauxController@exportationavances')->name('EAVNV');
    Route::get('/exporter-avances-dues', 'CommerciauxController@exportationavancesdues')->name('EAVNVD');
    Route::get('/exporter-carec', 'CommerciauxController@exportationcarec')->name('ECCC');
    Route::get('/exporter-amical', 'CommerciauxController@exportationamical')->name('EAC');
    Route::post('/import-carec', 'CommerciauxController@setcarecimport')->name('SECCC');
    Route::post('/import-amical', 'CommerciauxController@setamicalimport')->name('SEAC');
    Route::post('/setbonuscommercial', 'CommerciauxController@setbonus')->name('SBCC');
    Route::post('/setdefalquercommercial', 'CommerciauxController@setretenue')->name('SDCC');
    Route::post('/setautrecommissioncommercial', 'CommerciauxController@setautrecom')->name('SACC');
    Route::post('/import-naf', 'CommerciauxController@setnafimport')->name('SENAF');
    Route::get('/exporter-naf', 'CommerciauxController@exportationnaf')->name('ENAF');
    Route::post('/import-naf-propre', 'CommerciauxController@setnafpropre')->name('SPENAF');
    
    /* 
    |
    |  Tâche Equipe
    |
    */
    Route::get('/listequipe', 'EquipeController@listeqp')->name('listE');
    Route::get('/delete-equipe-{id}', 'EquipeController@deleteequipe')->name('DelE');
    Route::post('/modif-equipe', 'EquipeController@modifequipe')->name('ModifE');
    Route::get('/modif-equipe-{id}', 'EquipeController@getmodifequipe')->name('ModifEG');
    Route::post('/add-equipe', 'EquipeController@addequipe')->name('AddE');

    /* 
    |
    |  Tâche Inspection
    |
    */
    Route::get('/listins', 'InspectionController@listins')->name('listI');
    Route::post('/add-ins', 'InspectionController@addins')->name('addi');
    Route::get('/delete-ins-{id}', 'InspectionController@deleteins')->name('DelI');
    Route::get('/modif-ins-{id}', 'InspectionController@getmodifins')->name('ModifIG');
    Route::post('/modif-ins', 'InspectionController@setmodifins')->name('ModifI');
    Route::get('/mutation-ins-{id}', 'InspectionController@getmutationins')->name('GMutationI');
    Route::post('/mutation-ins', 'InspectionController@setmutationins')->name('MutationI');

    /* 
    |
    |  Tâche Région
    |
    */
    Route::get('/listrg', 'RegionController@listrg')->name('listG');
    Route::post('/add-rg', 'RegionController@addrg')->name('addG');
    Route::get('/delete-rg-{id}', 'RegionController@deleterg')->name('DelG');
    Route::get('/modif-rg-{id}', 'RegionController@getmodifrg')->name('ModifGG');
    Route::post('/modif-rg', 'RegionController@setmodifrg')->name('ModifG');
    
    /* 
    |
    |  Tâche Coordination
    |
    */
    Route::get('/listcd', 'CoordinationController@listcd')->name('listCd');
    Route::post('/add-cd', 'CoordinationController@addcd')->name('addCd');
    Route::get('/delete-cd-{id}', 'CoordinationController@deletecd')->name('DelCd');
    Route::get('/modif-cd-{id}', 'CoordinationController@getmodifcd')->name('ModifCd');
    Route::post('/modif-cd', 'CoordinationController@setmodifcd')->name('SModifCd');


    /* 
    |
    |  Tâche Commission
    | 
    */
    Route::get('/detail-contrat-{id}', 'CommissionValidationController@getdetailcontrat')->name('GetDetailContrat');
    Route::get('/detail-quittance-{id}', 'CommissionValidationController@getdetailquittance')->name('GetDetailQuittance');
    Route::get('/detailcommission-{id}', 'CommissionValidationController@getcommissiondetail')->name('CommissionD');
    Route::get('/listerreurcommission', 'CommissionValidationController@geterreurcommissioncons')->name('ECC');
    Route::get('/listcommission-sp', 'CommissionValidationController@getcommissioncons')->name('listCom');
    Route::get('/commissionconfirmer-sp', 'CommissionController@confirmercalcul')->name('listCommConfirm');
    Route::get('/validecommissionindiv', 'CommissionController@valideCommissionIndiv')->name('validComind');
    Route::post('/rejet-sp', 'CommissionController@setrejetsp')->name('RCSP');
    Route::get('/getcommission-csp', 'CommissionController@getvalidationcsp')->name('GCCSP');
    Route::post('/rejet-csp', 'CommissionController@setrejetcsp')->name('RCCSP');
    Route::get('/commissionconfirmer-csp', 'CommissionController@setvalidationcsp')->name('ConfirmCSP');
    Route::get('/getcommission-dt', 'CommissionController@getvalidationdt')->name('GCDT');
    Route::post('/rejet-dt', 'CommissionController@setrejetdt')->name('RCDT');
    Route::get('/commissionconfirmer-dt', 'CommissionController@setvalidationdt')->name('ConfirmDT');
    Route::get('/getcommission-dg', 'CommissionController@getvalidationdg')->name('GCDG');
    Route::post('/rejet-dg', 'CommissionController@setrejetdg')->name('RCDG');
    Route::get('/commissionconfirmer-dg', 'CommissionController@setvalidationdg')->name('ConfirmDG');
    Route::get('/getcommission-cdaf', 'CommissionController@getvalidationcdaf')->name('GCCDAF');
    Route::post('/rejet-cdaf', 'CommissionController@setrejetcdaf')->name('RCCDAF');
    Route::get('/commissionconfirmer-daf', 'CommissionController@setvalidationcdaf')->name('ConfirmCDAF');
    Route::get('/getcommission-t', 'CommissionController@getvalidationtresorerie')->name('GCT');
    Route::post('/rejet-t', 'CommissionController@setrejettresorerie')->name('RCT');
    Route::post('/getcommission-t', 'CommissionController@getvalidationtresorerie')->name('SCT');
    Route::get('/getreglement', 'CommissionController@getreglement')->name('GRT');
    Route::post('/getreglement', 'CommissionController@getreglement')->name('GRTS');
    Route::post('/setreglement', 'CommissionController@setreglementEtape')->name('SRT');
    Route::get('/setcommission-t', 'CommissionController@setvalidationtresorerie')->name('SCTV');
    
    Route::get('/genererfichepaie', 'CommissionController@setgenerationfiche')->name('GFPC');
	Route::get('/genereetatexcel', 'CommissionController@etatdetailresume')->name('GEDREXCEL');
    Route::get('/listcommission-cg', 'CommissionController@getcommissioncg')->name('listcomcg');
	Route::get('/commissionconfirmer-cg', 'CommissionController@setvalidationcg')->name('Confirmcg');
    
    Route::get('/delete-reglement-{id}', 'CommissionController@deletereglement')->name('DelRCT');
    Route::get('/calcul_commissions_dste', 'CommissionController@setcommission')->name('l');
    Route::get('/autrecommission', 'CommissionController@importerAutreCommission')->name('IAC');
    Route::post('/autrecommission', 'CommissionController@setautreCommission')->name('SIAC');
    Route::post('/errorautrecommission', 'CommissionController@geterrorautrecommission')->name('EIAC');
    Route::post('/setautrecommission', 'CommissionController@setautrecom')->name('SAC');
    Route::post('/setbonus', 'CommissionController@setbonus')->name('SBC');
    Route::post('/setdefalquer', 'CommissionController@setretenue')->name('SDC');
    Route::get('/commission', 'ImporterCommissionController@getcommission')->name('listCommission');
    Route::post('/commission', 'ImporterCommissionController@importercommission')->name('SlistCommission');
    Route::get('/commission_auto', 'ImporterCommissionController@importercommission')->name('SlistCommissionAuto');
    Route::get('/valideimporter', 'CommissionController@validecommissionimporter')->name('vcip');
    
    
    /*
    *
    *
    * Commission groupe
    *
    */
    Route::get('/calcul_commissions_groupe', 'CommissionGroupeController@setcommission')->name('CCG');
    Route::get('/calcul_commissions_groupe_up', 'CommissionGroupeController@updatecom')->name('CCGU');
    Route::get('/list_commission_groupe_sp', 'CommissionGroupeController@getcommissioncons')->name('listComG');
    Route::get('/commissionconfirmer_groupe_sp', 'CommissionGroupeController@confirmercalcul')->name('listCommConfirmG');
    Route::post('/rejet_groupe_sp', 'CommissionGroupeController@setrejetsp')->name('RCSPG');
    Route::get('/Gdetailcommission-groupe-{id}', 'CommissionGroupeController@getcommissiondetail')->name('CommissionDG');
    
    
    Route::get('/getcommission-groupe-csp', 'CommissionGroupeController@getvalidationcsp')->name('GCCSPG');
    Route::post('/rejet-groupe-csp', 'CommissionGroupeController@setrejetcsp')->name('RCCSPG');
    Route::get('/commissionconfirmer-groupe-csp', 'CommissionGroupeController@setvalidationcsp')->name('ConfirmCSPG');

    Route::get('/getcommission-groupe-dt', 'CommissionGroupeController@getvalidationdt')->name('GCDTG');
    Route::post('/rejet-groupe-dt', 'CommissionGroupeController@setrejetdt')->name('RCDTG');
    Route::get('/commissionconfirmer-groupe-dt', 'CommissionGroupeController@setvalidationdt')->name('ConfirmDTG');

    Route::get('/getcommission-groupe-dg', 'CommissionGroupeController@getvalidationdg')->name('GCDGG');
    Route::post('/rejet-groupe-dg', 'CommissionGroupeController@setrejetdg')->name('RCDGG');
    Route::get('/commissionconfirmer-groupe-dg', 'CommissionGroupeController@setvalidationdg')->name('ConfirmDGG');

    Route::get('/getcommission-groupe-cdaf', 'CommissionGroupeController@getvalidationcdaf')->name('GCCDAFG');
    Route::post('/rejet-groupe-cdaf', 'CommissionGroupeController@setrejetcdaf')->name('RCCDAFG');
    Route::get('/commissionconfirmer-groupe-daf', 'CommissionGroupeController@setvalidationcdaf')->name('ConfirmCDAFG');
    
    Route::get('/getcommissiongroupe-t', 'CommissionGroupeController@getvalidationtresorerie')->name('GCTG');
    Route::post('/rejetgroupe-t', 'CommissionGroupeController@setrejettresorerie')->name('RCTG');
    Route::post('/getcommissiongroupe-t', 'CommissionGroupeController@getvalidationtresorerie')->name('SCTG');
    Route::get('/getreglementgroupe', 'CommissionGroupeController@getreglement')->name('GRTG');
    Route::post('/getreglementgroupe', 'CommissionGroupeController@getreglement')->name('GRTSG');
    Route::post('/setreglementgroupe', 'CommissionGroupeController@setreglementEtape')->name('SRTG');

    Route::get('/setcommissiongroupe-t', 'CommissionGroupeController@setvalidationtresorerie')->name('SCTVG');
    Route::get('/delete-reglementgroupe-{id}', 'CommissionGroupeController@deletereglement')->name('DelRCTG');

    

    /*
    |
    |
    | Tâche historique Commission
    |
    */
    Route::get('/histcommission', 'HistoriqueController@gethistcommission')->name('histCommission');
    Route::get('/histcommission-{id}', 'HistoriqueController@gethistcommissiondetail')->name('histCommissionD');
    Route::post('/histcommission', 'HistoriqueController@sethistcommission')->name('histCommissionPDF');
    Route::get('/conmag', 'HistoriqueController@listCm')->name('listContrMag');
    Route::post('/conmag', 'HistoriqueController@controleCommercialDETAIl')->name('listContrMagSet');
    Route::post('/conmagre', 'HistoriqueController@controleCommercialDETAIlResum')->name('listContrMagSetRe');
    Route::get('/conmagre', 'HistoriqueController@controleCommercialDETAIlResum')->name('listContrMagSetR');

    /* 
    |
    |  Tâche Document
    |
    */
    Route::get('/listdocument', 'DocumentController@getlistDocument')->name('listdocument');
    
    Route::post('/listdocument', 'DocumentController@setlistDocument')->name('listdocumentset');
    
    Route::post('/renvoifiche', 'DocumentController@renvoiefiche')->name('RFPC');

    /* 
    |
    |  Tâche Trace
    |
    */
    Route::get('/listtrace', 'TraceController@getlist')->name('listTrace');

    /* 
    |
    |  Tâche Contrat
    |
    */
    Route::get('/listcontrat', 'ContratController@getcontrat')->name('listContrat');
    Route::get('/modif-contrat-{id}', 'ContratController@getmodifcontrat')->name('GetModifContrat');
    Route::post('/modif-contrat', 'ContratController@setmodifcontrat')->name('ModifContrat');
    Route::get('/exportcontrat', 'ContratController@exportcontrat')->name('EXPTC');

    /* 
    |
    |  Tâche Client
    |
    */
    Route::get('/listclient', 'ClientController@getclient')->name('listClient');
    Route::get('/exportclient', 'ClientController@exportclient')->name('EXPTCL');
    
    /* 
    |
    |  Tâche Menu
    |
    */
    Route::get('/listmenu', 'MenuController@getmenu')->name('listM');
    Route::post('/menu', 'MenuController@setmenu')->name('AddMenu');
    Route::get('/delete-menu-{id}', 'MenuController@delmenu')->name('DelMenu');
    Route::post('/modif-menu', 'MenuController@setmodifmenu')->name('ModifMenu');
    Route::post('/actions', 'MenuController@setactionmenu')->name('ActionMenu');
    Route::get('/modif-menu-{id}', 'MenuController@getmodifmenu')->name('GetModifMenu');

    /*
    |
    |  Tâche Société
    |
    */
    Route::get('/listsoc', 'SocieteController@getsoc')->name('listSoc');
    Route::post('/soc', 'SocieteController@setsoc')->name('AddSoc');
    
});