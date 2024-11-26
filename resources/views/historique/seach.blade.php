<tbody id="data">
								@forelse($list as $com)
								<tr>
									<?php 
										$comp = App\Providers\InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
										$tauxifu = App\Providers\InterfaceServiceProvider::RecupererTaux($com->Commercial);
								    ?>
									<td style="vertical-align:middle; text-align: center;">{{$com->Commercial}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->nomCom}} {{$com->prenomCom}}</td>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format(($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] ), 0, '.', ' ')." CFA" }}</th>
									<?php $net_temp = ($comp['compteNetapayerMoisCalculer'] + $comp['retenue'] + $comp['aibMoisCalculer']); ?>
									@if($net_temp == 0)
									    <th data-priority="1" style="vertical-align:middle; text-align: center;"> %</th>
									@else
									    <th data-priority="1" style="vertical-align:middle; text-align: center;">{{ round(($comp['aibMoisCalculer'] / $net_temp) * 100) }} %</th>
									@endif
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comp['aibMoisCalculer'] }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comp['avancesancien'] - $comp['avances'] }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $comp['retenue'] }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comp['compte'], 0, '.', ' ')." CFA" }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->moiscalculer }}</th>
								    <td style="vertical-align:middle; text-align: center;">
								        <button type="button" class="btn btn-primary">
                                          <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
								    </td>
								</tr>
								@empty
								<tr>
									<td colspan="9"><center>Pas de commission disponible!!! </center></td>
								</tr>
								@endforelse
							</tbody>