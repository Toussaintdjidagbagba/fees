@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Détail quittance NSIA :
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
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Quittance : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->NumQuittance }}" name="" disabled>
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Date Début Quittance : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" disabled id="inp-type-1" value="{{ $info->DateDebutQuittance }}" name="prod"  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Date Fin Quittance : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->DateFinQuittance }}" name="client" disabled >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Date Production : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->DateProduction }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Police : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->NumPolice }}" name="client"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Relevé : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->NumReleve }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Index Quittance : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->IndexQuittance }}" name="client"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Nombre de fois calculé : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->ncom }}" name=""  >
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