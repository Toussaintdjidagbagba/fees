@extends('layouts.template')

@section('content')

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">  
				Attribuer menu NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> @include('flash::message')</center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="{{ route('MenuAttr') }}" >
							<input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Rôle : </label>
									<div class="col-sm-12">
										<input type="hidden" name="role" value="{{ $role->idRole }}" />
										<input type="text" class="form-control" id="inp-type-1" value="{{ $role->libelle }}"  name="libelle">
									</div>
							    </div>			
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Attribuer un menu : </label>

										@foreach($allmenu as $menu)

											<div class="col-sm-6">

												<div class="col-sm-2"> 
													
													@if(count($auto_menu) != 0)
													   @if(in_array(strval($menu->idMenu), $auto_menu))
													      <center><input  type="checkbox" id="men{{$menu->idMenu}}" name="menu[]" value="{{$menu->idMenu}}" style="height: 25px; width: 25px;background-color: #0000ff;" checked></center>
													   @else
                                <center><input type="checkbox" id="men{{$menu->idMenu}}" name="menu[]" value="{{$menu->idMenu}}" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													   @endif
													@else
                             <center><input  type="checkbox" id="men{{$menu->idMenu}}" name="menu[]" value="{{$menu->idMenu}}" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													@endif
													
												</div>
												<div class="col-sm-10">
													<label for="men{{$menu->idMenu}}" style="vertical-align:middle; margin-top: 1%; font-size: 18px" class="col-sm-12  ">{{$menu->libelleMenu}} <?php  ?> </label>
													
                          <?php $allaction_this = App\Providers\InterfaceServiceProvider::actionMenu($menu->idMenu);
                           ?>

                          @foreach($allaction_this as $action)
                                <div class="col-sm-12">
												            <div class="col-sm-2">
												            @if(count($auto_action) != 0) 
                                        <?php
                                             $array = array();
                                             foreach($auto_action as $all){
                                             	 if($all->Menu == $menu->idMenu)
                                                  array_push($array, $all->ActionMenu);
                                             }
                                         ?>
                                         @if(in_array(strval($action->id), $array))
													              <center><input 
													           type="checkbox" id="act{{$action->id}}" 
		  				name="action[]" value="{{$action->id}}" style="height: 25px; width: 25px;background-color: #0000ff;" checked></center>
													               @else
                                        <center><input type="checkbox" id="act{{$action->id}}" name="action[]" value="{{$action->id}}" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													               @endif
                                    @else
                                        <center><input type="checkbox" id="act{{$action->id}}" name="action[]" value="{{$action->id}}" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
                                    @endif

												            </div>
												            <div class="col-sm-10">
													             <label for="act{{$action->id}}" style="vertical-align:middle; margin-top: 1%; font-size: 18px" class="col-sm-12">{{$action->action}} </label>
											              </div>
											         </div>
											    @endforeach
												</div>
												</div>
											
										@endforeach

									</div>

							  </div>			
							</div>
							
							<div class="form-group" style="display: block;" >
							    <div class="col-sm-12">
				              <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; margin-top: 20px; margin-left: 15px; width: 25%;">Attribuer
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