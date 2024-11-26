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