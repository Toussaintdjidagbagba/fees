

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">  
				Attribuer menu NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="<?php echo e(route('MenuAttr')); ?>" >
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                            
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Rôle : </label>
									<div class="col-sm-12">
										<input type="hidden" name="role" value="<?php echo e($role->idRole); ?>" />
										<input type="text" class="form-control" id="inp-type-1" value="<?php echo e($role->libelle); ?>"  name="libelle">
									</div>
							    </div>			
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Attribuer un menu : </label>

										<?php $__currentLoopData = $allmenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

											<div class="col-sm-6">

												<div class="col-sm-2"> 
													
													<?php if(count($auto_menu) != 0): ?>
													   <?php if(in_array(strval($menu->idMenu), $auto_menu)): ?>
													      <center><input  type="checkbox" id="men<?php echo e($menu->idMenu); ?>" name="menu[]" value="<?php echo e($menu->idMenu); ?>" style="height: 25px; width: 25px;background-color: #0000ff;" checked></center>
													   <?php else: ?>
                                <center><input type="checkbox" id="men<?php echo e($menu->idMenu); ?>" name="menu[]" value="<?php echo e($menu->idMenu); ?>" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													   <?php endif; ?>
													<?php else: ?>
                             <center><input  type="checkbox" id="men<?php echo e($menu->idMenu); ?>" name="menu[]" value="<?php echo e($menu->idMenu); ?>" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													<?php endif; ?>
													
												</div>
												<div class="col-sm-10">
													<label for="men<?php echo e($menu->idMenu); ?>" style="vertical-align:middle; margin-top: 1%; font-size: 18px" class="col-sm-12  "><?php echo e($menu->libelleMenu); ?> <?php  ?> </label>
													
                          <?php $allaction_this = App\Providers\InterfaceServiceProvider::actionMenu($menu->idMenu);
                           ?>

                          <?php $__currentLoopData = $allaction_this; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-sm-12">
												            <div class="col-sm-2">
												            <?php if(count($auto_action) != 0): ?> 
                                        <?php
                                             $array = array();
                                             foreach($auto_action as $all){
                                             	 if($all->Menu == $menu->idMenu)
                                                  array_push($array, $all->ActionMenu);
                                             }
                                         ?>
                                         <?php if(in_array(strval($action->id), $array)): ?>
													              <center><input 
													           type="checkbox" id="act<?php echo e($action->id); ?>" 
		  				name="action[]" value="<?php echo e($action->id); ?>" style="height: 25px; width: 25px;background-color: #0000ff;" checked></center>
													               <?php else: ?>
                                        <center><input type="checkbox" id="act<?php echo e($action->id); ?>" name="action[]" value="<?php echo e($action->id); ?>" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
													               <?php endif; ?>
                                    <?php else: ?>
                                        <center><input type="checkbox" id="act<?php echo e($action->id); ?>" name="action[]" value="<?php echo e($action->id); ?>" style="height: 25px; width: 25px;background-color: #0000ff;"></center>
                                    <?php endif; ?>

												            </div>
												            <div class="col-sm-10">
													             <label for="act<?php echo e($action->id); ?>" style="vertical-align:middle; margin-top: 1%; font-size: 18px" class="col-sm-12"><?php echo e($action->action); ?> </label>
											              </div>
											         </div>
											    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</div>
												</div>
											
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<?php $__env->stopSection(); ?>

<?php $__env->startSection("js"); ?>
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
      </script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("model"); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstestyle"); ?>
  <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstejs"); ?>
<script type="text/javascript">
    $(".chosen").chosen();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>