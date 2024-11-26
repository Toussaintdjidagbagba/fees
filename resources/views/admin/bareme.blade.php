@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Schéma :
			<span class="controls"> 
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
					<button type="button" class="btn btn-icon btn-icon-left btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#add" ><i class="ico fa fa-plus" ></i>Ajouter</button>

					<!--button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><i class="ico fa fa-mail-reply"></i>Retour</button-->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th>Identifiant</th>
									<th data-priority="1">Produit</th>
									<th data-priority="1">Schéma</th>
									<th data-priority="1">Actions</th>
								</tr>
							</thead>
							<tbody>

								@forelse($list as $bareme)
								<tr>
									<th style="text-align: center; vertical-align:middle;"><span class="co-name">{{$bareme->idSchema}}</span></th>
                                    <td style="text-align: center; vertical-align:middle;">{{App\Providers\InterfaceServiceProvider::libprod($bareme->Produit)}}</td>
                                    <td style="text-align: center; vertical-align:middle;">{{$bareme->libelle}}</td>
									<td>

									<center>

									<button type="button" title="Modifier"  class="btn btn-warning btn-rounded btn-xs  margin-bottom-10 waves-effect waves-light" >
									<a href="/modif-bareme-{{$bareme->idSchema }}" style="color:white;">Modifier</a> 
										<?php // $modifierschema = App\Providers\InterfaceServiceProvider::infoschema($bareme->idSchema) ?>
									</button> <br>

									<button type="button" title="Supprimer"  class="btn btn-danger btn-rounded btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-bareme-{{$bareme->idSchema }}" style="color:white;">Supprimer</a> </button> <br>

									<button type="button" title="Ajouter taux au schéma"  class="btn btn-primary btn-rounded btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/add-Taux-{{$bareme->idSchema }}" style="color:white">Ajouter taux</a> 
									</button> </center>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="3"><center>Pas de Schéma enregistrer!!! </center></td>
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

@section("js")
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
      </script>
@endsection
@section("model")

<div class="modal fade" id="modif" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modifier Schéma : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('ModifS') }}">
							@if(isset($modifierschema ))
							<input type="hidden" name="id" value="{{ $modifierschema->idSchema }}" />
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Libellé : </label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="inp-type-1" value="{{ $modifierschema->libelle }}" name="libelle" >
								</div>
							</div>

							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Taux AIB </label>
								<div class="col-sm-4">
									<input type="number" step="0.00001" class="form-control" id="inp-type-1" value="{{$modifierschema->tauxAIB}}" name="tauxaib">
								</div>
	
								<label for="inp-type-1" class="col-sm-2 control-label">Taux Non AIB</label>
								<div class="col-sm-4">
									<input type="number" step="0.00001" class="form-control" id="inp-type-1" value="{{$modifierschema->tauxNonAIB}}" name="tauxnonaib">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Produit </label>
								<div class="col-sm-10">
									
									<select type="text" class="form-control" id="inp-type-1" name="prod">
										<option value="{{$modifierschema->Produit}}">{{ App\Providers\InterfaceServiceProvider::libprod($modifierschema->Produit)}}</option>
										@foreach($prodall as $pro)
										@if($modifierschema->Produit != $pro->codeProduit)
											<option value="{{$pro->codeProduit}}">{{ App\Providers\InterfaceServiceProvider::libprod($pro->codeProduit)}}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							@endif
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Mise à jour</button>
				</form>
			</div>
		</div>
	</div>
	</div>


<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un Schéma : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('AddS') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								
								<label for="inp-type-2" class="col-sm-2 control-label">Libellé </label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="inp-type-2" name="lib" required>
								</div>
							</div>

							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Taux AIB </label>
								<div class="col-sm-4">
									<input type="number" step="0.00001" class="form-control" id="inp-type-1" placeholder="0.00" name="tauxaib">
								</div>
	
								<label for="inp-type-1" class="col-sm-2 control-label">Taux Non AIB</label>
								<div class="col-sm-4">
									<input type="number" step="0.00001" class="form-control" id="inp-type-1" placeholder="0.00" name="tauxnonaib">
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-1" class="col-sm-2 control-label">Produit </label>
								<div class="col-sm-10">
									<select type="text" class="form-control" id="inp-type-1" name="prod">
										@foreach($prodall as $pro)
											<option value="{{$pro->codeProduit}}">{{ App\Providers\InterfaceServiceProvider::libprod($pro->codeProduit)}}</option>
										@endforeach
									</select>
								</div>
							</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">AJOUTER</button>
			</form>
			</div>
		</div>
	</div>
	</div>


@endsection