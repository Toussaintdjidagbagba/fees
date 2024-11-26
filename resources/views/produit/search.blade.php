								@forelse($list as $prod)
								<tr>
									<th><span class="co-name">{{$prod->idProduit}}</span></th>
									<td>{{$prod->libelle}}</td>
									<td>{{$prod->codeProduit}}</td>
									<td>{{App\Providers\InterfaceServiceProvider::LibelleUser($prod->user_action)}}</td>
									<td>
                                    @if(in_array("update_prod", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> <a href="/modif-produit-{{$prod->idProduit}}" style="color:white;"><i class="ico fa fa-edit"></i> </a></button>
									@endif

									@if(in_array("delete_prod", session("auto_action")))
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-produit-{{$prod->idProduit}}" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									@endif
									@if(in_array("parameterize_taux_menu", session("auto_action")))
										<button type="button" title="Paramétrer"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/add-Taux-{{$prod->idProduit}}" style="color:white;"><i class="ico fa fa-bars"></i> </button>
									@endif

									</td>
								</tr>
								@empty
								<tr>
									<td colspan="5"><center>Pas de produit enregistrer!!! </center></td>
								</tr>
								@endforelse
