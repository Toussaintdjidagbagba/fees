@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des Clients NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">
				    @if(in_array("exportclients", session("auto_action"))) 
    					<a class="btn-primary dropdown-item" style="color: white; margin-right: 10px; padding: 8px;float:right;" 
					href="{{route('EXPTC')}}">EXPORTER CLIENT <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
    				@endif
					<form class="form-horizontal" action="{{route('listClient')}}" method="GET" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin-right: 0px; float: right;">
								<input class=" form-control" type="hidden" name="rec" value="1">
								<div class="search col-sm-12">
									<input class=" form-control"  type="text" name="check" placeholder="{{$search}} ">
								    <input class=" form-control" type="submit" id="sub">
								</div>
							</div>
						</div>
					</form>
					<script>
					
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
							const input = document.getElementById("sub")
                            input.click()
						}, true);
						
					</script>
					
                    <!------------------------------------------->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Client</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Nom</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Prénoms</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Sexe</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Payeur</th>
								</tr>
							</thead>
							<tbody>
								@forelse($list as $com)
								<tr>
									<td style="vertical-align:middle; text-align: center;">{{$com->idClient}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->nom}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->prenom}}</td>
									<td style="vertical-align:middle; text-align: center;" >{{$com->sexe}}</td>
									<td style="vertical-align:middle; text-align: center;">{{$com->Payeur}}</td>
									
								</tr>
								@empty
								<tr>
									<td colspan="5"><center>Pas de client enregistré!!! </center></td>
								</tr>
								@endforelse
							</tbody>
						</table>
						{{$list->links()}}

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

      <script>
			function afficheMenu(obj){
	
				var idMenu     = obj.id;
				var idSousMenu = 'sous' + idMenu;
				var sousMenu   = document.getElementById(idSousMenu);
			
				for(var i = 1; i <= 4; i++){
					if(document.getElementById('sousmenu' + i) && document.getElementById('sousmenu' + i) != sousMenu){
						document.getElementById('sousmenu' + i).style.display = "none";
					}
				}
				
				if(sousMenu){
					//alert(sousMenu.style.display);
					if(sousMenu.style.display == "block"){
						sousMenu.style.display = "none";
					}
					else{
						sousMenu.style.display = "block";
					}
				}
				
			}
	</script>

@endsection
@section('model')

@endsection

@section('dstestyle')
<style>
.search input[type="text"]
	{
	  padding: 4px 10px;
      height: 42px;
	  background: none;
	  border: 0.5 none;
	  float: left;
	  line-height: 1.5em;
	  margin: 0;
	  width: 200px;
	}

	.search input[type="submit"]
	{
		padding: 4px 10px;
      height: 42px;
	  background: #f0f8ff url(assets/images/seach.png);
	  background-position: center;
	  border: 0.5 none;
	  margin: 0;
	  text-indent: 100px;
	  width: 50px;
	  background-repeat: no-repeat;
	  display: block;

	}
</style>
@endsection