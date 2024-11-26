@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des Périodicités NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content"> 
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
					@if(in_array("add_period", session("auto_action")))
					<button type="button" style="margin-left: 30px;"  class="btn btn-icon btn-icon-left btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#add" ><i class="ico fa fa-plus" ></i>Ajouter</button>
					@endif
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									
									<th data-priority="1">Libelle</th>
									<th data-priority="3">Action Utilisateur</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody>

								@forelse($list as $periodicite)
								<tr>
									
									<td>{{$periodicite->libelle}}</td>
									<td>{{App\Providers\InterfaceServiceProvider::LibelleUser($periodicite->user_action)}}</td>
									<td>
										@if(in_array("update_period", session("auto_action")))
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">
										<a href="/modif-periodicite-{{$periodicite->idPeriodicite}}" style="color:white;"><i class="ico fa fa-edit"></i> </a> 
									</button>
									@endif
										@if(in_array("delete_period", session("auto_action")))
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-periodicite-{{$periodicite->idPeriodicite}}" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									@endif
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="3"><center>Pas de périodicité enregistrer!!! </center></td>
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

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un périodicité : </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="{{ route('AddP') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-3 control-label">Libelle </label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="inp-type-2" name="lib" required>
								</div>
							</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">AJOUTER</button>
			</div>
		</div>
	</div>
	</div>


@endsection