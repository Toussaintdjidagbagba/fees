@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Traiter une réclamation :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="{{ route('ModifRecl') }}" >
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="id" value="{{ $info->id }}" />
							<div class="form-group">
								<div class="col-sm-6">
									<label for="police" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Police : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="police" value="{{ $info->police }}" name="" disabled>
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="client" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Client : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="client" value="{{ App\Providers\InterfaceServiceProvider::RecupInfoClient($info->client) }}" disabled>
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="quittance" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Quittance : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="quittance" value="{{ $info->quittance }}" name="client" disabled >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="apporteur" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Apporteur : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="apporteur" disabled value="{{ App\Providers\InterfaceServiceProvider::Libellecom($info->apporteur) }}" name=""  >
									</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Type de réclamation : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled value="{{ $info->typerecl }}" name="client"  >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Libéller de la réclamation : </label>
									<div class="col-sm-12">
										{{ $info->librecl }}
									</div>
							    </div>
							</div>
								
							<div class="form-group">
								<div class="col-sm-9">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Observations : </label>
									<div class="col-sm-12">
										<textarea type="text" class="form-control" id="inp-type-1" name="obs">{{ $info->obsnsia }}</textarea>
									</div>
							    </div>
							    <div class="col-sm-3">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Période : </label>
									<div class="col-sm-12">
										<input class="form-control" type="month" name="mois" value="" min="2021-12" max="{{date('Y-m', strtotime('12 month'))}}">
							
									</div>
							    </div>
							</div>
							<div class="form-group" style="display: block;" >
							        <div class="col-sm-3">
				              <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style=" margin-top: 20px;  margin-left: 15px; width: 95%;">VALIDER
				              </button>
				              </div>
							    
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