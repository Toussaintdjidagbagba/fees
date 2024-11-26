
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
