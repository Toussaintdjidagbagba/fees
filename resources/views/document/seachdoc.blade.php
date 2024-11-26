<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Apporteur</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Fiche de paie</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Détail</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Fiche de paie (Duplicata)</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Détail (Duplicata)</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Période</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Statut Mail </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Renvoyer Mail </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Observations </th>
								</tr>
							</thead>
							<tbody >
								@if(isset($list) && count($list) != 0 )
								@for($i = 0; $i < count($list); $i++)
				
								<tr>
									
									<td style="vertical-align:middle; text-align: center;">{{$list[$i]->Agent}}</td> 
									@if(in_array("downloader_fiche", session("auto_action")))
									<td style="vertical-align:middle; text-align: center;"><a href="{{url($list[$i]->path)}}" target="_blank">Télécharger</a></td>
									@else
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									@endif
									@if(in_array("downloader_detail", session("auto_action")))
									<td style="vertical-align:middle; text-align: center;"><a href="{{url($list[$i]->pathD)}}" target="_blank">Télécharger</a></td>
									@else
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									@endif
									@if(in_array("downloader_fiche_duplicata", session("auto_action")))
									<td style="vertical-align:middle; text-align: center;"><a href="{{url($list[$i]->pathFD)}}" target="_blank">Télécharger</a></td>
									@else
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									@endif
									@if(in_array("downloader_detail_duplicata", session("auto_action")))
									<td style="vertical-align:middle; text-align: center;"><a href="{{url($list[$i]->pathDD)}}" target="_blank">Télécharger</a></td>
									@else
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									@endif

									<td data-priority="1" style="vertical-align:middle; text-align: center;">{{$list[$i]->periode}}</td>
									@if($list[$i]->statut =="true")                                 
									    <td data-priority="1" style="vertical-align:middle; text-align: center;">Envoyé</td>
									@else
    									@if($list[$i]->type != null)
    									    <td data-priority="1" style="vertical-align:middle; text-align: center;">Non envoyé</td>
    									@else
    									    <td data-priority="1" style="vertical-align:middle; text-align: center;">En cours..</td>
    									@endif
									@endif
									<?php 
									    $name = App\Providers\InterfaceServiceProvider::Libellecom($list[$i]->Agent);
					    			    $compte = array('Agent' => $list[$i]->Agent, 'nom' => $name, 'periode' => $list[$i]->periode);
										$comptejson = json_encode($compte);
									?>
									<td data-priority="1" style="vertical-align:middle; text-align: center;">
									    <a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}renv" title="Renvoyé ?">Renvoyé ?</a>
									</td>
									<td data-priority="1" style="vertical-align:middle; text-align: center;">
									    {{$list[$i]->type}}
									</td>
								</tr>
								@endfor
								@else
								<tr>
									<td colspan="10"><center>Pas de document disponible!!! </center></td>
								</tr>
								@endif
								
							</tbody>
						</table>
						{{$list->links()}}


						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

					</div> 