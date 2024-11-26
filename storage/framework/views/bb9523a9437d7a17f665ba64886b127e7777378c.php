

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Modifier un taux NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">   
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="<?php echo e(route('ModifT')); ?>" >
						
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />

						<div class="form-group">
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Durée contrat min : </label>
								<div class="col-sm-12">

									<input type="number" min="0" class="form-control" id="inp-type-1" value="<?php echo e($info->dureecontratmin); ?>" name="dureemin">
								</div>
							</div>
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Durée contrat max :  -1 = &#8734;</label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->dureecontratmax); ?>" name="dureemax">
								</div>
							</div>
							<div class="col-sm-4">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Durée en application : -1 = &#8734; </label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->dureenapplication); ?>" name="duree">
								</div>
							</div>
						</div>
							<div class="form-group">
								
							  <div class="col-sm-4">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Niveau :</label>
								<div class="col-sm-12">
									<select type="text" class="form-control" id="inp-type-4" name="niv">
									<option value="<?php echo e($info->Niveau); ?>"><?php echo e(App\Providers\InterfaceServiceProvider::infoniveau($info->Niveau)->libelleNiveau); ?></option>
										<?php $__currentLoopData = $allNiveau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $niv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($niv->codeNiveau != $info->Niveau): ?>
												<option value="<?php echo e($niv->codeNiveau); ?>"><?php echo e($niv->libelleNiveau); ?></option>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select> 
								</div>
								</div>
								<div class="col-sm-4">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Périodicité :</label>
								<div class="col-sm-12">
									<select type="text" class="form-control" id="inp-type-4" name="periodicite">
									<option value="<?php echo e($info->Periodicite); ?>"><?php echo e(App\Providers\InterfaceServiceProvider::infoperiodicite($info->Periodicite)->libelle); ?></option>
										<?php $__currentLoopData = $allPeriodicite; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($per->idPeriodicite != $info->Periodicite): ?>
												<option value="<?php echo e($per->idPeriodicite); ?>"><?php echo e($per->libelle); ?></option>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										
								</select> 
								</div>
								</div>
								<div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Schéma : </label>
                                <div class="col-sm-12">
                                    <select type="text" class="form-control" id="inp-type-1" name="schema">
                                        <option value="<?php echo e($info->Schema); ?>"><?php echo e($info->Schema); ?> SCHEMA</option>
                                        <?php if($info->Schema == "ANCIEN"): ?>
                                            <option value="NOUVEAU">NOUVEAU SCHEMA</option>
                                        <?php else: ?>
                                            <option value="ANCIEN">ANCIEN SCHEMA</option>
                                        <?php endif; ?>
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
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->basemin); ?>" name="combasemin">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Base Commission max : -1 = &#8734; </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->basemax); ?>" name="combasemax">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Quittance : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->Quittance); ?>" name="quitt">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Agent : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->Agent); ?>" name="agent">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Police : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->police); ?>" name="police">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Convention : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->conv); ?>" name="convent">
                                </div>
                            </div>
                            
                        </div>
                        	<div class="form-group">
								<div class="col-sm-4">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Taux % : -1 = fixe *</label>
									<div class="col-sm-12">
										<input type="hidden" name="id" value="<?php echo e($info->idTauxNiveau); ?>" />
										<input type="number" step="0.0001" class="form-control" id="inp-type-1" value="<?php echo e($info->tauxCommissionnement); ?>" name="taux">
									</div>
							    </div>
								<div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Pourcentage % : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->pourcentage); ?>" name="pourc">
                                </div>
                                </div>
                                <div class="col-sm-4">
                                <label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Commission fixe : </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" id="inp-type-1" value="<?php echo e($info->comfixe); ?>" name="fixecom">
                                </div>
                                </div>		
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Frais accessoire : </label>
									<div class="col-sm-12">
										<input type="number" step="0" class="form-control" id="inp-type-1" value="<?php echo e($info->acces); ?>" name="access">
									</div>
							    </div>
							    <div class="col-sm-4 ">
            						<label for="inp-type-1" class="col-sm-12 ">Statut : </label>
            						<div class="col-sm-12">
            								<select type="number" class="form-control" id="inp-type-4" name="statad" required>
            									<?php if($info->statad == 1): ?>	
            										<option value="1">Désactiver</option>
            										<option value="0">Activer</option>
            									<?php endif; ?>
            										
            									<?php if($info->statad == 0): ?>
                                                    <option value="0">Activer</option>
                                                    <option value="1">Désactiver</option>
                                                <?php endif; ?>
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