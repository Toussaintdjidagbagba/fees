

<?php $__env->startSection('content'); ?>

	<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Société NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				 
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="<?php echo e(route('AddSoc')); ?>"  enctype="multipart/form-data">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                            
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Libellé société : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="libelleSociete" value="<?php echo e($soc->libelleSociete); ?>" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Email : </label>
								<div class="col-sm-12">
									<input type="email" class="form-control" id="inp-type-2" name="email" value="<?php echo e($soc->email); ?>" required>
								</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Adresse : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="adresse" value="<?php echo e($soc->adresse); ?>" >
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Téléphone : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-2" name="contact" value="<?php echo e($soc->contact); ?>" required>
								</div>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Taux AIB : </label>
									<div class="col-sm-12">
										<input type="number" class="form-control" id="inp-type-1"  name="aib" value="<?php echo e($soc->tauxAIB); ?>" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Taux non AIB : </label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-2" name="nonaib" value="<?php echo e($soc->tauxNonAIB); ?>" required>
								</div>
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Pied de page : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="pied" value="<?php echo e($soc->piedpage); ?>" >
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Aide :
										<?php if($soc->aidemanuel != ""): ?>
											<a href="<?php echo e(url($soc->aidemanuel)); ?>">(Télécharger)</a>
										<?php endif; ?>
									 </label>
									<div class="col-sm-12">
										<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="aide" >
									</div>
							    </div>
							</div>
							<div class="form-group"> 
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Signataire : </label>
									<select type="number" class="form-control" id="inp-type-3" name="sin" required>
										<option value="0">Sélectionner un signataire</option>
										<?php $__currentLoopData = $allpersonnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($p->idUser); ?>"><?php echo e($p->nom); ?> <?php echo e($p->prenom); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
							    </div>
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Période des commissions : </label>
								<input class="form-control" type="month" name="mois" value="<?php echo e(date('Y-m', strtotime('01-'.$soc->periode))); ?>" min="2021-12">
							    </div>
							    <div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Logo : </label>
								<div class="col-sm-12"> 
									<div class="profile-img-container">
											<div class="img-block">
												<?php if($soc->logo == ""): ?>
												<img class="profile-image" id="sign-image" src="assets/images/defaut.png" style="border-radius: 50%;" >
												<?php else: ?>
												<img class="profile-image" id="sign-image" src="<?php echo e($soc->logo); ?>" style="border-radius: 50%;" >
												<?php endif; ?>
												<i id="pick-sign-image" class="bi bi-pencil-fill" data-toggle="tooltip" data-placement="top" title="Changer l'image"></i>
											</div>
                      <input type="file" hidden id="sign-image-input" name="photologo">
									</div>
								</div>
							    </div>			
							</div>
							<?php if(in_array("update_soc", session("auto_action"))): ?>
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-6">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Mettre à jour
				                    </button>
							    </div>
							</div>
							<?php endif; ?>
					</form>	
				</div>
				<!-- /.box-content -->
			</div>
		</div>
	</div>
</div>
</div>

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


<?php $__env->startSection("partjs"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script type="text/javascript">
		$(function () {
  			$('[data-toggle="tooltip"]').tooltip()
		})


		const input = document.getElementById("image-input")
		const pick = document.querySelector("#pick-image")
		pick.addEventListener('click', () => {
			input.click()
		})


		input.addEventListener('change', () => {
			var reader = new FileReader()
			const preview = document.getElementById("profile-image")
			reader.onload = function() {
				preview.setAttribute('src', reader.result)
			}
			reader.readAsDataURL(event.target.files[0]);
		})

		const input_sign = document.getElementById("sign-image-input")
		const pick_sign = document.querySelector("#pick-sign-image")
		pick_sign.addEventListener('click', () => {

			input_sign.click()
		})


		input_sign.addEventListener('change', () => {
			var sign_reader = new FileReader()
			const preview_sign = document.getElementById("sign-image")
			sign_reader.onload = function() {
				preview_sign.setAttribute('src', sign_reader.result)
			}
			sign_reader.readAsDataURL(event.target.files[0]);
		})
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>