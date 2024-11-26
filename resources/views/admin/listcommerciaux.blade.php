@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 
	<center>
	<div  class="col-lg-3 col-md-4 col-xs-6"> 
        @if(in_array("add_commercial", session("auto_action"))) 
		    <button type="button" class="btn btn-info btn-icon btn-icon-right btn-rounded btn-bordered identifyingeqp btn-sm waves-effect waves-light" style="margin-bottom:5px; min-width: 200px; padding: 8px; " data-toggle="modal" data-target="#add" data-id="0" >
		        Commercial <i class="fa fa-plus" aria-hidden="true"></i>
		    </button>
		@endif
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
       @if(in_array("export_commerciaux", session("auto_action")))
            <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item" style="margin-bottom:5px; min-width: 200px; padding: 8px;"
            					href="{{route('ECAT')}}" title="Exporter les commerciaux">EXPORTER <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
        @endif
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
    @if(in_array("import_commerciaux", session("auto_action")))
        <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item identifyingeqp" style="margin-bottom:5px; min-width: 200px; padding: 8px;" href="#add" 
            					data-target="#add" data-toggle="modal" data-id="1impo" title="Importer les commerciaux">IMPORTER <i class="fa fa-upload" aria-hidden="true"></i></a>
    @endif
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
        @if(in_array("carec_commerciaux", session("auto_action")))
		    <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item identifyingeqp" style="margin-bottom:5px; min-width: 200px; padding: 8px; " href="#add" data-target="#add" data-toggle="modal" data-id="1cace" title="">
		        CAREC <i class="ico fa fa-gear"></i></a>
	    @endif
	</div>			
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
        @if(in_array("carec_commerciaux", session("auto_action")))
			<a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item identifyingeqp" style="margin-bottom:5px; min-width: 200px; padding: 8px; " href="#add" data-target="#add" data-toggle="modal" data-id="1naff" title="">
			    NAF <i class="ico fa fa-gear"></i></a>
		@endif
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
        @if(in_array("amical_commerciaux", session("auto_action")))
		    <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item identifyingeqp" style="margin-bottom:5px; min-width: 200px; padding: 8px;" href="#add" data-target="#add" data-toggle="modal" data-id="1amic" title="">AMICAL <i class="ico fa fa-gear"></i></a>
		@endif
        
    </div>
    
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
       @if(in_array("export_commerciaux", session("auto_action")))
            <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item" style="margin-bottom:5px; min-width: 200px; padding: 8px;"
            					href="{{route('EAVNV')}}" title="">AVANCE REMBOURSER <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
        @endif
        
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
       @if(in_array("export_commerciaux", session("auto_action")))
            <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item" style="margin-bottom:5px; min-width: 200px; padding: 8px;"
            					href="{{route('EAVNVD')}}" title="">AVANCE DUES <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
        @endif
        
    </div>
    <div  class="col-lg-3 col-md-4 col-xs-6"> 
       @if(in_array("add_agence", session("auto_action")))
		    <a class="btn btn-info btn-rounded btn-bordered waves-effect waves-light dropdown-item identifyingeqp" style="margin-bottom:5px; min-width: 200px;  padding: 8px;" href="#add" 
					data-target="#add" data-toggle="modal" data-id="1agen" title="Ajouter un partenaire">Partenaire <i class="fa fa-plus" aria-hidden="true"></i></a>
	   @endif
        
    </div>
    </center>
    
    <div class="col-lg-12 col-md-12 col-xs-12"> 
		<div class="box-content bordered info js__card">
			<h4 class="box-title with-control"> 
				Liste des Commerciaux NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>  
			</span>
			</h4>
			@if(App\Providers\InterfaceServiceProvider::nivprocessus() == 1)
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">  
				    
					<!------------------------------------------>
					<form class="form-horizontal" action="{{route('listC')}}" method="GET" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-5" style="margin-left: 30px; ">
								<input class=" form-control" type="hidden" name="rec" value="1">
								<div class="search col-sm-12">
									<input class=" form-control" type="text" name="check" value="{{$search}}" id="search" placeholder="Rechercher ">
								    <input class=" form-control" type="submit" id="sub">
								</div>
							</div>
						</div>
					</form>
					<script>		
						
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
							const input = document.getElementById("sub")
                            input.click()
						}, true);
						
					</script>
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Code</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Nom et prénom</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Email</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Niveau</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Code Equipe</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Code Inspection</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Code Région</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Code Coordination</th>
									<th data-priority="6" style="vertical-align:middle;text-align: center;">Actions</th>
								</tr>
							</thead>
							<tbody id="data">
								<?php $i = 1; ?>
								@forelse($list as $com)
								<tr>
									<td style="vertical-align:middle; text-align: center;">{{$com->codeCom}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->nomCom}} {{$com->prenomCom}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->mail}}</td>
									<td style="vertical-align:middle; text-align: center;">{{ App\Providers\InterfaceServiceProvider::infoniveau($com->Niveau)->libelleNiveau}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Equipe : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeEquipe) }}" >{{$com->codeEquipe}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Inspecteur : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeInspection) }}">{{$com->codeInspection}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Région : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeRegion) }}">{{$com->codeRegion}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Coordination : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeCD) }}">{{$com->codeCD}}</td>
									<td style="vertical-align:middle; text-align: center;">

										<div class="btn-group">
											<button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
											</button>
											<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: -200px; right: auto; transform: translate3d(0px, 38px, 0px);">
												<span>
														<a class="dropdown-item" href="/adhère-coordination-{{$com->codeCom}}" title="Adhérer une équipe">Adhérer une coordination</a>
													</span>
												@if(in_array("update_commercial", session("auto_action"))) 
												<span>
													<a class="dropdown-item" href="/modif-commerciaux-{{$com->codeCom}}" title="Modifier">Modifier le commercial</a>
												</span>
												@endif
												<?php 
												    
												    $comptecarer = array('Agent' => $com->codeCom, 'duree' => $com->dureeencourCarec, 'soldecarec' => $com->compteCarec, 'carec_propre' => $com->tauxCarec);
													$comptecarerjson = json_encode($comptecarer);
													
													$comptenaf = array('Agent' => $com->codeCom, 'effet' => $com->effetnaf, 'soldenaf' => $com->comptenaf, 'nafpropre' => $com->naf);
													$comptenafjon = json_encode($comptenaf);
													
												    $compteami = array('Agent' => $com->codeCom, 'duree' => $com->dureeencourAmical, 'soldeami' => $com->compteAmical, 'ami_propre' => $com->montantAmical);
													$compteamijson = json_encode($compteami);
												    
													$comptearray = array('Agent' => $com->codeCom, 'solde' => $com->compte, 'avance' => $com->avances, 'duree' => $com->duree, 'recent' => $com->recentrembourcer, 'lib' => $com->libCompte, 'num' => $com->numCompte, 'lib2' => $com->libCompte2, 'num2' => $com->numCompte2, 'fixe' => $com->fixe, 'tel' => $com->dotationTelephonie, 'carb' => $com->dotationCarburant);
													$comptejson = json_encode($comptearray);
													
													$compteactiv = array('Agent' => $com->codeCom, 'statut' => $com->statut);
													$compteactivjson = json_encode($compteactiv);
													
													$comptearrayavance = array('duree' => $com->duree, 'solde' => $com->compte, 'avances' => $com->avances, 'agent' => $com->Agent);
													$comptejsonavance = json_encode($comptearrayavance);
													
													$comptearraybonus = array('Agent' => $com->codeCom, 'bonussolde' => $com->bonus);
													$comptejsonbonus = json_encode($comptearraybonus);

													$comptearrayretenue = array('Agent' => $com->codeCom, 'retenue' => $com->retenue);
													$comptejsonretenue = json_encode($comptearrayretenue);
													
													$comptearrayother = array('Agent' => $com->codeCom, 'autresolde' => $com->AutreCommissionMoisCalculer);
													$comptejsonothercom = json_encode($comptearrayother);
												?>
												@if(in_array("show_account_commercial", session("auto_action"))) 

												<span>
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}comp" title="Compte">Compte du commercial</a>
												</span>

												@endif

												@if(in_array("advance_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}avan" title="Avance">Imputer une avance</a>
												</span><br>
												@endif
												
												@if(in_array("advance_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}anav" title="Annulation une avance">Annulation une avance</a>
												</span><br>
												@endif
												
												@if(in_array("other_com_hie", session("auto_action")))
												<span>
													<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejsonothercom}}autr" title="">Autres Commissions</a>
												</span><br>
												@endif
												
												@if(in_array("bonus_com_hie", session("auto_action")))
												<span>
													<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejsonbonus}}bonu" title="">Bonus</a>
												</span>
												@endif
												<br>
												@if(in_array("retenue_com_hie", session("auto_action")))
												<span>
													<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejsonretenue}}rete" title="">Retenue</a>
												</span> <br>
												@endif
												
												@if(in_array("anticipation_reimbursement", session("auto_action")))
												<span>
													<a class="dropdown-item identifyingeqp" href="#add" data-toggle="modal" data-id="{{$comptejsonavance}}remb" title="">Remboursement anticiper</a> <br>
												</span>
												@endif
												
												@if(in_array("actv_commercial", session("auto_action"))) 
                                                
												<span>	
												@if($com->statut == '0')
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$compteactivjson}}actv" title="Désactiver">Désactiver ?</a>
												@endif
												@if($com->statut == '1')
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$compteactivjson}}actv" title="Activer">Activer ?</a>
												@endif
												</span><br>
												@endif

												@if(in_array("fixe_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}fixe" title="Fixe">Fixe</a>
												</span><br>
												@endif
												
												@if(in_array("carec_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptecarerjson}}care" title="Carec">Carec</a>
												</span><br>
												@endif
												
												@if(in_array("ami_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$compteamijson}}acim" title="Amical">Amical</a>
												</span><br>
												@endif
												
												@if(in_array("ami_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptenafjon}}nafp" title="Naf">NAF</a>
												</span><br>
												@endif
												
												@if(in_array("add_telephone_staffing_commercial", session("auto_action"))) 
												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}tele" title="Téléphonie">Dotation Téléphonie</a>
												</span><br>
												@endif

												@if(in_array("add_fuel_staffing_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}carb" title="Carburant">Dotation Carburant</a>
												</span><br>
												@endif

												@if($com->Niveau == "CONS" || $com->Niveau == "AG" || $com->Niveau == "INST" || $com->Niveau == "B" || $com->Niveau == "COU")
													@if($com->codeEquipe == "")
													@if(in_array("Join_team_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/adhère-equipe-{{$com->codeCom}}" title="Adhérer une équipe">Adhérer une équipe</a>
													</span>
													@endif

													@else
													@if(in_array("update_equipe_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/changer-equipe-{{$com->codeCom}}" title="Changer d'équipe">Changer d'équipe</a>
													</span>
													@endif
													@endif
													@if(in_array("leader_equipe_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addmanageurequipe-{{$com->codeCom}}"  title="Devenir chef d'équipe (NOUVELLE)">Devenir chef d'Equipe</a>
													</span>
													@endif

													@if(in_array("leader_equipe_existante_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addexistanteqp-{{$com->codeCom}}"  title="Devenir chef d'équipe (EXISTANTE)">Devenir chef d'Equipe (EXISTANTE)</a>
													</span>
													@endif
												@else
												    @if($com->Niveau == "CEQP")
												    @if(in_array("Inspection_head_commercial", session("auto_action"))) 
													<span> 
														<a class="dropdown-item" href="/addmanageurins-{{$com->codeCom}}"  title="Devenir chef d'une inspection (NOUVELLE)">Devenir chef d'Inspection</a>
													</span>
													@endif
													@if(in_array("Inspection_head_existante_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addexistantins-{{$com->codeCom}}"  title="Devenir chef d'une inspection (EXISTANTE)">Devenir chef d'une Inspection (EXISTANTE)</a>
													</span>
													@endif

													@if(in_array("downgrade_cons_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en CONSEILLER">Rétrograder</a>
												    </span>
												    @endif

													@else
													     @if($com->Niveau == "INS" || $com->Niveau == "BD" || $com->Niveau == "BDS" || $com->Niveau == "APL")
													     @if(in_array("leader_region_commercial", session("auto_action"))) 
													        <span>
															<a class="dropdown-item" href="/addmanageurrg-{{$com->codeCom}}" title="Devenir chef d'une région (NOUVEAU)">Devenir chef d'une région</a> 
															</span>
															@endif

															@if(in_array("leader_region_existante_commercial", session("auto_action"))) 
															<span>
																<a class="dropdown-item" href="/addexistantrg-{{$com->codeCom}}" data-toggle="modal" title="Devenir chef d'une région (EXISTANTE)">Devenir chef d'une région (EXISTANTE)</a>
															</span>
															@endif

															@if(in_array("downgrade_ceqp_commercial", session("auto_action"))) 

															<span>
																<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en CHEF D'EQUIPE ">Rétrograder</a>
														    </span>
														    @endif

													     @else
														     @if(in_array("downgrade_ins_commercial", session("auto_action"))) 
															<span>
																<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en INSPECTION ">Rétrograder</a>
															</span>
															@endif

													     @endif
													@endif
												@endif
												
											</div>
										</div>
									</td>
								</tr>
								<?php $i++; ?>
								@empty
								<tr>
									<td colspan="10"><center>Pas de commerciaux enregistrer!!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
						{{$list->links()}}

						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

					</div> 
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-6 col-xs-12 -->
		</div>
			</div>
			@else
			    <div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> Info! Veuillez patienter la fin du processus de commissionnement. </center></div>
				</div>
			@endif
		</div>
	</div>
	</div>

@endsection

@section('js')
	<script>

          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
    </script>

    <script type="text/javascript">
	    $(function () {
	    	$("#add").on('hidden.bs.modal', function () {
		        window.location.reload();
		    });
	    });

	</script>

     <script type="text/javascript">
	    $(function () {
	        $(".identifyingeqp").click(function () {
	            var id = $(this).data('id');
	            var div = document.getElementById('tst');
	            
	            if (id != 0) {
	                
	                if(id.substr(-4, 4) == "anav"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);

                            div.innerHTML = '' +
                                '<div class="modal-header">' +
                                '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '   <h4 class="modal-title" id="myModalLabel">Annulation d\'avance : </h4>' +
                                '</div>' +
                                '<div class="modal-body">' +
                                '   <form class="form-horizontal" method="post" action="{{ route("SICA") }}">' +
                                '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                                '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
                                '      <input type="hidden" name="actions" value="update" />' +
                                '      <div class="form-group">  ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="ac" class="col-sm-12">Avance :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="ac" name="avance" value="'+new Intl.NumberFormat('fr-FR').format(json.avance)+'" disabled="true"> </div>  ' +
                                '           </div>    ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="er" class="col-sm-12">Echéance :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="er" name="echance" value="'+json.duree+'" disabled="true"> </div>  ' +
                                '           </div>    ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="mm" class="col-sm-12">Mensuel :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="mm" name="solde" value="'+new Intl.NumberFormat('fr-FR').format(json.avance/json.duree)+'" disabled="true"></div>' +
                                '           </div>    ' +
                                '      </div>       ' +
                                '      <div class="form-group"> ' +
                                '           <div class="col-sm-12">  ' +
                                '              <label for="aa" class="col-sm-12">Voulez-vous vraiment annuler l\'avance en cours ? </label>' +
                                '           </div>    ' +
                                '       </div>                   ' +
                                '    <div class="modal-footer">   ' +
                                '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;"> ANNULER </button>' +
                                '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
                                '    waves-light" style="float:right; color:white;">FERMER</button></div>';
                	}

	            	if(id.substr(-4, 4) == "comp"){
	            	    identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);

                    	div.innerHTML = '<div class="modal-header">' +
							'<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button>' +
                            '<h4 class="modal-title" id="myModalLabel">Compte du commerciaux : </h4>' +
                            '</div><div class="modal-body">' +
                            '<form class="form-horizontal" method="post" action="">' +
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}" /> ' +
                            ' <div class="form-group"> ' +
                            '    <div class="col-sm-6"> ' +
                            '    <label for="inp-type-2" class="col-sm-12">Solde commission (en cours) :</label>' +
                            '    <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" value="'+json.solde+'" name="solde" disabled="true"></div>' +
                            '    </div> ' +
                            '    <div class="col-sm-6"> ' +
                            '    <label for="inp-type-2" class="col-sm-12">Fixe :</label>' +
                            '    <div class="col-sm-12"> <input type="text" class="form-control" id="inp-type-2" value="'+json.fixe+'" name="avance" disabled="true"> </div>  ' +
                            '    </div> ' +
                            ' </div> ' +
                            ' <div class="form-group"> ' +
                            '    <div class="col-sm-6"> ' +
                            '       <label for="inp-type-2" class="col-sm-12">Avance :</label>' +
                            '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="duree" value="'+json.avance+'" disabled="true"> </div> ' +
                            '    </div> ' +
                            '    <div class="col-sm-6"> ' +
                            '       <label for="inp-type-2" class="col-sm-12">Nombre d\'échéance : </label>' +
                            '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="recent" value="'+json.duree+'" disabled="true"> </div> ' +
                            '    </div> ' +
                            ' </div> ' +
                            ' <div class="form-group"> ' +
                            '    <div class="col-sm-6">  ' +
                            '       <label for="inp-type-2" class="col-sm-12">Libellé règlement : </label>' +
                            '     <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="lib" value="'+json.lib+'" disabled="true"></div> ' +
                            '    </div> ' +
                            '    <div class="col-sm-6">  ' +
                            '       <label for="inp-type-2" class="col-sm-12">Numéro règlement :</label>' +
                            '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="nullib" value="'+json.num+'" disabled="true"> </div>' +
                            '    </div>' +
                            '</div>'+
                            ' </div>   </div><div class="modal-footer"><button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button></div>';
	            	}

                	if(id.substr(-4, 4) == "avan"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);

	            	    if(json.avance == 0)
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Imputer une avance : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SIC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <input type="hidden" name="actions" value="add" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-2 control-label">Solde :</label>' +
	                            '           <div class="col-sm-4"><input type="text" class="form-control" id="inp-type-2" name="solde" value="'+json.solde+'" disabled="true"></div>' +
	                            '           <label for="inp-type-2" class="col-sm-2 control-label">Avance :</label>' +
	                            '           <div class="col-sm-4"><input type="text" class="form-control" id="inp-type-2" name="avance" value="'+json.avance+'" disabled="true"> </div>  ' +
	                            '      </div>       ' +
	                            '      <div class="form-group"> ' +
	                            '           <div class="col-sm-6">  ' +
	                            '              <label for="inp-type-2" class="col-sm-12">Saisir avance à imputer : </label>' +
	                            '              <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="avancenew"></div> ' +
	                            '           </div>    ' +
	                            '           <div class="col-sm-6">  ' +
	                            '               <label for="inp-type-2" class="col-sm-12">Nombre d\'échéancier : </label>' +
	                            '               <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="avancenombr"></div>' +
	                            '           </div> ' +
	                            '       </div>                   ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">IMPUTER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
                        else
                            div.innerHTML = '' +
                                '<div class="modal-header">' +
                                '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '   <h4 class="modal-title" id="myModalLabel">Modifier une avance restante : </h4>' +
                                '</div>' +
                                '<div class="modal-body">' +
                                '   <form class="form-horizontal" method="post" action="{{ route("SIC") }}">' +
                                '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                                '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
                                '      <input type="hidden" name="actions" value="update" />' +
                                '      <div class="form-group">  ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="ac" class="col-sm-12">Avance :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="ac" name="avance" value="'+new Intl.NumberFormat('fr-FR').format(json.avance)+'" disabled="true"> </div>  ' +
                                '           </div>    ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="er" class="col-sm-12">Echéance :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="er" name="echance" value="'+json.duree+'" disabled="true"> </div>  ' +
                                '           </div>    ' +
                                '           <div class="col-sm-4">  ' +
                                '               <label for="mm" class="col-sm-12">Mensuel :</label>' +
                                '               <div class="col-sm-12"><input type="text" class="form-control" id="mm" name="solde" value="'+new Intl.NumberFormat('fr-FR').format(json.avance/json.duree)+'" disabled="true"></div>' +
                                '           </div>    ' +
                                '      </div>       ' +
                                '      <div class="form-group"> ' +
                                '           <div class="col-sm-6">  ' +
                                '              <label for="aa" class="col-sm-12">Augmenter l\'avance : </label>' +
                                '              <div class="col-sm-12"><input type="number" class="form-control" id="aa" min="0" name="avancenew"></div> ' +
                                '           </div>    ' +
                                '           <div class="col-sm-6">  ' +
                                '               <label for="ne" class="col-sm-12">Augmenter l\'échéancier : </label>' +
                                '               <div class="col-sm-12"><input type="number" class="form-control" id="ne" min="0" name="avancenombr" value=""></div>' +
                                '           </div> ' +
                                '       </div>                   ' +
                                '    <div class="modal-footer">   ' +
                                '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;"> MODIFIER </button>' +
                                '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
                                '    waves-light" style="float:right; color:white;">FERMER</button></div>';
                	}
                	
                	if(id.substr(-4, 4) == "autr"){
    	            	identifiant = id.slice(0, id.length - 4);
    	            	var json = JSON.parse(identifiant);
    
    	            	div.innerHTML = '<div class="modal-header">' +
    						'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
    						'<h4 class="modal-title" id="myModalLabel">Imputer une autre commission : </h4>' +
    						'</div><div class="modal-body">' +
    						'<form class="form-horizontal" method="post" action="{{ route("SACC") }}">' +
    						'<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
    						'<input type="hidden" name="codecom" value="'+json.Agent+'" /> ' +
    						'     <div class="form-group"> ' +
    						'<div class="col-sm-12"> ' +
    						' <label for="inp-type-2" class="col-sm-12 ">Autre commission actuelle :</label>   ' +
    						'<div class="col-sm-12">' +
    						'<input type="text" class="form-control" id="inp-type-2" name="solde" value="'+json.autresolde+'" disabled="true">' +
    						'</div> ' +
    						'</div> ' +
    						'</div>       ' +
    					    ' <div class="form-group"> ' +
                            '<div class="col-sm-12"> ' +
                            ' <label for="inp-type-3" class="col-sm-12">Libellé autre commission : </label>' +
                            '<div class="col-sm-12"><input type="text" class="form-control" id="inp-type-3" name="libother">' +
                            '</div> ' +
                            '</div>                     ' +
                            '    </div>' +
    						' <div class="form-group"> ' +
    						'<div class="col-sm-12"> ' +
    						' <label for="inp-type-2" class="col-sm-12">Saisir autre commission à ajouter : </label>' +
    						'<div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="soldeautre">' +
    						'</div> ' +
    						'</div>                     ' +
    						'    </div>' +
    						'<div class="modal-footer">   ' +
    						'<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">IMPUTER</button>  ' +
    						' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
    						'</div>';
    				}
                	
                	if (id.substr(-4,4) == "bonu") {
                        identifiant = id.slice(0, id.length - 4);
                        var json = JSON.parse(identifiant);
    
                        div.innerHTML = '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<h4 class="modal-title" id="myModalLabel">Imputer un bonus : </h4>' +
                            '</div><div class="modal-body">' +
                            '<form class="form-horizontal" method="post" action="{{ route("SBCC") }}">' +
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                            '<input type="hidden" name="codecom" value="'+json.Agent+'" /> ' +
                            '     <div class="form-group"> ' +
                            '<div class="col-sm-12"> ' +
                            ' <label for="inp-type-2" class="col-sm-12 ">Bonus actuel :</label>   ' +
                            '<div class="col-sm-12">' +
                            '<input type="text" class="form-control" id="inp-type-2" name="solde" value="'+json.bonussolde+'" disabled="true">' +
                            '</div> ' +
                            '</div> ' +
                            '</div>       ' +
                            ' <div class="form-group"> ' +
                            '<div class="col-sm-12"> ' +
                            ' <label for="inp-type-3" class="col-sm-12">Libellé bonus : </label>' +
                            '<div class="col-sm-12"><input type="text" class="form-control" id="inp-type-3" name="libbonu">' +
                            '</div> ' +
                            '</div>                     ' +
                            '    </div>' +
                            ' <div class="form-group"> ' +
                            '<div class="col-sm-12"> ' +
                            ' <label for="inp-type-2" class="col-sm-12">Saisir bonus : </label>' +
                            '<div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="soldebonus">' +
                            '</div> ' +
                            '</div>                     ' +
                            '    </div>' +
                            '<div class="modal-footer">   ' +
                            '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">IMPUTER</button>  ' +
                            ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                            '</div>';
    				}

				    if (id.substr(-4,4) == "rete") {
                    identifiant = id.slice(0, id.length - 4);
                    var json = JSON.parse(identifiant);

                    div.innerHTML = '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel">Retenue : </h4>' +
                        '</div><div class="modal-body">' +
                        '<form class="form-horizontal" method="post" action="{{ route("SDCC") }}">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                        '<input type="hidden" name="codecom" value="'+json.Agent+'" /> ' +
                        '     <div class="form-group"> ' +
                        '<div class="col-sm-12"> ' +
                        ' <label for="inp-type-2" class="col-sm-12 ">Retenue actuel :</label>   ' +
                        '<div class="col-sm-12">' +
                        '<input type="text" class="form-control" id="inp-type-2" name="solde" value="'+json.retenue+'" disabled="true">' +
                        '</div> ' +
                        '</div> ' +
                        '</div>       ' +
                        ' <div class="form-group"> ' +
                        '<div class="col-sm-12"> ' +
                        ' <label for="inp-type-3" class="col-sm-12">Libellé retenue : </label>' +
                        '<div class="col-sm-12"><input type="text" class="form-control" id="inp-type-3" name="libretenue">' +
                        '</div> ' +
                        '</div>                     ' +
                        '    </div>' +
                        ' <div class="form-group"> ' +
                        '<div class="col-sm-12"> ' +
                        ' <label for="inp-type-2" class="col-sm-12">Saisir solde à défalquer : </label>' +
                        '<div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="soldedefalque">' +
                        '</div> ' +
                        '</div>                     ' +
                        '    </div>' +
                        '<div class="modal-footer">   ' +
                        '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">IMPUTER</button>  ' +
                        ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                        '</div>';
				}
                	
                	if(id.substr(-4, 4) == "remb"){
                    	identifiant = id.slice(0, id.length - 4);
                    	var json = JSON.parse(identifiant);
        
                        div.innerHTML = '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<h4 class="modal-title" id="myModalLabel">Rembourcement anticiper : </h4>' +
                            '</div><div class="modal-body">' +
                            '<form class="form-horizontal" method="post" action="{{ route("SRAC") }}">' +
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                                '<input type="hidden" name="agent" value="'+json.agent+'" />' +
                              
                            '<div class="form-group"> ' +
                            '<div class="col-sm-6" style=""> ' +
                        	' <label for="inp-type-2" class="col-sm-12">Solde : </label>' +
        					'<div class="col-sm-12"><input type="number" class="form-control" value="'+json.solde+'" id="inp-type-2" disabled="true" name="solde"> </div>' +
                            '</div>' +
                            '<div class="col-sm-6" style=""> ' +
                        	' <label for="inp-type-2" class="col-sm-12">Nombre d\'échéance : </label>' +
        					'<div class="col-sm-12"><input type="number" class="form-control" value="'+json.duree+'" id="inp-type-2" disabled="true" name="necha"> </div>' +
                            '</div>' +
                            '</div> '+
                                
                            '<div class="form-group"> ' +
                            '<div class="col-sm-6" style=""> ' +
                        	' <label for="inp-type-2" class="col-sm-12">Avance actuel : </label>' +
        					'<div class="col-sm-12"><input type="number" class="form-control" value="'+json.avances+'" id="inp-type-2" disabled="true" name=""> </div>' +
                            '</div>' +
                            '</div> '+
                            
                            '<div class="form-group"> ' +
                            '<div class="col-sm-12" style=""> ' +
                        	' <label for="inp-type-2" class="col-sm-12">Nombre d\'échéance à rembourcer : </label>' +
        					'<div class="col-sm-12"><input type="number" min="0" max="'+json.duree+'" class="form-control" id="inp-type-2" name="echeance"> </div>' +
                            '</div>' +
                            '</div> '+
                            
                            '<div class="form-group"> ' +
                            '<div class="col-sm-12" style=""> ' +
                        	'    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">VALIDER</button>' +
                        	'    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                        '    waves-light" style="float:right; color:white;">FERMER</button></div>' +
                        	'</div>' +
                            '</div> '+
                            
                            '</div>' +
                            '<div class="modal-footer">' +
                            '</div>';
                            
                    }

                	if(id.substr(-4, 4) == "fixe"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Modifier Fixe : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SICF") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Fixe actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="solde" value="'+json.fixe+'" disabled="true"></div>' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Remplacé par :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="fixenew"> </div>  ' +
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">MODIFIER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }
	                
	                if(id.substr(-4, 4) == "care"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Carec : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SPC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="sc" class="col-sm-12 ">Compte actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="sc" name="soldecarec" value="'+json.soldecarec+'" disabled="true"></div>' +
	                            '           <label for="tdc" class="col-sm-12 ">Durée :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="tdc" value="'+json.duree+'" disabled="true" name="dure"> </div>  ' +
	                            '           <label for="tpc" class="col-sm-12 ">Propre taux en %:</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="tpc" value="'+json.carec_propre+'" name="tauxcarecpropre"> </div>  ' +
	                            
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">MODIFIER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }
	                
	                if(id.substr(-4, 4) == "naff"){
                		div.innerHTML = '<div class="modal-header">' +
						                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						                    '<h4 class="modal-title" id="myModalLabel">NAF : </h4>' +
						                    '</div><div class="modal-body">' +
						                    '<form class="form-horizontal" method="post" action="{{ route("SENAF") }}" enctype="multipart/form-data">' +
						                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						                    '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> '+
						                    '<div class="form-group"> ' +
						                    '<div class="col-sm-12">' +
						                    '<a class="btn-primary" style="float:right; color: white; padding: 15px; margin-right: 10px" href="{{ route("ENAF") }}"> Télécharger le fichier à remplir </a>' +
						                    '</div> ' +
						                    '</div> ' +
						                    ' <div class="form-group"> ' +
						                    '<div class="col-sm-12"> ' +
						                    ' <label for="inp-type-2" class="col-sm-12">Importer : </label>' +
						                    ' <div class="col-sm-12"><input type="file" accept=".xls, .xlsx" class="form-control" id="inp-type-2" name="fichienaf">' +
						                    '</div> ' +
						                    '</div>' +
						                    '    </div>' +
						                    '<div class="modal-footer">   ' +
						                    '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">Valider</button>  ' +
						                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
						                    '</div>';
	                }
	                
	                if(id.substr(-4, 4) == "nafp"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">NAF : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SPENAF") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="sc" class="col-sm-12 ">Compte actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="sc" name="soldenaf" value="'+json.soldenaf+'" disabled="true"></div>' +
	                            '           <label for="pm" class="col-sm-12 ">Paiement mensuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="pm" name="soldepm" value="'+json.nafpropre+'" disabled="true"></div>' +
	                            '           <label for="en" class="col-sm-12 ">Date effet :</label>' +
	                            '           <div class="col-sm-12"><input type="text" class="form-control" id="en" value="'+json.effet+'" disabled="true" name="effet"> </div>  ' +
	                            '           <label for="newnaf" class="col-sm-12 ">Remplacer paiement mensuel par :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="newnaf" value="" name="newnaf"> </div>  ' +
	                            
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <!--button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">MODIFIER</button-->' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }
	                
	                if(id.substr(-4, 4) == "acim"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">AMICAL : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SPC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="sc" class="col-sm-12 ">Compte actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="sc" name="soldeami" value="'+json.soldeami+'" disabled="true"></div>' +
	                            '           <label for="tdc" class="col-sm-12 ">Durée :</label>' +
	                            '           <div class="col-sm-12"><input type="text" class="form-control" id="tdc" value="'+json.duree+'" disabled="true" name="dure"> </div>  ' +
	                            '           <label for="tpc" class="col-sm-12 ">Montant :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="tpc" value="'+json.ami_propre+'" name="ami_propre"> </div>  ' +
	                            
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">MODIFIER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }

	                if(id.substr(-4, 4) == "tele"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel"> Dotation Téléphonie : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SICDT") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Dotation téléphonie actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="solde" value="'+json.tel+'" disabled="true"></div>' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Saisir :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="addnew"> </div>  ' +
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">AJOUTER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }

	                if(id.substr(-4, 4) == "carb"){
                		identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Dotation Carburant : </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SICDC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Dotation carburant actuel :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="solde" value="'+json.carb+'" disabled="true"></div>' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Saisir :</label>' +
	                            '           <div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" name="addnew"> </div>  ' +
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">AJOUTER</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
	                }

                	if(id.substr(-4, 4) == "impo"){
						div.innerHTML = '<div class="modal-header">' +
						                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						                    '<h4 class="modal-title" id="myModalLabel">IMPORTER : </h4>' +
						                    '</div><div class="modal-body">' +
						                    '<form class="form-horizontal" method="post" action="{{ route("IC") }}" enctype="multipart/form-data">' +
						                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						                    '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> '+
						                    '<div class="form-group"> ' +
						                    '<div class="col-sm-12">' +
						                    '<a class="btn-primary" style="float:right; color: white; padding: 15px; margin-right: 10px" href="{{ url($exemple) }}"> Télécharger l\'exemplaire du fichier </a>' +
						                    '</div> ' +
						                    '</div> ' +
						                    ' <div class="form-group"> ' +
						                    '<div class="col-sm-12"> ' +
						                    ' <label for="inp-type-2" class="col-sm-12">Importer : </label>' +
						                    ' <div class="col-sm-12"><input type="file" accept=".xls, .xlsx" class="form-control" id="inp-type-2" name="fichie">' +
						                    '</div> ' +
						                    '</div>' +
						                    '    </div>' +
						                    '<div class="modal-footer">   ' +
						                    '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">Valider</button>  ' +
						                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
						                    '</div>';
	            	}

	            	if(id.substr(-4, 4) == "agen"){
						div.innerHTML = '<div class="modal-header">' +
						                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						                    '<h4 class="modal-title" id="myModalLabel">AJOUTER UN PARTENAIRE : </h4>' +
						                    '</div><div class="modal-body">' +
						                    '<form class="form-horizontal" method="post" action="{{ route("AddC") }}" >' +
						                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						                    '<input type="hidden" name="agence" value="AGENCE" />' +
						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-12"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Code <i style="color:red">*</i> : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="code" required>' +
						                    '       </div> ' +
						                    '    </div>' +
						                    ' </div>' +
						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-12"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Raison Commercial : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="nom">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    ' </div>' +

						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Email : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="mail">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Téléphone : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="tel">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    ' </div>' +    
						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Règlement : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="compte">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Numéro : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="numcompte">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    ' </div>' +
						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">IFU : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="aib">' +
						                    '       </div> ' +
						                    '    </div>' +
						                    
						                    ' </div>' +
						                    ' <div class="form-group"> ' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Niveau : </label>' +
						                    '       <div class="col-sm-12">'+
						                    '          <select type="text" class="form-control" id="inp-type-2" name="niv">' +
						                    '               <option value="AG"> AGENCE GENERALE </option> ' +
						                    '               <option value="INST"> INSTITUTION </option> ' +
						                    '               <option value="B"> BANQUE </option> ' +
						                    '               <option value="COU"> COURTIER </option> ' +
						                    '          </select> ' +
						                    '       </div> ' +
						                    '    </div>' +
						                    '    <div class="col-sm-6"> ' +
						                    '       <label for="inp-type-2" class="col-sm-12">Adresse : </label>' +
						                    '       <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="adress">' +
						                    '    </div>' +
						                    ' </div>' +
						                    ' </div>' +
						                    '<div class="modal-footer">   ' +
						                    '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">Valider</button>  ' +
						                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
						                    '</div>';
	            	}
	            	
	            	if(id.substr(-4, 4) == "cace"){
						div.innerHTML = '<div class="modal-header">' +
						                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						                    '<h4 class="modal-title" id="myModalLabel">CAREC : </h4>' +
						                    '</div><div class="modal-body">' +
						                    '<form class="form-horizontal" method="post" action="{{ route("SECCC") }}" enctype="multipart/form-data">' +
						                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						                    '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> '+
						                    '<div class="form-group"> ' +
						                    '<div class="col-sm-12">' +
						                    '<a class="btn-primary" style="float:right; color: white; padding: 15px; margin-right: 10px" href="{{ route("ECCC") }}"> Télécharger le fichier à remplir </a>' +
						                    '</div> ' +
						                    '</div> ' +
						                    ' <div class="form-group"> ' +
						                    '<div class="col-sm-12"> ' +
						                    ' <label for="inp-type-2" class="col-sm-12">Importer : </label>' +
						                    ' <div class="col-sm-12"><input type="file" accept=".xls, .xlsx" class="form-control" id="inp-type-2" name="fichie">' +
						                    '</div> ' +
						                    '</div>' +
						                    '    </div>' +
						                    '<div class="modal-footer">   ' +
						                    '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">Valider</button>  ' +
						                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
						                    '</div>';
	            	}
	            	
	            	if(id.substr(-4, 4) == "amic"){
						div.innerHTML = '<div class="modal-header">' +
						                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						                    '<h4 class="modal-title" id="myModalLabel">AMICAL : </h4>' +
						                    '</div><div class="modal-body">' +
						                    '<form class="form-horizontal" method="post" action="{{ route("SEAC") }}" enctype="multipart/form-data">' +
						                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						                    '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> '+
						                    '<div class="form-group"> ' +
						                    '<div class="col-sm-12">' +
						                    '<a class="btn-primary" style="float:right; color: white; padding: 15px; margin-right: 10px" href="{{ route("EAC") }}"> Télécharger le fichier à remplir </a>' +
						                    '</div> ' +
						                    '</div> ' +
						                    ' <div class="form-group"> ' +
						                    '<div class="col-sm-12"> ' +
						                    ' <label for="inp-type-2" class="col-sm-12">Importer : </label>' +
						                    ' <div class="col-sm-12"><input type="file" accept=".xls, .xlsx" class="form-control" id="inp-type-2" name="fichie">' +
						                    '</div> ' +
						                    '</div>' +
						                    '    </div>' +
						                    '<div class="modal-footer">   ' +
						                    '<button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">Valider</button>  ' +
						                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
						                    '</div>';
	            	}

                    if(id.substr(-4, 4) == "actv"){
                        
                        identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);
	            	    
	            	    if(json.statut == '0')
                			div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Désactiver ? </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SADC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Voulez-vous vraiment désactiver le commercial ?</label>' +
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">OUI</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">NON</button></div>';
	                    else
	                        div.innerHTML = '' +
	                            '<div class="modal-header">' +
	                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
	                            '   <h4 class="modal-title" id="myModalLabel">Activer ? </h4>' +
	                            '</div>' +
	                            '<div class="modal-body">' +
	                            '   <form class="form-horizontal" method="post" action="{{ route("SADC") }}">' +
	                            '      <input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
	                            '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Voulez-vous vraiment activer le commercial ?</label>' +
	                            '      </div>       ' +
	                            '    <div class="modal-footer">   ' +
	                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">OUI</button>' +
	                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                            '    waves-light" style="float:right; color:white;">NON</button></div>';
                        
                    }
	            }

      			
	        })
	    });
	</script>

      <script>
			function afficheMenu(obj){
	
				var idMenu     = obj.id;
				var idSousMenu = 'sous' + idMenu;
				var sousMenu   = document.getElementById(idSousMenu);
			
				for(var i = 1; i <= 4; i++){
					if(document.getElementById('sousmenu' + i) && document.getElementById('sousmenu' + i) != sousMenu){
						document.getElementById('sousmenu' + i).style.display = "none";
					}
				}
				
				if(sousMenu){
					//alert(sousMenu.style.display);
					if(sousMenu.style.display == "block"){
						sousMenu.style.display = "none";
					}
					else{
						sousMenu.style.display = "block";
					}
				}
				
			}
	</script>

@endsection
@section('model')


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">

		<div class="modal-content" id="tst">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un commercial : </h4>
			</div>
			<div class="modal-body">
				
				<form class="form-horizontal" method="post" action="{{ route('AddC') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<div class="form-group">
						<label for="inp-type-2" class="col-sm-2 control-label">Code<i style="color:red">*</i> </label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="inp-type-2" name="code" required>
						</div>

					</div>
					<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label">Nom<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="nom" required>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Prénom<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="prenom" required>
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label">Email<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<input type="email" class="form-control" id="inp-type-2" name="mail" >
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Téléphone </label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="inp-type-2" name="tel" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label">Sexe<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<select type="sexe" class="form-control" id="inp-type-2" name="sexe">
										<option value="M">Masculin</option>
										<option value="F">Féminin</option>
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Adresse </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="adress" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-4 control-label">Mode de Règlement </label>
								<div class="col-sm-8">
									<select type="text" class="form-control" id="inp-type-2" name="mode" required>
										<option value="MOMO">MOMO</option>
									    <option value="BANQUE">BANQUE</option>
									    <option value="VIREMENT">VIREMENT</option>
									    <option value="CHEQUE">CHEQUE</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label">Règlement </label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="inp-type-2" name="compte" required>
										@foreach($listPayement as $pal)
										    <option value="{{ $pal->sigle }}">{{ $pal->libelle }}</option>
										@endforeach
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Numéro </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="numcompte" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label">IFU </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="aib">
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Niveau<i style="color:red">*</i>  </label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="inp-type-2" name="niv" required>
										<option value="CONS">CONSEILLER</option>
									</select>
								</div>
							</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">AJOUTER</button>
			</div>
		</div>

	</div>
	</div>

@endsection

@section('dstestyle')
<style>

.dropdown-item{
	font-family: "Open Sans", sans-serif;
  font-size: 15px;
  font-weight: 400;
  color: #333;
  display: inline-block;
  padding: 15px;
  position: relative;
}

.search input[type="text"]
	{
	  padding: 4px 10px;
      height: 52px;
	  background: none;
	  border: 0.5 none;
	  float: left;
	  line-height: 1.5em;
	  margin: 0;
	  width: 210px;
	}

	.search input[type="submit"]
	{
		padding: 4px 10px;
      height: 52px;
	  background: #f0f8ff url(assets/images/seach.png);
	  background-position: center;
	  border: 0.5 none;
	  margin: 0;
	  text-indent: 100px;
	  width: 50px;
	  background-repeat: no-repeat;
	  display: block;

	}

</style>
@endsection