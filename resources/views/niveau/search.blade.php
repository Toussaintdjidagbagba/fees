

								@forelse($list as $niveau)
								<tr>
									<th><span class="co-name">{{$niveau->codeNiveau}}</span></th>
									<td>{{$niveau->libelleNiveau}}</td>
									<td>{{App\Providers\InterfaceServiceProvider::LibelleUser($niveau->user_action)}}</td>
									<td>
                                    @if(in_array("update_niv", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> <a href="/modif-niveaux-{{$niveau->codeNiveau}}" style="color:white;"><i class="ico fa fa-edit"></i></a>
									</button>
									@endif

									@if(in_array("delete_niv", session("auto_action")))
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-niveaux-{{$niveau->codeNiveau}}" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									</td>
									@endif
								</tr>
								@empty
								<tr>
									<td colspan="4"><center>Pas de niveaux enregistrer!!! </center></td>
								</tr>
								@endforelse
