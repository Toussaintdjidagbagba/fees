

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Modifier un commercial NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">   
				
					<button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><a href="<?php echo e(route('listC')); ?>"><i class="ico fa fa-mail-reply"></i>Retour</a></button>
			<div class="col-xs-12">

				<div class="box-content">
					<form class="form-horizontal" method="post" action="<?php echo e(route('ModifC')); ?>">
							<?php if(isset($modifiercom)): ?>
							<input type="hidden" name="id" value="<?php echo e($modifiercom->codeCom); ?>" />
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
							
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label" id="textleft">Nom<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->nomCom); ?>" name="nom" required>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label" >Prénom </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->prenomCom); ?>" name="prenom" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label" id="textleft">Email<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<input type="email" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->mail); ?>" name="mail" >
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Téléphone </label>
								<div class="col-sm-4">
									<input type="number" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->telCom); ?>" name="tel" >
								</div>
							</div>
							
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label" id="textleft" >Sexe<i style="color:red">*</i> </label>
								<div class="col-sm-4">
									<select type="sexe" class="form-control" id="inp-type-2" name="sexe">
										<option value="<?php echo e($modifiercom->sexeCom); ?>"><?php echo e(App\Providers\InterfaceServiceProvider::sexe($modifiercom->sexeCom)); ?></option>
										<?php if( $modifiercom->sexeCom == 'M'): ?>
											<option value="F">Féminin</option>
										<?php else: ?>
											<option value="M">Masculin</option>
										<?php endif; ?>
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Adresse </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->adresseCom); ?>" name="adress" >
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label" id="textleft">IFU </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" value="<?php echo e($modifiercom->AIB); ?>" name="aib" >
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Niveau </label>
								<div class="col-sm-4">
									<select type="text" class=" chosen form-control" id="inp-type-2" name="niv" disabled="true" required>
										<option value="<?php echo e($modifiercom->Niveau); ?>">
										<?php echo e(App\Providers\InterfaceServiceProvider::infoniveau($modifiercom->Niveau)->libelleNiveau); ?> 
										</option>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="modergt" class="col-sm-2 control-label" id="textleft">Mode de règlement </label>
								<div class="col-sm-4">
									<select type="text" class="chosen form-control" id="modergt" name="mode">
									    <option value="<?php echo e($compte->ModePayement); ?>"><?php echo e($compte->ModePayement); ?></option>
									    <option value="MOMO">MOMO</option>
									    <option value="BANQUE">BANQUE</option>
									    <option value="VIREMENT">VIREMENT</option>
									    <option value="CHEQUE">CHEQUE</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label" id="textleft">Réglement </label>
								<div class="col-sm-4">
									<select type="text" class=" chosen form-control" id="inp-type-2" name="ban" >
										<option value="<?php echo e($compte->libCompte); ?>"><?php echo e($compte->libCompte); ?></option>
										<?php $__currentLoopData = $listPayement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										    <option value="<?php echo e($pal->sigle); ?>"><?php echo e($pal->libelle); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label" >Numéro de compte </label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="inp-type-2" value="<?php echo e($compte->numCompte); ?>" name="numban" >
								</div>
							</div>
							
							<div class="form-group">
								<label for="inp-type-2" class="col-sm-2 control-label"id="textleft">Equipe  </label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2"  name="eqp" disabled="true">
										<option value="<?php echo e($modifiercom->codeEquipe); ?>"> <?php echo e($modifiercom->codeEquipe); ?> </option>
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Chef d'Equipe </label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2" disabled="true" required>
										<option >
										<?php echo e(App\Providers\InterfaceServiceProvider::infosup($modifiercom->codeEquipe)); ?> 
										</option>
									</select>
								</div>
							</div>

							<div class="form-group" style="display:block;">
								<label for="inp-type-2" class="col-sm-2 control-label"id="textleft">Inspection</label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2"  disabled="true" required>
										<option value="<?php echo e($modifiercom->codeInspection); ?>"><?php echo e($modifiercom->codeInspection); ?></option>	
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Chef de l'inspection </label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2" disabled="true" required>
										<option >
										<?php echo e(App\Providers\InterfaceServiceProvider::infosup($modifiercom->codeInspection)); ?>

										</option>
									</select>
								</div>
							</div>

							<div class="form-group" style="display:block;">
								<label for="inp-type-2" class="col-sm-2 control-label"id="textleft">Région  </label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2" disabled="true" required>
										<option value="<?php echo e($modifiercom->codeRegion); ?>"><?php echo e($modifiercom->codeRegion); ?></option>
									</select>
								</div>
								<label for="inp-type-2" class="col-sm-2 control-label">Chef du Région </label>
								<div class="col-sm-4">
									<select type="number" class=" chosen form-control" id="inp-type-2" disabled="true" required>
										<option >
											<?php echo e(App\Providers\InterfaceServiceProvider::infosup($modifiercom->codeRegion)); ?>

										</option>
									</select>
								</div>
							</div>



							<div class="form-group" style="display: block;" >
							    <div class="col-sm-12">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; margin-top: 20px; margin-left: 15px; width: 25%;">Mettre à jour
				                    </button>
							    </div>
							</div>
							<?php endif; ?>
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
	<script type="text/javascript">
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);

          var niv = '<?php echo $modifiercom->Niveau;?>';
console.log("tat");
  
  </script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("model"); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstestyle"); ?>
  <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    <script type="text/javascript">
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);

          var niv = '<?php echo $modifiercom->Niveau;?>';
          console.log(niv);
          console.log("toto");

  
  </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstejs"); ?>
<script type="text/javascript">
    $(".chosen").chosen();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>