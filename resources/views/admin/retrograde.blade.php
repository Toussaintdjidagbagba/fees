@extends('layouts.template')

@section('content') 

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			 
			<h4 class="box-title with-control">
				@if($type == "CEQP")
				    Retrograder un chef d'équipe en conseiller NSIA :
				@endif
				@if($type == "INS" || $type == "BD" || $type == "BDS" || $type == "APL")
				    Retrograder un chef d'inspection en chef d'équipe NSIA :
				@endif
				@if($type == "RG")
				    Retrograder un chef région en inspection NSIA :
				@endif
			<span class="controls">
					<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
				
					<button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><a href="{{ route('listC') }}" style="color:white"><i class="ico fa fa-mail-reply"></i>Retour</a></button>
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="{{ route('RECS') }}" enctype="multipart/form-data" >
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> <!-- Limite 5Mo -->
							<!-- Formulaire -->
							<div class="col-sm-6">
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code commercial : </label>
										<div class="col-sm-12">
											<input type="hidden"  name="codeC" value="{{ $affcom->codeCom }}">
											<input type="hidden"  name="type" value="{{ $type }}">
											<input type="hidden"  name="codeeexistant" value="{{ $affcom->codeEquipe }}">
											<input type="text" class="form-control" id="inp-type-1"  name="codeh" value="{{ $affcom->codeCom }}" disabled="true">
										</div>
										</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Nom et prénom : </label>
										<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled="true"  name="lib" value="{{ $affcom->nomCom }} {{ $affcom->prenomCom }}">
									</div>
									</div>			
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Niveau actuelle : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" disabled="true" id="inp-type-1"  name="ville" value="{{ $affcom->Niveau }}">
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code du supérieur hiérarchie : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" disabled="true" id="inp-type-1"  name="ville" value="{{ $affcom->codeEquipe }}">
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Référence : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="ref" value="" required>
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Description : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="desc" value="" required>
										</div>
									</div>								
								</div>
								
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> La note : </label>
										<div class="col-sm-12">
											<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="note" required>
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Date éffet : </label>
										<div class="col-sm-12">
											<input type="date" class="form-control" id="inp-type-1"  name="dateeffet" value="{{ date('d/m/Y') }}" required>
										</div>
									</div>								
								</div>

								<div class="form-group" style="display: block;" >
										<div class="col-sm-12">
															<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 50%;">Mettre à jour le commercial
															</button>
											
										</div>
								</div>
						</div>
						<!-- Listes des  équipes -->
						<div class="col-sm-6">
							
							@if($type == "CEQP")
							<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Remplacer le chef d'équipe par : </label>
								<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Recherche par code conseiller"> 
							@endif
							@if($type == 'INS' || $type == "BD" || $type == "BDS" || $type == "APL") 
							<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Remplacer le chef d'inspection par : </label>
								<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Recherche par code d'équipe"> 
							@endif
							@if($type == "RG")
							<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Remplacer le chef région par : </label>
								<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Recherche par code inspection"> 
							@endif
								

								<table id="myTable">
									<tr>
										<th></th>
										<th>
											@if($type == "CEQP") Code conseiller @endif
											@if($type == 'INS' || $type == "BD" || $type == "BDS" || $type == "APL") Code Equipe @endif
											@if($type == "RG") Code inspection @endif
										</th>
										<th>
											@if($type == "CEQP") Nom du conseiller @endif
											@if($type == 'INS' || $type == "BD" || $type == "BDS" || $type == "APL") Nom Chef Equipe @endif
											@if($type == "RG") Nom de l'inpection @endif
										</th>
										<th>
											@if($type == "CEQP") Equipe @endif
											@if($type == 'INS' || $type == "BD" || $type == "BDS" || $type == "APL") INSPECTION @endif
											@if($type == "RG") Région @endif
										</th>
									</tr>
									@forelse($all as $eqp)
										<tr>
									  	<td><input type= "radio" name="select" value="{{ $eqp->codeCom }}" required></td>
									    <td>{{ $eqp->codeCom }}</td>
									    <td>{{ App\Providers\InterfaceServiceProvider::Libellecom($eqp->codeCom) }}</td>
									    <td>{{ App\Providers\InterfaceServiceProvider::Equipecom($eqp->codeCom) }}</td>
									  </tr>
									@empty
									<tr>
										<td colspan="4"><center>Pas d'infos disponible !! </center></td>
									</tr>
									@endforelse
							  
							  
							</table>

						</div>
					</form>	
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-6 col-xs-12 -->
		</div>
			</div>
		</div>
	</div>

@endsection

@section("js")
	<script>
					$('#flash-overlay-modal').modal();
					$('div.alert').not('.alert-important').delay(6000).fadeOut(350);
			</script>
@endsection
@section("model")

@endsection

@section("dstestyle")

	<script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
		<link rel="stylesheet" type="text/css" href="dste/chosen.css">
		<script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
		<script>
			
		</script>
@endsection

@section("dstejs")
<script type="text/javascript">
		$(".chosen").chosen();
</script>
<script type="text/javascript">
	function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
@endsection