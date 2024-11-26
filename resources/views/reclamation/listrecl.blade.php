@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des réclamations NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">
				    @if(in_array("export_recl", session("auto_action"))) 
    					<a class="btn-primary dropdown-item" style="color: white; margin: 10px; padding: 8px;float:right;" 
					href="" id="exp" onclick="exporter()">EXPORTER RECLAMATION <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
    				@endif
    				
    				
						<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<input class="form-control" type="search" name="check" onsearch="rechercher()" onblur="rechercher()" id="check" placeholder="Rechercher ">
								</div>
							</div>
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<select class="form-control" type="search" name="checktype" onsearch="trietype()" onchange="trietype()" id="checktype">
									    <option value="">Trier par type de réclamation</option>
									    <option>Commission non calculée</option>
									    <option>Police non crée</option>
									</select>
								</div>
							</div>
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<select class="form-control" type="search" name="checketat" onsearch="trietype()" onchange="trietype()" id="checketat">
									    <option value="">Trier par état</option>
									    <option value="1">Traité</option>
									    <option value="0">Non traité</option>
									</select>
								</div>
							</div>
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<select class="form-control" type="text" name="checketat" id="periode">
									    <option value="">Période</option>
									    <option>06-2023</option>
									    <option>05-2023</option>
									</select>
								</div>
							</div>
						</div>
					
					<script>
					    function exporter(){
					        check = document.getElementById("check").value;
					        token = document.getElementById("_token").value;
					        checketat = document.getElementById("checketat").value;
					        checktype = document.getElementById("checktype").value;
					        document.getElementById("exp").href = "https://fees.nsiaviebenin.com/exportreclamation?_token="+token+"&etat="+checketat+"&type="+checktype+"&seach="+check;
					        document.getElementById("exp").click();
					    }
					    
					    async function rechercher(){
					        token = document.getElementById("_token").value;
					        check = document.getElementById("check").value;
					        
						    try {
        						  let response = await fetch("https://fees.nsiaviebenin.com/seachreclamation?_token="+token+"&seach="+check, 
        						  {
        						    method: 'GET',
                            		headers: {
                            			'Access-Control-Allow-Origin': 'https://fees.nsiaviebenin.com/seachreclamation',
                            			'Access-Control-Allow-Credentials': true,
                            			'Content-Type': 'application/json',
                            			'Accept': 'application/json',
                            		},
        						  });
                                let html = "";
                                if(response.status == 200)
                                {
                                    data = await response.text();
                                    suc = JSON.parse(data).success;
                                    list = JSON.parse(data).data;
                                    if(suc == true){
                                      list.forEach(function(recl){
                                            html += "<tr>";
                                            html += '<td style="vertical-align:middle; text-align: center;">'+recl.nomCom+' '+recl.prenomCom+'('+recl.apporteur+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.police+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.quittance+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">{{ App\Providers\InterfaceServiceProvider::RecupInfoClient('+recl.client+') }}</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.typerecl+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.librecl+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">';
        									    if(recl.etatnsia != 0) html += 'traité'; else html += 'Non traité';
        									html += '</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">';
        									html += '<a href="https://fees.nsiaviebenin.com/modif-reclamation-'+recl.id+'" style="color:white;">';
        									html += '<button type="button" title="Modifier" class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">'; 
        									html += '<i class="ico fa fa-edit"></i>';
        									html += '</button>';
        									html += '</a>';
        									html += '</td>';
                                            html += "</tr>";
                                      });
                                    }else{
                                        html += "<tr>";
                                        html += "<td class='textcenter' colspan='8'> Pas de réclamation disponible. </td>";
                                        html += "</tr>";
                                    }
                                    document.getElementById("data").innerHTML = html;
                                }
                                
						    } catch (error) {
                                let html = "";
                                html += "<tr>";
                                html += "<td class='textcenter' colspan='8'> Pas de réclamation disponible.</td>";
                                html += "</tr>";
                                document.getElementById("data").innerHTML = html;
                            }
					    }
						
						async function trietype(){
					        token = document.getElementById("_token").value;
					        checketat = document.getElementById("checketat").value;
					        checktype = document.getElementById("checktype").value;
					        
						    try {
        						  let response = await fetch("https://fees.nsiaviebenin.com/triereclamation?_token="+token+"&etat="+checketat+"&type="+checktype, 
        						  {
        						    method: 'GET',
                            		headers: {
                            			'Access-Control-Allow-Origin': 'https://fees.nsiaviebenin.com/triereclamation',
                            			'Access-Control-Allow-Credentials': true,
                            			'Content-Type': 'application/json',
                            			'Accept': 'application/json',
                            		},
        						  });
                                let html = "";
                                if(response.status == 200)
                                {
                                    data = await response.text();
                                    suc = JSON.parse(data).success;
                                    list = JSON.parse(data).data;
                                    if(suc == true){
                                      list.forEach(function(recl){
                                            html += "<tr>";
                                            html += '<td style="vertical-align:middle; text-align: center;">'+recl.nomCom+' '+recl.prenomCom+'('+recl.apporteur+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.police+'</td>';
        									
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.quittance+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.client+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">{{ App\Providers\InterfaceServiceProvider::RecupInfoClient('+recl.client+') }}</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.typerecl+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.librecl+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">';
        									    if(recl.etatnsia != 0) html += 'traité'; else html += 'Non traité';
        									html += '</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+recl.created_at+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">';
        									html += '<a href="https://fees.nsiaviebenin.com/modif-reclamation-'+recl.id+'" style="color:white;">';
        									html += '<button type="button" title="Modifier" class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">'; 
        									html += '<i class="ico fa fa-edit"></i>';
        									html += '</button>';
        									html += '</a>';
        									html += '<button type="button" title="Supprimer" onclick="avissuppr('+recl.id+', "'+recl.typerecl+'", "'+recl.librecl+'", "'+recl.nomCom+' '+recl.prenomCom+'")" data-toggle="modal" data-target="#add" class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> ';
    									    html += '<i class="ico fa fa-trash"></i>';
    								        html += '</button>';
        									html += '</td>';
                                            html += "</tr>";
                                      });
                                    }else{
                                        html += "<tr>";
                                        html += "<td class='textcenter' colspan='8'> Pas de réclamation disponible. </td>";
                                        html += "</tr>";
                                    }
                                    document.getElementById("data").innerHTML = html;
                                }
                                
						    } catch (error) {
                                let html = "";
                                html += "<tr>";
                                html += "<td class='textcenter' colspan='8'> Pas de réclamation disponible.</td>";
                                html += "</tr>";
                                document.getElementById("data").innerHTML = html;
                            }
					    }
					</script>
					
                    <!------------------------------------------->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Apporteur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Police</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Quittance</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Payeur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Client</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Type de réclamation</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Réclamation</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Etat</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Date demande</th>
									<th data-priority="6" style="vertical-align:middle;text-align: center;">Actions</th>
								</tr>
							</thead>
							<tbody id="data">
								
								@forelse($list as $com)
								<tr>
									<td style="vertical-align:middle; text-align: center;">{{App\Providers\InterfaceServiceProvider::Libellecom($com->apporteur)}}({{$com->apporteur}})</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->police}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->quittance}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->client}}</td>
									<td style="vertical-align:middle; text-align: center;">{{ App\Providers\InterfaceServiceProvider::RecupInfoClient($com->client) }}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->typerecl}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->librecl}}</td> 
									<td style="vertical-align:middle; text-align: center;">
									    @if($com->etatnsia == 0) Non traité @else traité @endif
									</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->created_at}}</td>
									<td style="vertical-align:middle; text-align: center;">
									<a href="{{route('GetModifRecl', $com->id)}}" style="color:white;">
									    <button type="button" title="Modifier" class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> 
    									    <i class="ico fa fa-edit"></i>
    									</button>
									</a>
									<button type="button" title="Supprimer" onclick="avissuppr({{$com->id}}, '{{$com->typerecl}}', '{{$com->librecl}}', '{{App\Providers\InterfaceServiceProvider::Libellecom($com->apporteur)}}')" data-toggle="modal" data-target="#add" class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> 
    									    <i class="ico fa fa-trash"></i>
    								</button>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="13"><center>Pas de réclamation enregistré!!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script>
    function avissuppr(id, typ, lib,nom){
        texte = "Voulez-vous vraiment supprimer la réclamation de "+nom+" ? <br> <b>Type de réclamation :</b> "+typ+" <br> <b>Libellé réclamation :</b> "+lib+" <br> <i style='color:red'> La suppression est irréversible. </i>";
        document.getElementById("idrecl").value = id;
        document.getElementById("inforecl").innerHTML = texte;
        console.log(texte);
    }
</script>
@endsection

@section('js')

@endsection
@section('model')
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">

		<div class="modal-content" id="tst">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Supprimer une réclamation : </h4>
			</div>
			<div class="modal-body">
			    <form class="form-horizontal" method="get" action="{{ route('DelRecl') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<input type="hidden" name="idrecl" id="idrecl" value="" />
					<div class="form-group">
						<label class="col-sm-12 control-label" style="text-align : left" id="inforecl">
						    
						</label>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-danger btn-sm waves-effect waves-light">CONFIRMER</button>
			</div>
		</div>

	</div>
	</div>

@endsection

@section('dstestyle')
@endsection