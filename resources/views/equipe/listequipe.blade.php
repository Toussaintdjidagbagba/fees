@extends('layouts.template')

@section('content')

<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;">@include('flash::message')</center></div>

@if(in_array("add_equipe", session("auto_action")))
	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Ajouter une équipe :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="{{ route('AddE') }}"  enctype="multipart/form-data">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> <!-- Limite 5Mo -->
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="codeh" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Libellé : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-1"  name="lib">
								</div>
							    </div>			
							</div>
							<div class="form-group">
								
							    <div class="col-sm-6">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Chef d'équipe :</label>
								<div class="col-sm-12">
									<select type="number" class="chosen form-control" id="inp-type-1" name="manageur" >
										@if($com_manageur != "")
											<option value="{{$com_manageur->codeCom}}">{{ $com_manageur->nomCom }} {{ $com_manageur->prenomCom }}</option>
										@endif
										@foreach($listmag as $mag)
										    <option value="{{$mag->codeCom}}">{{ $mag->nomCom }} {{ $mag->prenomCom }}</option>
										@endforeach
									</select>
								</div>
								</div>
								<div class="col-sm-6">
									<label for="inp-type-2" class="col-sm-12 ">Supérieur hiérarchie :</label>
									<div class="col-sm-12">
										<select type="text" class=" chosen form-control" id="inp-type-1" name="sup" >
											@foreach($listsup as $sup)
											    <option value="{{$sup->codeH}}">{{ App\Providers\InterfaceServiceProvider::infomanageur($sup->managerH) }}</option>
										    @endforeach
										</select>
									</div>
							    </div>
							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Référence : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="ref" value="" required>
										</div>
									</div>								
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Description : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="desc" value="" required>
										</div>
									</div>								
								

							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> La note : </label>
										<div class="col-sm-12">
											<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="note" required>
										</div>
								
								</div>
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Date effet : </label>
										<div class="col-sm-12">
											<input type="date" class="form-control" id="inp-type-1"  name="dateeffet" value="{{ date('d/m/Y') }}" required>
										</div>
								</div>
							</div>


							
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-6">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Enregistrer
				                    </button>
							    </div>
							</div>
							<div class="form-group" style="display: none;" id="modify">
							    <div class="col-sm-12">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; margin-top: 20px; margin-left: 15px; width: 25%;">Mettre à jour
				                    </button>
							    </div>
							</div>
					</form>	
				</div>
				<!-- /.box-content -->
			</div>
		</div>
	</div>
</div>
</div>
@endif

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des équipes NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing"> 
					<!------------------------------------------>
					<form class="form-horizontal" action="" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-right: 30px; float: right;">
								<input class=" form-control" type="text" id="search" placeholder="Rechercher "  >
							</div>
						</div>
					</form>
					<script>		
						
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
							var oSelect = document.getElementById("data");
							
							oSelect.innerHTML = sData;
						}
						
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "{{route('listE')}}?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keydown", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "{{route('listE')}}?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keyup", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "{{route('listE')}}?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);
						
					</script>
					
                    <!------------------------------------------->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th class="textcenter">Code</th>
									<th class="textcenter" data-priority="1">Structure</th>
									<th class="textcenter" data-priority="1">Libellé</th>
									<th class="textcenter" data-priority="1">Chef d'équipe</th>
									<th class="textcenter" data-priority="1">Supérieur</th>
									
									<th class="textcenter" data-priority="3">Action Utilisateur</th>
									<th style="text-align: center; vertical-align:middle;" data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody id="data">

								@forelse($list as $eqp)
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name">{{$eqp->codeH}}</span>
									</th>
									<td style="text-align: center; vertical-align:middle;">CEQP
									</td>
									<td style="text-align: center; vertical-align:middle;">
										{{$eqp->libelleH}}
									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="{{ App\Providers\InterfaceServiceProvider::infomanageur($eqp->managerH) }}">
									    {{$eqp->managerH}}
								    </td>
									<td style="text-align: center; vertical-align:middle;" title="{{ App\Providers\InterfaceServiceProvider::infosup($eqp->superieurH) }}">{{$eqp->superieurH}}</td>
									
									<td style="text-align: center; vertical-align:middle;">{{App\Providers\InterfaceServiceProvider::LibelleUser($eqp->user_action)}}</td>
									<td style="text-align: center; vertical-align:middle;">
										@if(in_array("update_equipe", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-equipe-{{$eqp->codeH}}" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button> 
									@endif

									</td>
								</tr>
								@empty
								<tr>
									<td colspan="8"><center>Pas d'équipe enregistrer!!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
						{{$list->links()}}

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

@section("js")
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
          
          function passer($val) {
			  var x = document.getElementById("modify");
			  var y = document.getElementById("Ajouter"); 
			  if (y.style.display == "block") {
			  	y.style.display = "none";
			    x.style.display = "block";
			  } else {
			    x.style.display = "none";
			    y.style.display = "block"
			  }
			}


    </script>
@endsection
@section("model")

@endsection
@section("dstestyle")
  <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection