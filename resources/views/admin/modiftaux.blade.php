@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Modifier un taux NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="{{ route('ModifT') }}" >
						
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />

						<div class="form-group">
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Durée contrat min : </label>
								<div class="col-sm-12">

									<input type="number" min="0" class="form-control" id="inp-type-1" value="{{$info->dureecontratmin}}" name="dureemin">
								</div>
							</div>
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Durée contrat max :  -1 = &#8734;</label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-1" value="{{$info->dureecontratmax}}" name="dureemax">
								</div>
							</div>
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Durée en application : -1 = &#8734; </label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-1" value="{{$info->dureenapplication}}" name="duree">
								</div>
							</div>
						</div>
							<div class="form-group">
								
							  <div class="col-sm-4">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Niveau :</label>
								<div class="col-sm-12">
									<select type="text" class="form-control" id="inp-type-4" name="niv">
									<option value="{{$info->Niveau}}">{{App\Providers\InterfaceServiceProvider::infoniveau($info->Niveau)->libelleNiveau}}</option>
										@foreach($allNiveau as $niv)
											@if($niv->codeNiveau != $info->Niveau)
												<option value="{{ $niv->codeNiveau }}">{{ $niv->libelleNiveau }}</option>
											@endif
										@endforeach
								</select> 
								</div>
								</div>
								<div class="col-sm-4">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Périodicité :</label>
								<div class="col-sm-12">
									<select type="text" class="form-control" id="inp-type-4" name="periodicite">
									<option value="{{$info->Periodicite}}">{{App\Providers\InterfaceServiceProvider::infoperiodicite($info->Periodicite)->libelle}}</option>
										@foreach($allPeriodicite as $per)
											@if($per->idPeriodicite != $info->Periodicite)
												<option value="{{ $per->idPeriodicite }}">{{ $per->libelle }}</option>
											@endif
										@endforeach
										
								</select> 
								</div>
								</div>
								<div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Schéma : </label>
                                <div class="col-sm-12">
                                    <select type="text" class="form-control" id="inp-type-1" name="schema">
                                        <option value="{{$info->Schema}}">{{$info->Schema}} SCHEMA</option>
                                        @if($info->Schema == "ANCIEN")
                                            <option value="NOUVEAU">NOUVEAU SCHEMA</option>
                                        @else
                                            <option value="ANCIEN">ANCIEN SCHEMA</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
							</div>
                        <div class="form-group">
                            
                            
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Base Commission min : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->basemin}}" name="combasemin">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Base Commission max : -1 = &#8734; </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->basemax}}" name="combasemax">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Quittance : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->Quittance}}" name="quitt">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Agent : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->Agent}}" name="agent">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Police : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->police}}" name="police">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Convention : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->conv}}" name="convent">
                                </div>
                            </div>
                            
                        </div>
                        	<div class="form-group">
								<div class="col-sm-4">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Taux % : -1 = fixe *</label>
									<div class="col-sm-12">
										<input type="hidden" name="id" value="{{ $info->idTauxNiveau }}" />
										<input type="number" step="0.0001" class="form-control" id="inp-type-1" value="{{$info->tauxCommissionnement}}" name="taux">
									</div>
							    </div>
								<div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Pourcentage % : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->pourcentage}}" name="pourc">
                                </div>
                                </div>
                                <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Commission fixe : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="{{$info->comfixe}}" name="fixecom">
                                </div>
                                </div>		
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Frais accessoire : </label>
									<div class="col-sm-12">
										<input type="number" step="0" class="form-control" id="inp-type-1" value="{{$info->acces}}" name="access">
									</div>
							    </div>
							    <div class="col-sm-4 ">
            						<label for="inp-type-1" class="col-sm-12 ">Statut : </label>
            						<div class="col-sm-12">
            								<select type="number" class="form-control" id="inp-type-4" name="statad" required>
            									@if($info->statad == 1)	
            										<option value="1">Désactiver</option>
            										<option value="0">Activer</option>
            									@endif
            										
            									@if($info->statad == 0)
                                                    <option value="0">Activer</option>
                                                    <option value="1">Désactiver</option>
                                                @endif
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