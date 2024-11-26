@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Modifier un utilisateur NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="{{ route('ModifU') }}" >
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Identifiant : </label>
									<div class="col-sm-12">
										<input type="hidden" name="id" value="{{ $info->idUser }}" />
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->login }}"  name="login">
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Nom : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $info->nom }}" name="nom" >
								</div>
							    </div>			
							</div>

							<div class="form-group">
								
							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Prénom : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->prenom }}" name="prenom" >
									</div>
							  </div>

							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Sexe : </label>
									<div class="col-sm-12">
										<select type="text" class="form-control" id="inp-type-1" name="sexe" >
										<option value="{{ $info->sexe }}">{{ App\Providers\InterfaceServiceProvider::sexe($info->sexe) }}</option>
										@if( $info->sexe == 'M')
											<option value="F">Féminin</option>
										@else
											<option value="M">Masculin</option>
										@endif
									</select>
									</div>
							  </div>

							</div>

							<div class="form-group">
								
							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Téléphone : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->tel }}" name="tel">
									</div>
							  </div>

							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Adresse : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="{{ $info->adresse }}" name="adress">
									</div>
							  </div>

							</div>

							<div class="form-group">
								
							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Email : </label>
									<div class="col-sm-12">
										<input type="email" class="form-control" id="inp-type-2" value="{{ $info->mail }}" name="mail">
									</div>
							  </div>

							  <div class="col-sm-6">
							  	<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Autres : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-4" value="{{ $info->other }}" name="autres">
									</div>
							  </div>

							</div>

							<div class="form-group">
								
								<div class="col-sm-6">
									<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Rôle :</label>
									<div class="col-sm-12">
									<select type="text" class="form-control" id="inp-type-4" name="role">
										<option value="{{ $info->Role }}">{{App\Providers\InterfaceServiceProvider::LibelleRole($info->Role)}}</option>
										@foreach($allRole as $role)
											@if($role->idRole != $info->Role)
												<option value="{{ $role->idRole }}">{{ $role->libelle }}</option>
											@endif
										@endforeach

									</select> 
								</div>
								</div>
							</div>

							  

							<div class="form-group" style="display: block;" >
							    <div class="col-sm-12">
				              <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; margin-top: 20px; margin-left: 15px; width: 25%;">Mettre à jour
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