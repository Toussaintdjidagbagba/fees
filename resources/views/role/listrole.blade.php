@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des rôles NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4> 
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
					@if(in_array("add_role", session("auto_action")))
					<button type="button" style="margin-left:30px" class="btn btn-icon btn-icon-left btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#add" ><i class="ico fa fa-plus" ></i>Ajouter</button>
					@endif

					<!------------------------------------------>
					<form class="form-horizontal" action="" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-right: 30px; margin-top: -45px; float: right;">
								<input class=" form-control" type="text" id="search" placeholder="Rechercher un rôle.."  >
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
							xhr.open("GET", "{{route('listR')}}?check="+search+"&rec=1", true);
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
							xhr.open("GET", "{{route('listR')}}?check="+search+"&rec=1", true);
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
							xhr.open("GET", "{{route('listR')}}?check="+search+"&rec=1", true);
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
				<div class="box-content" id="data">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th>Code</th>
									<th data-priority="1">Libelle</th>
									<th data-priority="3">Action Utilisateur</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody>

								@forelse($list as $role)
								<tr>
									<th><span class="co-name">{{$role->code}}</span></th>
									<td>{{$role->libelle}}</td> 
									<td>{{App\Providers\InterfaceServiceProvider::LibelleUser($role->user_action)}}</td>
									<td>
                                    @if(in_array("update_role", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-roles-{{$role->idRole}}" style="color:white;"><i class="ico fa fa-edit"></i></a>
									</button>
									@endif

									@if(in_array("delete_role", session("auto_action")))
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-roles-{{$role->idRole}}" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									@endif

                                    @if(in_array("menu_role", session("auto_action")))
									<button type="button" title="Menu"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/menu-roles-{{$role->idRole}}" style="color:white;"><i class="ico fa fa-bars"></i></a> 
									</button>
									@endif

									</td>
								</tr>
								@empty
								<tr>
									<td colspan="4"><center>Pas de rôle enregistrer!!! </center> </td>
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
      </script>
@endsection
@section("model")


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un rôle : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('AddR') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Code </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1"  name="code">
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Libelle </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" name="lib" required>
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