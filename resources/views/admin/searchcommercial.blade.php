
								<?php $i = 1; ?>
								@forelse($list as $com)
								<tr>
									<td style="vertical-align:middle; text-align: center;">{{$com->codeCom}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->nomCom}} {{$com->prenomCom}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->mail}}</td>
									<td style="vertical-align:middle; text-align: center;">{{ App\Providers\InterfaceServiceProvider::infoniveau($com->Niveau)->libelleNiveau}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Equipe : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeEquipe) }}" >{{$com->codeEquipe}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Inspecteur : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeInspection) }}">{{$com->codeInspection}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Région : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeRegion) }}">{{$com->codeRegion}}</td>
									<td style="vertical-align:middle; text-align: center;" title="Chef Coordination : {{ App\Providers\InterfaceServiceProvider::infohier($com->codeCD) }}">{{$com->codeCD}}</td>
									<td style="vertical-align:middle; text-align: center;">

										<div class="btn-group">
											<button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
											</button>
											<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: -200px; right: auto; transform: translate3d(0px, 38px, 0px);">
												<span>
														<a class="dropdown-item" href="/adhère-coordination-{{$com->codeCom}}" title="Adhérer une équipe">Adhérer une coordination</a>
													</span>
												@if(in_array("update_commercial", session("auto_action"))) 
												<span>
													<a class="dropdown-item" href="/modif-commerciaux-{{$com->codeCom}}" title="Modifier">Modifier le commercial</a>
												</span>
												@endif
												<?php 
													$comptearray = array('Agent' => $com->codeCom, 'solde' => $com->compte, 'avance' => $com->avances, 'duree' => $com->duree, 'recent' => $com->recentrembourcer, 'lib' => $com->libCompte, 'num' => $com->numCompte, 'lib2' => $com->libCompte2, 'num2' => $com->numCompte2, 'fixe' => $com->fixe, 'tel' => $com->dotationTelephonie, 'carb' => $com->dotationCarburant);
													$comptejson = json_encode($comptearray);
												?>
												@if(in_array("show_account_commercial", session("auto_action"))) 

												<span>
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}comp" title="Compte">Compte du commercial</a>
												</span>

												@endif

												@if(in_array("advance_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}avan" title="Avance">Imputer une avance</a>
												</span><br>
												@endif

												@if(in_array("fixe_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}fixe" title="Fixe">Fixe</a>
												</span><br>
												@endif

												@if(in_array("add_telephone_staffing_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}tele" title="Téléphonie">Dotation Téléphonie</a>
												</span><br>
												@endif

												@if(in_array("add_fuel_staffing_commercial", session("auto_action"))) 

												<span>	
												<a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="{{$comptejson}}carb" title="Carburant">Dotation Carburant</a>
												</span><br>
												@endif

												@if($com->Niveau == "CONS" || $com->Niveau == "AG" || $com->Niveau == "INST" || $com->Niveau == "B" || $com->Niveau == "COU")
													@if($com->codeEquipe == "")
													@if(in_array("Join_team_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/adhère-equipe-{{$com->codeCom}}" title="Adhérer une équipe">Adhérer une équipe</a>
													</span>
													@endif

													@else
													@if(in_array("update_equipe_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/changer-equipe-{{$com->codeCom}}" title="Changer d'équipe">Changer d'équipe</a>
													</span>
													@endif
													@endif
													@if(in_array("leader_equipe_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addmanageurequipe-{{$com->codeCom}}"  title="Devenir chef d'équipe (NOUVELLE)">Devenir chef d'Equipe</a>
													</span>
													@endif

													@if(in_array("leader_equipe_existante_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addexistanteqp-{{$com->codeCom}}"  title="Devenir chef d'équipe (EXISTANTE)">Devenir chef d'Equipe (EXISTANTE)</a>
													</span>
													@endif
												@else
												    @if($com->Niveau == "CEQP")
												    @if(in_array("Inspection_head_commercial", session("auto_action"))) 
													<span> 
														<a class="dropdown-item" href="/addmanageurins-{{$com->codeCom}}"  title="Devenir chef d'une inspection (NOUVELLE)">Devenir chef d'Inspection</a>
													</span>
													@endif
													@if(in_array("Inspection_head_existante_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/addexistantins-{{$com->codeCom}}"  title="Devenir chef d'une inspection (EXISTANTE)">Devenir chef d'une Inspection (EXISTANTE)</a>
													</span>
													@endif

													@if(in_array("downgrade_cons_commercial", session("auto_action"))) 
													<span>
														<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en CONSEILLER">Rétrograder</a>
												    </span>
												    @endif

													@else
													     @if($com->Niveau == "INS" || $com->Niveau == "BD" || $com->Niveau == "BDS" || $com->Niveau == "APL")
													     @if(in_array("leader_region_commercial", session("auto_action"))) 
													        <span>
															<a class="dropdown-item" href="/addmanageurrg-{{$com->codeCom}}" title="Devenir chef d'une région (NOUVEAU)">Devenir chef d'une région</a> 
															</span>
															@endif

															@if(in_array("leader_region_existante_commercial", session("auto_action"))) 
															<span>
																<a class="dropdown-item" href="/addexistantrg-{{$com->codeCom}}" data-toggle="modal" title="Devenir chef d'une région (EXISTANTE)">Devenir chef d'une région (EXISTANTE)</a>
															</span>
															@endif

															@if(in_array("downgrade_ceqp_commercial", session("auto_action"))) 

															<span>
																<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en CHEF D'EQUIPE ">Rétrograder</a>
														    </span>
														    @endif

													     @else
														     @if(in_array("downgrade_ins_commercial", session("auto_action"))) 
															<span>
																<a class="dropdown-item" href="/retrograder-{{$com->codeCom}}" title="Rétrograder le commercial en INSPECTION ">Rétrograder</a>
															</span>
															@endif

													     @endif
													@endif
												@endif
												
											</div>
										</div>
									</td>
								</tr>
								<?php $i++; ?>
								@empty
								<tr>
									<td colspan="10"><center>Pas de commerciaux enregistrer!!! </center></td>
								</tr>
								@endforelse
