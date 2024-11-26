@forelse($list as $ins)
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name">{{$ins->codeH}}</span>
									</th>
									<td style="text-align: center; vertical-align:middle;">{{App\Providers\InterfaceServiceProvider::infoniveau($ins->structureH)->libelleNiveau}}
									</td>
									<td style="text-align: center; vertical-align:middle;">
										{{$ins->libelleH}}
									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="{{ App\Providers\InterfaceServiceProvider::infomanageur($ins->managerH) }}">
									    {{$ins->managerH}}
								    </td>
									<td style="text-align: center; vertical-align:middle;" title="{{ App\Providers\InterfaceServiceProvider::infosup($ins->superieurH, 'RG') }}">{{$ins->superieurH}}</td>
									<td style="text-align: center; vertical-align:middle;">{{$ins->villeH}}</td>
									<td style="text-align: center; vertical-align:middle;">{{App\Providers\InterfaceServiceProvider::LibelleUser($ins->user_action)}}</td>
									<td style="text-align: center; vertical-align:middle;">
										@if(in_array("update_insp", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-ins-{{$ins->codeH}}" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button>
									@endif

									</td>
								</tr>
								@empty
								<tr>
									<td colspan="8"><center>Pas d'inspections enregistrer!!! </center></td>
								</tr>
								@endforelse