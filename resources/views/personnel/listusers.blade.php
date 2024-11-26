@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 
 
		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Liste des utilisateurs NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">
					@if(in_array("add_user", session("auto_action")))
					<button type="button" style="margin-left: 30px;" class="btn btn-icon btn-icon-left btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#add" ><i class="ico fa fa-plus" ></i>Ajouter</button>
					@endif

                    <!------------------------------------------>
					<form class="form-horizontal" action="" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-right: 30px; margin-top: -45px; float: right;">
								<input class=" form-control" type="text" id="search" placeholder="Rechercher un utilisateur.."  >
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
							xhr.open("GET", "{{route('listU')}}?check="+search+"&rec=1", true);
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
							xhr.open("GET", "{{route('listU')}}?check="+search+"&rec=1", true);
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
							xhr.open("GET", "{{route('listU')}}?check="+search+"&rec=1", true);
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
									<th>Identifiant</th>
									<th data-priority="1">Nom</th>
									<th data-priority="3">Prénom(s)</th>
									<th data-priority="1">Téléphone</th>
									<th data-priority="3">Email</th>
									<th data-priority="3">Rôle</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse($list as $user)
								<tr>
									<th><span class="co-name">{{$user->login}}</span></th>
									<td>{{$user->nom}}</td>
									<td>{{$user->prenom}}</td>
									<td>{{$user->tel}}</td>
									<td>{{$user->mail}}</td>
									<td>{{App\Providers\InterfaceServiceProvider::LibelleRole($user->Role)}}</td>
									<td>
                                    @if(in_array("update_user", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">
										<a href="/modif-users-{{$user->idUser}}" style="color:white;"> <i class="ico fa fa-edit"></i></a> 
										
									</button>
									@endif

									@if(in_array("delete_user", session("auto_action")))
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-users-{{$user->idUser}}" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									@endif

									@if(in_array("reset_user", session("auto_action")))
									<button type="button" title="Réinitialiser"  class="btn btn-warning btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/reinitialiser-users-{{$user->idUser}}" style="color:white;"> <i class="ico fa fa-circle-o-notch"></i></a></button>
									@endif

									@if(in_array("status_user", session("auto_action")))
									@if($user->statut == "0")
									<button type="button" title="Désactivé ?"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/desactivé-users-{{$user->idUser}}" style="color:white;"> <i class="fa fa-toggle-on" aria-hidden="true"></i></a></button>
									@endif
									@if($user->statut == "1")
									<button type="button" title="Activé ?"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light" style="background-color:grey"><a href="/activé-users-{{$user->idUser}}" style="color:white;"> <i class="fa fa-toggle-off" aria-hidden="true"></i></a></button>
									@endif
									@endif
									
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="7"><center>Pas d'utilisateur enregistrer!!!</center> </td>
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

<div class="modal fade" id="modif" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modifier l'utilisateur : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('ModifU') }}">
							@if(isset($modifieruser))
							<input type="hidden" name="id" value="{{ $modifieruser->idUser }}" />
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Identifiant : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifieruser->login }}"  name="login">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Nom : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifieruser->nom }}" name="nom" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Prénom : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifieruser->prenom }}" name="prenom" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Sexe : </label>
								<div class="col-sm-9">
									<select type="text" class="form-control" id="inp-type-1" name="sexe" >
										<option value="{{ $modifieruser->sexe }}">{{ App\Providers\InterfaceServiceProvider::sexe($modifieruser->sexe) }}</option>
										@if( $modifieruser->sexe == 'M')
											<option value="F">Féminin</option>
										@else
											<option value="M">Masculin</option>
										@endif
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Téléphone : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifieruser->tel }}" name="tel">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-3 control-label">Adresse : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifieruser->adresse }}" name="adress">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-3 control-label">Email : </label>
								<div class="col-sm-9">
									<input type="email" class="form-control" id="inp-type-2" value="{{ $modifieruser->mail }}" name="mail">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-3 control-label">Autres : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-4" value="{{ $modifieruser->other }}" name="autres">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-3 control-label">Rôle : </label>
								<div class="col-sm-9">
									<select type="text" class="form-control" id="inp-type-4" name="role">
										<option value="{{ $modifieruser->Role }}">{{App\Providers\InterfaceServiceProvider::LibelleRole($modifieruser->Role)}}</option>
										@foreach($allRole as $role)
											@if($role->idRole != $modifieruser->Role)
												<option value="{{ $role->idRole }}">{{ $role->libelle }}</option>
											@endif
										@endforeach

									</select> 
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-3 control-label">Image : </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-4" name="photo" >
								</div>
							</div>
							@endif
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Mise à jour</button>
				</form>
			</div>
		</div>
	</div>
	</div>


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un utilisateur : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('AddU') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Identifiant </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1"  name="login">
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Email </label>
								<div class="col-sm-4">
									<input type="email" class="form-control" id="inp-type-2" name="mail" required>
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Nom </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1"  name="nom" required>
								</div>
							
								<label for="inp-type-1" class="col-sm-2 control-label">Prénom </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1" name="prenom" >
								</div>
							</div>
							
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Téléphone </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1" name="tel">
								</div>
	
								<label for="inp-type-1" class="col-sm-2 control-label">Adresse </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-1" name="adress">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Sexe </label>
								<div class="col-sm-10">
									<select type="text" class="form-control" id="inp-type-1" name="sexe" >
										<option value="F">Féminin</option>
										<option value="M">Masculin</option>	
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-2 control-label">Rôle </label>
								<div class="col-sm-10">
									<select type="text" class="form-control" id="inp-type-4" name="role">
										@foreach($allRole as $role)	
												<option value="{{ $role->idRole }}">{{ $role->libelle }}</option>
										@endforeach
									</select> 
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-2 control-label">Mode </label>
								<div class="col-sm-10">

									<select type="text" class="form-control" id="inp-type-4" name="auth">
										<option value="direct">Authentification direct</option>
										<option value="sys">Authentification système</option>
									</select> 
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-4" class="col-sm-2 control-label">Autres </label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="inp-type-4" name="autres">
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