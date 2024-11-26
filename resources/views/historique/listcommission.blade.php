@extends('layouts.template')

@section('content')



	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
			 Historique des Commissions NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">  

					<form class="form-horizontal" action="{{ route('histCommissionPDF') }}" method="post" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-left: 20px;">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Mois calculé : </label>
								<input class="form-control" type="month" name="mois" value="" min="2021-12" max="{{date('Y-m', strtotime('0 month'))}}">
								
							</div>
							<div class="col-sm-3" style="margin-left: 20px;">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Apporteur : </label>
								<input class=" form-control" type="text" name="codeApporteur" id="agentcheck" placeholder="Rechercher.."  >
								
							</div>
							<div class="col-sm-2" style="margin-left: 0px;">
							    <label style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">. </label>
								
									<button style="width : 50px" class="btn btn-primary form-control" type="submit" name="rec" valu="1" id="search"> 
									    <span style="font-size : 40px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="iconify" data-icon="ci:search"></span>
									</button>
								
							</div>
							@if(in_array("export_pdf_com_global", session("auto_action")))
							<div class="col-sm-3" style="margin-left: 40px;">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Exporter en : </label>
								<button style="width : 50px" class="btn btn-primary form-control" type="submit" value="PDF" name="pdf" id="export"> 
								    <span style="font-size : 30px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="fa fa-file-pdf-o" aria-hidden="true"></span>
								</button>
								<button style="width : 50px" class="btn btn-primary form-control" type="submit" value="EXCEL" name="excel" id="export"> 
								    <span style="font-size : 30px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="fa fa-file-excel-o" aria-hidden="true"></span>
								</button>
								<button style="width : 50px" class="btn btn-primary form-control" type="submit" value="EXCEL DETAIL" name="exceldetail" id="export"> 
								    <span style="font-size : 40px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="iconify" data-icon="eva:file-text-outline"></span>
								</button>
								
							</div>
							@endif
						</div>
					</form>
					<script>		
					
					</script>
					
			<div class="col-xs-12">
				<div class="box-content" >
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped" style="font-size: 10px;">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Code Apporteur</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Nom et Prénoms Apporteur</th>
									<!--th data-priority="1" style="vertical-align:middle; text-align: center;">Gains</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">IFU</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Taux AIB</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">AIB</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Avance Com Remboursée</th-->
									<!--th data-priority="1" style="vertical-align:middle; text-align: center;">Prelèvement</th-->
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Commission Nette</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Période</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Détails</th>
								</tr>
							</thead>
							<tbody id="data">
								@forelse($list as $com)
								<tr>
									<?php 
										$comp = App\Providers\InterfaceServiceProvider::RecupCompteAncien($com->Commercial, $com->moiscalculer);
										$tauxifu = App\Providers\InterfaceServiceProvider::RecupererTaux($com->Commercial);
								    ?>
									<td style="vertical-align:middle; text-align: center;">{{$com->Commercial}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->nomCom}} {{$com->prenomCom}}</td>
									<!--th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format(($comp['bonus'] + $comp['fixe'] + 
									$comp['AutreCommissionMoisCalculer'] + $comp['compteMoisCalculer'] + $comp['compteEncadrementMoisCalculer'] ), 0, '.', ' ')." CFA" }}</th>
									<?php $net_temp = ($comp['compteNetapayerMoisCalculer'] + $comp['retenue'] + $comp['aibMoisCalculer']); ?>
									@if($net_temp != 0)
    									@if(round(($comp['aibMoisCalculer'] / $net_temp) * 100) == 5)
    									    <td style="vertical-align:middle; text-align: center;"></td>
    									@else
    									    <td style="vertical-align:middle; text-align: center;">{{App\Providers\InterfaceServiceProvider::recipIFU($com->Commercial)}}</td>
    									@endif
									@else
									    <td style="vertical-align:middle; text-align: center;"></td>
									@endif
									@if($net_temp == 0)
									    <th data-priority="1" style="vertical-align:middle; text-align: center;"> %</th>
									@else
									    <th data-priority="1" style="vertical-align:middle; text-align: center;">{{ round(($comp['aibMoisCalculer'] / $net_temp) * 100) }} %</th>
									@endif
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comp['aibMoisCalculer'] , 0, '.', ' ')." CFA" }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comp['recentrembourcer'] , 0, '.', ' ')." CFA" }}</th>
									
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comp['retenue'] , 0, '.', ' ')." CFA" }}</th-->
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ number_format($comp['compte'], 0, '.', ' ')." CFA" }}</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">{{ $com->moiscalculer }}</th>
									
								    <td style="vertical-align:middle; text-align: center;">
								        <form action="histcommission-{{ $com->id }}">
    								        <button type="submit" class="btn btn-primary" >
                                              <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </form>
								    </td>
								</tr>
								@empty
								<tr>
									<td colspan="10"><center>Pas de commission disponible!!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
						{{$list->links()}}

						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
						

					</div> 
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-6 col-xs-12 -->
		</div>
			</div>
		</div>
	</div>

@endsection

@section('js')
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
    </script>

    <script type="text/javascript">
	    $(function () {
	    	$("#add").on('hidden.bs.modal', function () {
		        window.location.reload();
		    });
	    });
	</script>
	<script src="https://code.iconify.design/2/2.1.2/iconify.min.js"></script>
	
@endsection
@section("dstestyle")
    <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    <style>
	.btn {
      background: #6495ed;
      background-image: -webkit-linear-gradient(top, #6495ed, #2980b9);
      background-image: -moz-linear-gradient(top, #6495ed, #2980b9);
      background-image: -ms-linear-gradient(top, #6495ed, #2980b9);
      background-image: -o-linear-gradient(top, #6495ed, #2980b9);
      background-image: linear-gradient(to bottom, #6495ed, #2980b9);
      -webkit-border-radius: 7;
      -moz-border-radius: 7;
      border-radius: 7px;
      text-shadow: 7px 22px 15px #8a7c8a;
      font-family: Arial;
      color: #ffffff;
      font-size: 12px;
      padding: 10px 20px 10px 20px;
      text-decoration: none;
    }
    
    .btn:hover {
      background: #212f68;
      background-image: -webkit-linear-gradient(top, #212f68, #6495ed);
      background-image: -moz-linear-gradient(top, #212f68, #6495ed);
      background-image: -ms-linear-gradient(top, #212f68, #6495ed);
      background-image: -o-linear-gradient(top, #212f68, #6495ed);
      background-image: linear-gradient(to bottom, #212f68, #6495ed);
      text-decoration: none;
    }

</style>
@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection