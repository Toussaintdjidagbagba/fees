@extends('layouts.template')

@section('content')



	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
			 Liste des Traces du système NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">  

					
					<script>		
						
						function getXMLHttpRequest() {
							var xhr = null;
							
							if (window.XMLHttpRequest || window.ActiveXObject) {
								if (window.ActiveXObject) {
									try {
										xhr = new ActiveXObject("Msxml2.XMLHTTP");
									} catch(e) {
										xhr = new ActiveXObject("Microsoft.XMLHTTP");
									}
								} else {
									xhr = new XMLHttpRequest(); 
								}
							} else {
								alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
								return null;
							}
							
							return xhr;
						}
						function readData(sData) {
							//alert(sData);
							var oSelect = document.getElementById("data");
							
							oSelect.innerHTML = sData;
						}
						var elt = document.querySelector('select');
						elt.addEventListener('change', function () {
							document.getElementById("recherche").submit();
							/*//console.log('selectedIndex => '+this.value);
							var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "{{route('listdocument')}}?check="+this.value+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText); // Données textuelles récupérées

							    }
							};*/
						})
						
						var x = document.getElementById("recherche");
						x.addEventListener("blur", function () {
							document.getElementById("recherche").submit();
						  /*//console.log("Mois "+document.getElementById("moischeck").value);
						  moisch = document.getElementById("moischeck").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "{{route('listdocument')}}?check="+moisch+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText); // Données textuelles récupérées

							    }
							};*/
						}, true);

						
					</script>
					
			<div class="col-xs-12">
				<div class="box-content" id="data">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Action Utilisateur</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Libellé</th>
									
								</tr>
							</thead>
							<tbody >
								@if(isset($list) && count($list) != 0 )
								@for($i = 0; $i < count($list); $i++)
				
								<tr>
									<td data-priority="1" style="vertical-align:middle; text-align: center;">
									    {{App\Providers\InterfaceServiceProvider::LibelleUser($list[$i]->user_action)}}</td>
									
									<td style="vertical-align:middle; text-align: center;">
									    
										<p id="libelle" style=""> {{$list[$i]->libelleTrace}} </p>
									</td> 
									
								</tr>
								@endfor
								@else
								<tr>
									<td colspan="2"><center>Pas de traces disponible!!! </center></td>
								</tr>
								@endif
								
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
	
@endsection
@section("dstestyle")
  <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    <style>

	.dropdown-item{
		font-family: "Open Sans", sans-serif;
	  font-size: 15px;
	  font-weight: 400;
	  color: #333;
	  display: inline-block;
	  padding: 15px;
	  position: relative;
	}

    #libelle{
		width:200px; 
		height: 40px ; 
		overflow:scroll;
		white-space:pre-line; text-overflow: ellipsis;
		word-wrap: break-word;
	}
    
    @media screen and (min-width: 480px){
    	#libelle{
    		width:300px; 
    		height: 40px ; 
    		overflow:scroll;
    		white-space:pre-line; text-overflow: ellipsis;
    		word-wrap: break-word;
    	}
    }
    
    @media screen and (min-width: 700px){
    	#libelle{
    		width:400px; 
    		height: 40px ; 
    		overflow:scroll;
    		white-space:pre-line; text-overflow: ellipsis;
    		word-wrap: break-word;
    	}
    }
    
    @media screen and (min-width: 1024px){
    	#libelle{
    		width:900px; 
    		height: 50px ; 
    		overflow:scroll;
    		white-space:pre-line; text-overflow: ellipsis;
    		word-wrap: break-word;
    	}
    }

	
    </style>

@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection