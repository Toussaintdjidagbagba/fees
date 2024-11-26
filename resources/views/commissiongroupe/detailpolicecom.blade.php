@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Détail du contrat NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Police : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->police }}" name="" disabled>
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Produit : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" disabled id="inp-type-1" value="{{ $info->Produit }}" name="prod"  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Client : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->Client }}" name="client" disabled >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Nom du Client : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ App\Providers\InterfaceServiceProvider::RecupInfoClient($info->Client) }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Payeur : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ App\Providers\InterfaceServiceProvider::RecupInfoPayeurId($info->police) }}" name="client"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Nom du payeur : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ App\Providers\InterfaceServiceProvider::RecupInfoPayeur($info->police) }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Apporteur : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->Agent }}" name="apport"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Nom de l'apporteur : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ App\Providers\InterfaceServiceProvider::Libellecom($info->Agent) }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Date début Effet : </label>
									<div class="col-sm-12">
										<input type="date" class="form-control" id="inp-type-1" disabled value="{{ DateTime::createFromFormat('d-m-Y', $info->DateDebutEffet)->format('Y-m-d') }}" name="dde"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Date fin Effet : </label>
									<div class="col-sm-12">
										<input type="date" class="form-control" id="inp-type-1" disabled value="{{ DateTime::createFromFormat('d-m-Y', $info->DateFinEffet)->format('Y-m-d') }}" name="dfe"  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Statut : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->statutSunshine }}" name="sta"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;"  class="col-sm-12  ">Fractionnement : </label>
									<div class="col-sm-12">
										<select type="text" disabled class="form-control" id="inp-type-1" name="fract"  >
										<option value="{{ $info->fractionnement }}">{{ $info->fractionnement }}</option>
										<option value="MENSUELLE">MENSUELLE</option>
										<option value="TRIMESTRIELLE">TRIMESTRIELLE</option>
										<option value="ANNUELLE">ANNUELLE</option>
										<option value="SEMESTRIELLE">SEMESTRIELLE</option>
										<option value="UNIQUE">UNIQUE</option>
										</select>
									</div>
							    </div>
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
@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection