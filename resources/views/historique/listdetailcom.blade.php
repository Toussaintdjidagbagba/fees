@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
			 Historique des Commissions de  {{ $nomapp }} ( {{ $mois }} ) : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				
					<!------------------------------------->
				<div class="row small-spacing"> 
			        <div class="col-xs-12">
				        <div class="box-content" >

							<div class="form-group">
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Code Apporteur : </label>
									<div class="col-sm-12">
									    <input type="text" class="form-control" id="inp-type-1"  value="{{ $comp['Agent'] }}"> </div>
							    </div>
								<div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Nom et prénom Apporteur : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ $nomapp }}"> </div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Libellé Règlement : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ $comp['libCompte'] }}"> </div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Numéro Règlement : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ $comp['numCompte'] }}"> </div>
							    </div>
							</div> 
							
							<div class="form-group">
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Taux AIB : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ $taux}} %"> </div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Taux Carec : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['tauxCarec'] , 0, '.', ' ')." %" }}"></div>
							    </div>
							    <div class="col-sm-6">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Montant Amical : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['montantAmical'] , 0, '.', ' ') }}  CFA"></div>
							    </div>
							</div>
							
							<div class="form-group">
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Commission Brute : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['compteMoisCalculer']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Encadrement : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['compteEncadrementMoisCalculer']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Autre Commission : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['AutreCommissionMoisCalculer']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Bonus : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['bonus']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
							   
							</div>
							<div class="form-group">
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Fixe : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['fixe']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Commission à déduire ce mois : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['compteBloquer']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Dotation Téléphonique : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['dotationTelephonie']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Dotation Carburant : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format(($comp['dotationCarburant']), 0, '.', ' ')." CFA" }}"> </div>
							    </div>
							   
							</div>
							<div class="form-group">
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Avance Com. due : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['avancesancien'] , 0, '.', ' ') }}  CFA"></div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Avance Com. Remboursée : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['recentrembourcer'] , 0, '.', ' ') }}  CFA"></div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Anticiper : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['anticiper'] , 0, '.', ' ') }}"></div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Echéance restant : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['duree'] , 0, '.', ' ') }}"></div>
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">AIB : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['aibMoisCalculer'], 0, '.', ' ') }} CFA"></div>
							    </div>
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Prelèvement : </label>
									<div class="col-sm-12"> 
									<input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['retenue'] , 0, '.', ' ') }} CFA"></div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Carec : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['traceCarec'] , 0, '.', ' ') }}  CFA"></div>
							    </div>
								<div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Montant Amical : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['tracesAmical'] , 0, '.', ' ') }}  CFA"></div>
							    </div>
							    
							</div>
							<div class="form-group">
							    <div class="col-sm-12">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Commission Nette : </label>
								    <div class="col-sm-12"> 
								    <input type="text" class="form-control" id="inp-type-1"  value="{{ number_format( $comp['compte'] , 0, '.', ' ')." CFA" }}"></div>
							    </div>
							    
							    
							</div>
							<div class="form-group">
							    <?php $equip = App\Providers\InterfaceServiceProvider::infohierarchie($comm->codeEquipe) ?>
								<div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Code Chef Equipe : </label>
									<div class="col-sm-12"> 
									    @if( $equip == "" || $equip == "Par défaut" || $equip == "inconnue" ) 
									    <input type="text" class="form-control" id="inp-type-1"  value="0 ">
									    @else 
									    <input type="text" class="form-control" id="inp-type-1"  value="{{ $equip->codeCom }}"> @endif
									</div>
							    </div>
								<div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Nom Chef Equipe : </label>
								    <div class="col-sm-12"> @if( $equip == "" || $equip == "Par défaut" || $equip == "inconnue" ) 
								    <input type="text" class="form-control" id="inp-type-1"  value="0"> @else <input type="text" class="form-control" id="inp-type-1"  value="{{ $equip->nomCom }} {{ $equip->prenomCom }}"> @endif </div>
							    </div>
							    <?php $insp = App\Providers\InterfaceServiceProvider::infohierarchieNonE($comm->codeInspection) ?>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Code Inspecteur : </label>
								    <div class="col-sm-12"> @if( $insp == "" || $insp == "Par défaut" || $insp == "inconnue" ) 
								    <input type="text" class="form-control" id="inp-type-1"  value="0"> @else <input type="text" class="form-control" id="inp-type-1"  value=" {{ $insp->codeCom }}"> @endif </div>
							    </div>
							    <div class="col-sm-3">
								    <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-size: 14px; font-weight: bold;" class="col-sm-12">Nom Chef Inspecteur  </label>
								    <div class="col-sm-12"> @if( $insp == "" || $insp == "Par défaut" || $insp == "inconnue" ) 
								    <input type="text" class="form-control" id="inp-type-1"  value="0">
								    @else <input type="text" class="form-control" id="inp-type-1"  value="{{ $insp->nomCom }} {{ $insp->prenomCom }}"> @endif  </div>
							    </div>
							</div>
							
				        </div>
			        </div>
		        </div>
				<!------------------------------------->
				
				<div class="row small-spacing">  
					
			<div class="col-xs-12">
				<div class="box-content" >
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped" style="font-size: 10px;">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Police</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Client</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Quittance</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Période</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Catégorie</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Base de Commission</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Commission</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Taux AIB</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">AIB</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Commission Nette</th>
								</tr>
							</thead>
							<tbody id="data">
							    <?php 
							        $sombase = 0;
							        $somcom = 0;
							        $somaib = 0;
							        $somnette = 0;
							        $sombasec = 0;
    							    $somcomc = 0;
    							    $somaibc = 0;
    							    $somnettec = 0;
							    ?>
								@forelse($list as $com)
								<tr>
							        <th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->NumPolice }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->Client}}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->NumQuittance }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->Statut }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">CONS</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format( $com->BaseCommission , 0, '.', ' ')}}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format( $com->MontantConseiller , 0, '.', ' ')}} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $taux}} %</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format( round(($com->MontantConseiller * $taux) / 100) , 0, '.', ' ')}} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format( round($com->MontantConseiller - (($com->MontantConseiller * $taux) / 100)) , 0, '.', ' ')}} CFA</th>
								    <?php 
    							        $sombase += $com->BaseCommission;
    							        $somcom += $com->MontantConseiller;
    							        $somaib += round(($com->MontantConseiller * $taux) / 100);
    							        $somnette += round($com->MontantConseiller - (($com->MontantConseiller * $taux) / 100));
    							    ?>
								</tr>
								@empty
								<tr>
									<td colspan="10"><center>Pas de commission disponible!!! </center></td>
								</tr>
								@endforelse
								
								<tr>
									<th colspan="4" data-priority="1" style="vertical-align:middle; text-align: center;"> </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;"> Sous Total Catégorie :</th>
									
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($sombase , 0, '.', ' ')}}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somcom , 0, '.', ' ') }} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $taux}} %</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somaib , 0, '.', ' ')}} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somnette , 0, '.', ' ')}} CFA</th>
								</tr>
								<tr><th colspan="10" data-priority="1" style="vertical-align:middle; text-align: center;"> </th></tr>
								<tr><th colspan="10" data-priority="1" style="vertical-align:middle; text-align: center;"> </th></tr>
								@if($niveau <> "CONS")
    								 
    							    @forelse($detailCom as $comm)
    							    <tr>
    								    <th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comm->NumPolice }}</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comm->Client}}</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comm->NumQuittance }}</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comm->Statut }}</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $niveau}}</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comm->BaseCommission , 0, '.', ' ')}}</th>
    									<?php 
    									    if($niveau == "CEQP")
    									        $commission = $comm->MontantCEQ;
    									    if($niveau == "INS")
                                                $commission = $comm->MontantInspecteur;
                                            if($niveau == "RG")
                                                $commission = $comm->MontantRG;
    									?>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($commission, 0, '.', ' ')}} CFA</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $taux}} %</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format(round(($commission * $taux) / 100), 0, '.', ' ')}} CFA</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format( round($commission - (($commission * $taux) / 100)) , 0, '.', ' ')}} CFA</th>
    								    <?php 
        							        $sombasec += $comm->BaseCommission;
        							        $somcomc += $commission;
        							        $somaibc += round(($commission * $taux) / 100);
        							        $somnettec += round($commission - (($commission * $taux) / 100));
        							    ?>
        							  </tr>
        							@empty
    								<tr>
    									<td colspan="10"><center>Pas de commission d'encadrement disponible!!! </center></td>
    								</tr>
    								@endforelse
    							    <tr>
    									<th colspan="4" data-priority="1" style="vertical-align:middle; text-align: center;"> </th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;"> Sous Total Catégorie :</th>
    									
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($sombasec, 0, '.', ' ')}} </th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somcomc, 0, '.', ' ') }} CFA</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $taux}} %</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somaibc, 0, '.', ' ')}} CFA</th>
    									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somnettec, 0, '.', ' ')}} CFA</th>
    								</tr>
								@endif
								<tr>
									<th colspan="4" data-priority="1" style="vertical-align:middle; text-align: center;"> </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;"> Total Catégorie :</th>
									
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($sombase + $sombasec , 0, '.', ' ')}}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somcom + $somcomc , 0, '.', ' ')}} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $taux}} %</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somaib + $somaibc , 0, '.', ' ')}} CFA</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($somnette + $somnettec , 0, '.', ' ')}} CFA</th>
								</tr>
							</tbody>
						</table>
						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
						

					</div> 
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-6 col-xs-12 -->
		</div>
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
	<script src="https://code.iconify.design/2/2.1.2/iconify.min.js"></script>
	
@endsection
@section("dstestyle")
    <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
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
	
	.btn {
      background: #6495ed;
      background-image: -webkit-linear-gradient(top, #6495ed, #2980b9);
      background-image: -moz-linear-gradient(top, #6495ed, #2980b9);
      background-image: -ms-linear-gradient(top, #6495ed, #2980b9);
      background-image: -o-linear-gradient(top, #6495ed, #2980b9);
      background-image: linear-gradient(to bottom, #6495ed, #2980b9);
      -webkit-border-radius: 7;
      -moz-border-radius: 7;
      border-radius: 7px;
      text-shadow: 7px 22px 15px #8a7c8a;
      font-family: Arial;
      color: #ffffff;
      font-size: 12px;
      padding: 10px 20px 10px 20px;
      text-decoration: none;
    }
    
    .btn:hover {
      background: #212f68;
      background-image: -webkit-linear-gradient(top, #212f68, #6495ed);
      background-image: -moz-linear-gradient(top, #212f68, #6495ed);
      background-image: -ms-linear-gradient(top, #212f68, #6495ed);
      background-image: -o-linear-gradient(top, #212f68, #6495ed);
      background-image: linear-gradient(to bottom, #212f68, #6495ed);
      text-decoration: none;
    }

</style>
@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection