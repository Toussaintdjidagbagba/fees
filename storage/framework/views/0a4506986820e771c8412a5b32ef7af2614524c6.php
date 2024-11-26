

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Paramétrer taux du produit <?php echo e(App\Providers\InterfaceServiceProvider::infoproduit($produit)->libelle); ?> :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing"> 
				    <?php if(in_array("add_taux_prod", session("auto_action"))): ?>  
					<button type="button" style="margin-left:30px" class="btn btn-icon btn-icon-left btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#add" ><i class="ico fa fa-plus" ></i>Ajouter</button>
					<?php endif; ?>

					<button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><a href="<?php echo e(route('listProd')); ?>" style="color:white"><i class="ico fa fa-mail-reply"></i>Retour</a></button>
			<div class="col-xs-12">
				<div class="box-content"> 
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr> 
									<th class="textcenter">Niveau</th>
									<th data-priority="1" class="textcenter">Périodicité</th>
									<th data-priority="1" class="textcenter">Durée en applications</th>
									<th data-priority="1" class="textcenter">Durée contrat min </th>
									<th data-priority="1" class="textcenter">Durée contrat max </th>
									<th data-priority="1" class="textcenter">Schéma</th>
									<th data-priority="1" class="textcenter">Agent</th>
									<th data-priority="1" class="textcenter">Quittance</th>
									<th data-priority="1" class="textcenter">Taux</th>
									<th data-priority="1" class="textcenter">Pourcentage</th>
									<th data-priority="1" class="textcenter">Police</th>
									<th data-priority="1" class="textcenter">Convention</th>
									<th data-priority="1" class="textcenter">Base Com min</th>
									<th data-priority="1" class="textcenter">Base Com max</th>
									<th data-priority="1" class="textcenter">Com. Fixe</th>
									
									<th data-priority="1" class="textcenter">Accessoire</th>
									<th data-priority="1" class="textcenter">Statut</th>
									<th data-priority="6" class="textcenter">Actions</th>
								</tr>
							</thead>
							<tbody>

								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taux): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th class="textcenter"><?php echo e(App\Providers\InterfaceServiceProvider::infoniveau($taux->Niveau)->libelleNiveau); ?></th>
									<td class="textcenter"><?php echo e(App\Providers\InterfaceServiceProvider::infoperiodicite($taux->Periodicite)->libelle); ?></td>
									<td class="textcenter">
										<?php if( $taux->dureenapplication == -1): ?>
											Pendant la durée du contrat
										<?php else: ?>
											<?php echo e($taux->dureenapplication); ?>

										<?php endif; ?>
									</td>
									<td class="textcenter"><?php echo e($taux->dureecontratmin); ?></td>
									<td class="textcenter">
										<?php if( $taux->dureecontratmax == -1): ?>
											&#8734;
										<?php else: ?>
											<?php echo e($taux->dureecontratmax); ?>

										<?php endif; ?>
									</td>
									<td class="textcenter"><?php echo e($taux->Schema); ?></td>
									<td class="textcenter"><?php echo e($taux->Agent); ?></td>
									<td class="textcenter"><?php echo e($taux->Quittance); ?></td>
									<td class="textcenter"><?php echo e($taux->tauxCommissionnement); ?> %</td>
									<td class="textcenter"><?php echo e($taux->pourcentage); ?> %</td>
									<td class="textcenter"> <?php echo e($taux->police); ?> </td>
									<td class="textcenter"> <?php echo e($taux->conv); ?> </td>
									<td class="textcenter"> <?php echo e($taux->basemin); ?> </td>
									<td class="textcenter"> <?php echo e($taux->basemax); ?> </td>
									<td class="textcenter"><?php echo e($taux->comfixe); ?> CFA</td>
									<td class="textcenter"><?php echo e($taux->acces); ?> CFA</td>
									<td class="textcenter">
									    <?php if( $taux->statad == 0): ?>
									        Activer
									    <?php else: ?>
									        Désactiver
									    <?php endif; ?>
									</td>
									<td class="textcenter">
										<?php if(in_array("update_taux_prod", session("auto_action"))): ?> 
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">
										<a href="/modif-taux-<?php echo e($taux->idTauxNiveau); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button>
									    <?php endif; ?>
									    <?php if(in_array("delete_taux_prod", session("auto_action"))): ?> 
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-taux-<?php echo e($taux->idTauxNiveau); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="13"><center>Pas de taux enregistrer pour le <?php echo e(App\Providers\InterfaceServiceProvider::infoproduit($produit)->libelle); ?> !!! </center></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div> 
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
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enregistrer un Taux :  </h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" action="<?php echo e(route('AddT')); ?>">
					<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
					<input type="hidden" name="prod" value="<?php echo e($produit); ?>" />

					<div class="form-group">
                        <div class="col-sm-4">
							<label for="inp-type-1" class="col-sm-12">Durée contrat min (mois) :</label>
							<div class="col-sm-12">
								<input type="number" class="form-control" id="inp-type-1" min="0" name="dureecontratmin" required>
							</div>
                        </div>
                        <div class="col-sm-4">
							<label for="inp-type-1" class="col-sm-12">Durée contrat max : <i style="color:red">-1 = &#8734;</i></label>
							<div class="col-sm-12">
								<input type="number" class="form-control" id="inp-type-1" name="dureecontratmax" required>
							</div>
                        </div>
                        <div class="col-sm-4">
    						<label for="inp-type-1" class="col-sm-12">Durée en application : <i style="color:red">-1 = &#8734;</i> </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" placeholder="" required name="duree">
    						</div>
    					</div>
					</div>
					
					<div class="form-group">
    					<div class="col-sm-4 ">
    						<label for="inp-type-1" class="col-sm-12 ">Niveau : </label>
    						<div class="col-sm-12">
    								<select type="text" class="form-control" id="inp-type-4" name="niv" required>
    									<?php if(isset($allNiveau[0]->codeNiveau)): ?>
    										<?php $__currentLoopData = $allNiveau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $niv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    												<option value="<?php echo e($niv->codeNiveau); ?>"><?php echo e($niv->libelleNiveau); ?></option>
    										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    									<?php else: ?>
    										<option value="0"></option>
    									<?php endif; ?>
    								</select> 
    						</div>
    					</div>
    
    					<div class="col-sm-4">
    								<label for="inp-type-2" class="col-sm-12">Périodicité : </label>
    								<div class="col-sm-12">
    								<select type="text" class="form-control" id="inp-type-4" name="periodicite" required>
    									<?php if(isset($allPeriodicite[0]->idPeriodicite)): ?>
    										<?php $__currentLoopData = $allPeriodicite; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
    												<option value="<?php echo e($per->idPeriodicite); ?>"><?php echo e($per->libelle); ?></option>
    										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    									<?php else: ?>
    										<option value="0"></option>
    									<?php endif; ?>
    								</select> 
    								</div>
    					</div>
    					
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Schéma : </label>
    						<div class="col-sm-12">
    							<select type="text" class="form-control" id="inp-type-4" name="schema" required>
    								<option value="ANCIEN">ANCIEN SCHEMA</option>
    								<option value="NOUVEAU">NOUVEAU SCHEMA</option>
    							</select>
    						</div>
    					</div>
					</div>
					
					<div class="form-group">
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Base Commission min : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="combasemin">
    						</div> 
    					</div>
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Base Commission max : <i style="color:red">-1 = &#8734;</i> </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="combasemax">
    						</div> 
    					</div>
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Quittance : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="quittance">
    						</div>
    					</div>
    				</div>
					
					<div class="form-group">
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Code Agent : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="agent">
    						</div> 
    					</div>
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Police : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="police">
    						</div> 
    					</div>
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Convention : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="convent">
    						</div> 
    					</div>
    				</div>
    				
    				<div class="form-group">
    					<div class="col-sm-4">
    							<label for="inp-type-1" class="col-sm-12">Taux % : <i style="color:red">-1 = fixe*</i> </label>
    							<div class="col-sm-12">
    								<input type="number" step="0.0001" class="form-control" required id="inp-type-1" name="taux">
    							</div>
    					</div>
                        <div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Pourcentage % : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="pourcentage" required>
    						</div>
    					</div>
    					<div class="col-sm-4">
    						<label for="inp-type-2" class="col-sm-12">Commission fixe : </label>
    						<div class="col-sm-12">
    							<input type="number" class="form-control" id="inp-type-1" name="fixecom">
    						</div> 
    					</div>
                    </div>
                    
                    <div class="form-group">
    					<div class="col-sm-4">
    							<label for="inp-type-1" class="col-sm-12">Frais accessoire : </label>
    							<div class="col-sm-12">
    								<input type="number" step="0" class="form-control" required id="inp-type-1" name="access">
    							</div>
    					</div>
    					<div class="col-sm-4 ">
    						<label for="inp-type-1" class="col-sm-12 ">Statut : </label>
    						<div class="col-sm-12">
    								<select type="number" class="form-control" id="inp-type-4" name="statad" required>
    										<option value="0">Activer</option>
    										<option value="1">Désactiver</option>
    										
    								</select> 
    						</div>
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


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>