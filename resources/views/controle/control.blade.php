@extends('layouts.template')

@section('content')



	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
			 Controle des Commissions Manager NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">  

					<form class="form-horizontal" action="{{ route('listContrMagSet') }}" method="post" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							
							<div class="col-sm-3" style="margin-left: 20px;">
								<label for="con" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Inspecteur : </label>
								<select class=" form-control" type="text" name="Commercial" id="con">
								    @foreach($listIns as $value)
								        <option value="{{ $value->managerH }}">{{ $value->managerH }}</option>
								    @endforeach
								</select>
							</div>
							
							@if(in_array("export_pdf_com_global", session("auto_action")))
							<div class="col-sm-3" style="margin-left: 40px;">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Exporter en DETAIL: </label>
								
								<button style="width : 50px" class="btn btn-primary form-control" type="submit" value="EXCEL" name="excel" id="export"> 
								    <span style="font-size : 30px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="fa fa-file-excel-o" aria-hidden="true"></span>
								</button>
								
							</div>
							@endif
						</div>
					</form>
					<form class="form-horizontal" action="{{ route('listContrMagSetRe') }}" method="post" id="recherche">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="form-group">
							
							<div class="col-sm-3" style="margin-left: 20px;">
								<label for="con" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Inspecteur : </label>
								<select class=" form-control" type="text" name="Commercial" id="con">
								    @foreach($listIns as $value)
								        <option value="{{ $value->managerH }}">{{ $value->managerH }}</option>
								    @endforeach
								</select>
							</div>
							
							@if(in_array("export_pdf_com_global", session("auto_action")))
							<div class="col-sm-3" style="margin-left: 40px;">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Exporter en RESUME: </label>
								
								<button style="width : 50px" class="btn btn-primary form-control" type="submit" value="EXCEL" name="excel" id="export"> 
								    <span style="font-size : 30px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="fa fa-file-excel-o" aria-hidden="true"></span>
								</button>
								
							</div>
							@endif
						</div>
					</form>
					<script>		
					
					</script>
					
			<div class="col-xs-12">
				<div class="box-content" >
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped" style="font-size: 10px;">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Inspection</th>
									
								</tr>
							</thead>
							<tbody id="data">
								
								<tr>
								    <td>Vide</td>
								</tr>
							</tbody>
						</table>
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
	<script src="https://code.iconify.design/2/2.1.2/iconify.min.js"></script>
	
@endsection
@section("dstestyle")
    <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    <style>
	.btn {
      background: #6495ed;
      background-image: -webkit-linear-gradient(top, #6495ed, #2980b9);
      background-image: -moz-linear-gradient(top, #6495ed, #2980b9);
      background-image: -ms-linear-gradient(top, #6495ed, #2980b9);
      background-image: -o-linear-gradient(top, #6495ed, #2980b9);
      background-image: linear-gradient(to bottom, #6495ed, #2980b9);
      -webkit-border-radius: 7;
      -moz-border-radius: 7;
      border-radius: 7px;
      text-shadow: 7px 22px 15px #8a7c8a;
      font-family: Arial;
      color: #ffffff;
      font-size: 12px;
      padding: 10px 20px 10px 20px;
      text-decoration: none;
    }
    
    .btn:hover {
      background: #212f68;
      background-image: -webkit-linear-gradient(top, #212f68, #6495ed);
      background-image: -moz-linear-gradient(top, #212f68, #6495ed);
      background-image: -ms-linear-gradient(top, #212f68, #6495ed);
      background-image: -o-linear-gradient(top, #212f68, #6495ed);
      background-image: linear-gradient(to bottom, #212f68, #6495ed);
      text-decoration: none;
    }

</style>
@endsection

@section("dstejs")
<script type="text/javascript">
    $(".chosen").chosen();
</script>
@endsection