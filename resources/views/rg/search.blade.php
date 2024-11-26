@forelse($list as $rg)
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name">{{$rg->codeH}}</span>
									</th>
									<td style="text-align: center; vertical-align:middle;">
										Région
									</td>
									<td style="text-align: center; vertical-align:middle;">
										{{$rg->libelleH}}
									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="{{ App\Providers\InterfaceServiceProvider::infomanageur($rg->managerH) }}">
									    {{$rg->managerH}}
								    </td>
									<td style="text-align: center; vertical-align:middle;" title="{{ App\Providers\InterfaceServiceProvider::infosup($rg->superieurH, 'CD') }}">{{$rg->superieurH}}</td>
									
									<td style="text-align: center; vertical-align:middle;">{{$rg->villeH}}</td>
									<td style="text-align: center; vertical-align:middle;">{{App\Providers\InterfaceServiceProvider::LibelleUser($rg->user_action)}}</td>
									<td style="text-align: center; vertical-align:middle;">
										@if(in_array("update_region", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-rg-{{$rg->codeH}}" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button>
									@endif

									</td>
								</tr>
								@empty
								<tr>
									<td colspan="8"><center>Pas de région enregistrer!!! </center></td>
								</tr>
								@endforelse