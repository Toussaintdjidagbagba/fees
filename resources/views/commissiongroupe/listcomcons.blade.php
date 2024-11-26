@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card"> 
			
			<h4 class="box-title with-control">
			    <?php setlocale(LC_ALL, 'fr_FR', 'fra_FRA') ?>
				Liste Commissions Groupe {{utf8_encode(strtoupper(strftime('%B %Y'))) }} calculées NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;" id="infoconfirme"> @include('flash::message')</center></div>
				<div class="row small-spacing">
					<div id="ref">
						Ref <b style="color:#001e60">SP</b>/{{$sigle}}
					</div>
					@if(sizeof($list) != 0)
					    @if(in_array("confirm_sp_com", session("auto_action")))
					        <a class="btn-warning dropdown-item autrecom" style="box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; float:right; padding: 10px; margin-right: 30px" href="#autrCom"
					            data-toggle="modal" data-id="1conf" title="">Confirmer <i class="fa fa-check-circle fa-spin" aria-hidden="true"></i> </a>
					    @endif
					   
					    @if(in_array("other_operation_sp_com", session("auto_action")))
					    <a class="btn-warning dropdown-item autrecom" style="box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; padding: 10px; margin-right: 30px ; float:right" href="#autrCom"
						   data-toggle="modal" data-id="1impo" title="">Autres Opérations</a>
					    @endif
					    @if(in_array("rejet_com", session("auto_action")))
    					<a class="btn-danger dropdown-item autrecom" id="valid" href="#autrCom"
    					   data-toggle="modal" data-id="spreje" title="">Rejeter <i class="fa fa-ban" aria-hidden="true"></i> </a>
    					@endif
					@else
    					@if(in_array("confirm_sp_com", session("auto_action")))
    					<a class="btn-warning dropdown-item autrecom" style="box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; float:right; padding: 10px; margin-right: 30px" href="#autrCom"
    					   data-toggle="modal" data-id="1erro" title="">Confirmer <i class="fa fa-check" aria-hidden="true"></i> </a>
    					@endif
    					@if(in_array("other_operation_sp_com", session("auto_action")))
    					<a class="btn-warning dropdown-item autrecom" style="box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; padding: 10px; margin-right: 30px; float:right;" href="#autrCom"
    						   data-toggle="modal" data-id="1erro" title="">Autres Opérations</a>
    					@endif
    					@if(in_array("rejet_com", session("auto_action")))
    					<a class="btn-danger dropdown-item autrecom" id="valid" href="#autrCom"
    					   data-toggle="modal" data-id="1erro" title="">Rejeter <i class="fa fa-ban" aria-hidden="true"></i> </a>
    					@endif
					@endif
					<!------------------------------------------>
					<form class="form-horizontal" action="{{route('listComG')}}" method="GET" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-12 col-xs-12 col-lg-4" style="margin-right: 0px; float: right;">
								<input class=" form-control" type="hidden" name="rec" value="1">
								<div class="search col-sm-12">
									<input class=" form-control" type="text" name="check" placeholder="{{$search}} ">
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
					
                    <!------------------------------------------->

			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Code Commission</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Quittance</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Police</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Convention</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Produit</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Base Commission</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Montant Commission</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Apporteur</th>
									<th data-priority="6" style="vertical-align:middle;text-align: center;">Actions</th>
								</tr>
							</thead>
							<tbody id="data">
								<?php $i = 1; ?>
								@forelse($list as $com)
								<tr>
									<td style="vertical-align:middle; text-align: center;">{{$com->Commission}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->Quittance}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->Police}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->conv}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->Produit}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->base}}</td>
									@if($com->mont != $com->montSun && $com->sch != "NOUVEAU" && $com->montSun != null)
										<td style="vertical-align:middle; text-align: center; background: #ffb0b0;" title="Commission non concordante. Montant Sunshine ({{$com->montSun}})">
										  	{{ number_format($com->mont, 0, '.', ' ')  }}
										</td>
									@else
										<td style="vertical-align:middle; text-align: center;">
											{{number_format($com->mont, 0, '.', ' ') }}
										</td>
									@endif
									<!--td style="vertical-align:middle; text-align: center;">{{$com->Garantie}}</td-->
									<td style="vertical-align:middle; text-align: center;" title="{{ App\Providers\InterfaceServiceProvider::infomanageur($com->Commercial) }}">{{$com->Commercial}}</td>
									<!--td style="vertical-align:middle; text-align: center;">

										<div class="btn-group">
											<button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
											</button>
											<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: -80px; right: auto; transform: translate3d(0px, 38px, 0px);">
												
												<?php 
												    $comp = App\Providers\InterfaceServiceProvider::RecupCompte($com->Commercial);
													$comptearray = array('com' => $com->Commission, 'autresolde' => $comp->AutreCommissionMoisCalculer);
													$comptejson = json_encode($comptearray);

													$comptearraybonus = array('com' => $com->Commission, 'bonussolde' => $comp->bonus);
													$comptejsonbonus = json_encode($comptearraybonus);

													$comptearrayretenue = array('com' => $com->Commission, 'retenue' => $comp->retenue);
													$comptejsonretenue = json_encode($comptearrayretenue);
													
													
												?>
												@if(in_array("other_com_sp", session("auto_action")))
												<span>
													<a class="dropdown-item autrecom" href="#autrCom" data-toggle="modal" data-id="{{$comptejson}}autr" title="">Autres Commissions</a>
												</span>
												@endif
												@if(in_array("bonus_com_sp", session("auto_action")))
												<span>
													<a class="dropdown-item autrecom" href="#autrCom" data-toggle="modal" data-id="{{$comptejsonbonus}}bonu" title="">Bonus</a>
												</span>
												@endif
												<br>
												@if(in_array("retenue_com_sp", session("auto_action")))
												<span>
													<a class="dropdown-item autrecom" href="#autrCom" data-toggle="modal" data-id="{{$comptejsonretenue}}rete" title="">Retenue</a>
												</span>
												@endif
												<br>
												
											</div>
										</div>
										
									</td-->
								</tr>
								<?php $i++; ?>
								@empty
								<tr>
									<td colspan="12"><center>Commission groupe indisponible !!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
						{{$list->links()}}
						<br><br><br><br><br><br><br><br>

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
    <script type="text/javascript">
	    $(function () {
	        $(".autrecom").click(function () {
	            var id = $(this).data('id');
	            var div = document.getElementById('tst');
	            if(id.substr(-4, 4) == "autr"){
	            	identifiant = id.slice(0, id.length - 4);
	            	var json = JSON.parse(identifiant);

	            	div.innerHTML = '<div class="modal-header">' +
						'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						'<h4 class="modal-title" id="myModalLabel">Imputer une autre commission : </h4>' +
						'</div><div class="modal-body">' +
						'<form class="form-horizontal" method="post" action="{{ route("SAC") }}">' +
						'<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
						'<input type="hidden" name="codecom" value="'+json.com+'" /> ' +
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
                        '<form class="form-horizontal" method="post" action="{{ route("SBC") }}">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                        '<input type="hidden" name="codecom" value="'+json.com+'" /> ' +
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
                        '<form class="form-horizontal" method="post" action="{{ route("SDC") }}">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                        '<input type="hidden" name="codecom" value="'+json.com+'" /> ' +
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

                if(id.substr(-4, 4) == "impo"){
                div.innerHTML = '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">Autres Opérations : </h4>' +
                    '</div><div class="modal-body">' +
                    '<form class="form-horizontal" method="post" action="{{ route("SIAC") }}" enctype="multipart/form-data">' +
                    '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> '+
                    '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                    '<div class="form-group"> ' +
                    '<div class="col-sm-12">' +
                    '<a class="btn-primary" style="float:right; color: white; padding: 15px; margin-right: 10px" href="{{ route("IAC") }}"> Télécharger le fichier à remplir </a>' +
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

                if(id.substr(-4, 4) == "conf"){
                    div.innerHTML = '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel">Confirmation : </h4>' +
                        '</div><div class="modal-body">' +
                        ' <div class="form-group"> ' +
                        '<div class="col-sm-12" style="font-size:20px" id="infoconfirme"> ' +
                        '  Voulez-vous vraiment confirmer les commissions calculées ? <br><br> ' +
                        '</div>' +
                        '</div>    </div>' +
                        '<div class="modal-footer">   ' +
                        '<button type="button" id="ferm" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;"><a id="validerreglement" onClick="validecomm();" href="#">' +
                        'Confirmer <i class="ico fa fa-check"></i></a></button>  ' +
                        ' <button type="button" id="ferm" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                        '</div>';
                }

                if(id.substr(-4, 4) == "erro"){
                    div.innerHTML = '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel">Information : </h4>' +
                        '</div><div class="modal-body">' +
                        ' <div class="form-group"> ' +
                        '<div class="col-sm-12" style="font-size:20px"> ' +
                        '  Pas de commission disponible !!! <br><br> ' +
                        '</div>' +
                        '</div>    </div>' +
                        '<div class="modal-footer">   ' +
                        ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                        '</div>';
                }
                
                if(id.substr(-4, 4) == "reje"){
                	identifiant = id.slice(0, id.length - 4);
    
                    div.innerHTML = '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel">Rejeter : </h4>' +
                        '</div><div class="modal-body">' +
                        '<form class="form-horizontal" method="post" action="{{ route("RCSPG") }}">' +
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}" />' +
                            
                        '<div class="form-group"> ' +
                        '<div class="col-sm-12" style="font-size:20px"> ' +
                    	' <label for="inp-type-2" class="col-sm-12">Motif du rejet : </label>' +
    					'<div class="col-sm-12"><input type="text" maxlength="300" class="form-control" id="inp-type-2" name="motif">' +
    					'</div> ' +
                        '</div>' +
                        
                        '<button type="submit" class="btn btn-danger btn-sm waves-effect waves-light" style="float:left; margin-left:30px; margin-top:15px; color:white;">' +
    					'Rejeter <i class="ico fa fa-check"></i></button>' +
                        '</div> '+
                        '</div>' +
                        '<div class="modal-footer">   ' +
                        
                        ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                        '</div> </form>';
                }
                
               

            });
	    });
	    
	    function getXMLHttpRequest() {
              var xhr = null;
              
              if (window.XMLHttpRequest || window.ActiveXObject) {
                if (window.ActiveXObject) {
                  try {
                    xhr = new ActiveXObject("Msxml2.XMLHTTP");
                  } catch(e) {
                    xhr = new ActiveXObject("Microsoft.XMLHTTP");
                  }
                } else {
                  xhr = new XMLHttpRequest(); 
                }
              } else {
                alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
                return null;
              }
              
              return xhr;
            }
            
        function readData(sData) {
              //alert(sData);
              var oSelect = document.getElementById("infoconfirme");
              
              oSelect.innerHTML = '<div class="alert alert-info" role="alert">'+sData+'</div>';
            }
            
        function validecomm(){
            readData("Lancement des calcules.."); 
            var d = document.getElementById("validerreglement");
            d.onclick = null; d.innerHTML = "Veuillez patienter..";
            var f = document.getElementById("ferm");
            f.style.display="none";
            
            document.getElementById("infoconfirme").innerHTML = "En cours de traitement des commissions. Le processus peut prendre quelques minutes. Veuillez patienter jusqu'à la fin.";
            
                var xhr = getXMLHttpRequest(); 
                  xhr.open("GET", "{{route('listCommConfirmG')}}", true);
                  xhr.send(null);
                  xhr.onreadystatechange = function() {
                      if (xhr.readyState == 1 || xhr.readyState == 2 || xhr.readyState == 3) 
                        readData("En cours de traitement des commissions. Le processus peut prendre quelques minutes. Veuillez patienter jusqu'à la fin. ");
                      
                          
                      if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) { 
                        readData(xhr.responseText); }
                      
                  };
        }
	</script>
	
@endsection
@section('model')
<div class="modal fade" id="autrCom" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tst">

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
#ref{
	float:left; margin-left: 30px; font-size: 20px;
}
#valid{
	box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; float:right; padding: 10px; margin-right: 30px
}
.search input[type="text"]
	{
	  padding: 4px 10px;
      height: 42px;
	  background: none;
	  border: 0.5 none;
	  float: left;
	  line-height: 1.5em;
	  margin: 0;
	  width: 200px;
	}

	.search input[type="submit"]
	{
		padding: 4px 10px;
      height: 42px;
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