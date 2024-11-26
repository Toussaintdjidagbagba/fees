

<?php $__env->startSection('content'); ?>

<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"><?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Profil :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="<?php echo e(route('SPU')); ?>"  enctype="multipart/form-data">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />

              <div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Rôle : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="<?php echo e(App\Providers\InterfaceServiceProvider::LibelleRole(session('utilisateur')->Role)); ?>"  name="role" disabled="true">
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Login : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="<?php echo e(session('utilisateur')->login); ?>"  name="log">
									</div>
							    </div>			
							</div>

							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Nom : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="<?php echo e(session('utilisateur')->nom); ?>"  name="nom" >
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Prénoms : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-1" value="<?php echo e(session('utilisateur')->prenom); ?>" name="prenom" >
								</div>
							    </div>			
							</div>
							
							<div class="form-group">	
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Sexe : </label>
										<div class="col-sm-12">
											<?php if(session('utilisateur')->sexe == 'M'): ?>
											<input type="text" class="form-control" id="inp-type-1"  name="sexe" value="MASCULIN" disabled="true">
											<?php endif; ?>
											<?php if(session('utilisateur')->sexe == 'F'): ?>
											<input type="text" class="form-control" id="inp-type-1"  name="sexe" value="FEMININ" disabled="true">
											<?php endif; ?>
										</div>
									</div>								
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Téléphone : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="tel" value="<?php echo e(session('utilisateur')->tel); ?>" >
										</div>
									</div>
							</div>

							<div class="form-group">	
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Adresse : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="adr" value="<?php echo e(session('utilisateur')->adresse); ?>" >
										</div>
									</div>								
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Email : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="email" value="<?php echo e(session('utilisateur')->mail); ?>" >
										</div>
									</div>
							</div>

							<div class="form-group">	
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Autre : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="autr" value="<?php echo e(session('utilisateur')->other); ?>" >
										</div>
									</div>								
								
							</div>

							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Avatar : </label>

										<div class="profile-img-container">
											<div class="img-block">
												<?php if(session('utilisateur')->image == "" || session('utilisateur')->image == null): ?>
												<img class="profile-image" id="profile-image" src="assets/images/defaut.png" style="border-radius: 50%;" >
												<?php else: ?>
												<img class="profile-image" id="profile-image" src="<?php echo e(session('utilisateur')->image); ?>" style="border-radius: 50%;" >
												<?php endif; ?>
												<i id="pick-image" class="bi bi-pencil-fill" data-toggle="tooltip" data-placement="top" title="Changer l'image"></i>
											</div>
                      <input type="file" hidden id="image-input" name="photoavatar">
										</div>
								  </div>

									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Signature : </label>
										<div class="profile-img-container">
											<div class="img-block">
												<?php if(session('utilisateur')->signature == "" || session('utilisateur')->signature == null): ?>
												<img class="profile-image" id="sign-image" src="assets/images/defaut.png" style="border-radius: 50%;" >
												<?php else: ?>
												<img class="profile-image" id="sign-image" src="<?php echo e(session('utilisateur')->signature); ?>" style="border-radius: 50%;" >
												<?php endif; ?>
												<i id="pick-sign-image" class="bi bi-pencil-fill" data-toggle="tooltip" data-placement="top" title="Changer l'image"></i>
											</div>
                      <input type="file" hidden id="sign-image-input" name="photosignature">
										</div>
								</div>
							</div>


							
							<div class="form-group" id="Ajouter">
							    <div class="col-sm-6">
				              <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: auto;padding: .7rem 1.5rem;">Mettre à jour
				              </button>
							    </div>
							</div>
					</form>	
				</div>
				<!-- /.box-content -->
			</div>
		</div>
	</div>
</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection("js"); ?>
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
          
          function passer($val) {
			  var x = document.getElementById("modify");
			  var y = document.getElementById("Ajouter"); 
			  if (y.style.display == "block") {
			  	y.style.display = "none";
			    x.style.display = "block";
			  } else {
			    x.style.display = "none";
			    y.style.display = "block"
			  }
			}


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