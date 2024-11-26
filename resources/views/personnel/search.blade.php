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