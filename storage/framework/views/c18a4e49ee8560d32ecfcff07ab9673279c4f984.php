

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Modifier une équipe NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">   
				
					<button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><a href="<?php echo e(route('listI')); ?>"><i class="ico fa fa-mail-reply"></i>Retour</a></button>
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="<?php echo e(route('ModifE')); ?>" enctype="multipart/form-data" >
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> <!-- Limite 5Mo -->
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code : </label>
									<div class="col-sm-12">
										<input type="hidden"  name="codeh" value="<?php echo e($infoeqp->codeH); ?>">
										<input type="text" class="form-control" id="inp-type-1"  name="codeh" value="<?php echo e($infoeqp->codeH); ?>" disabled="true">
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Libellé : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-1"  name="lib" value="<?php echo e($infoeqp->libelleH); ?>">
								</div>
							    </div>			
							</div>
							<div class="form-group">
								
							    <div class="col-sm-6">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Manageur :</label>
								<div class="col-sm-12">
									<select type="number" class="chosen form-control" id="inp-type-1" name="manageur" >
										<option value="<?php echo e($infoeqp->managerH); ?>"> <?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($infoeqp->managerH)); ?></option>
										<?php $__currentLoopData = $listmag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if($mag->codeCom != $infoeqp->managerH ): ?>
										    <option value="<?php echo e($mag->codeCom); ?>"><?php echo e($mag->nomCom); ?> <?php echo e($mag->prenomCom); ?></option>
										<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
								</div>

								<div class="col-sm-6">
									<label for="inp-type-2" class="col-sm-12 ">Supérieur hiérarchie :</label>
									<div class="col-sm-12">
										<select type="text" class=" chosen form-control" id="inp-type-1" name="sup" >
											<option value="<?php echo e($infoeqp->superieurH); ?>"> <?php echo e(App\Providers\InterfaceServiceProvider::infosup($infoeqp->superieurH)); ?> </option>
											<?php $__currentLoopData = $listsup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($sup->codeH != $infoeqp->superieurH): ?>
											    <option value="<?php echo e($sup->codeH); ?>"><?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($sup->managerH)); ?></option>
											<?php endif; ?>
										  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>
							    </div>
							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Référence : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="ref" value="" required>
										</div>
									</div>								
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Description : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="desc" value="" required>
										</div>
									</div>								
								

							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> La note : </label>
										<div class="col-sm-12">
											<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="note" required>
										</div>
								
								</div>
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Date éffet : </label>
										<div class="col-sm-12">
											<input type="date" class="form-control" id="inp-type-1"  name="dateeffet" value="<?php echo e(date('d/m/Y')); ?>" required>
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