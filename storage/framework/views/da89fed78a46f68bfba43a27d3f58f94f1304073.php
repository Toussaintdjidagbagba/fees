								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th><span class="co-name"><?php echo e($prod->idProduit); ?></span></th>
									<td><?php echo e($prod->libelle); ?></td>
									<td><?php echo e($prod->codeProduit); ?></td>
									<td><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($prod->user_action)); ?></td>
									<td>
                                    <?php if(in_array("update_prod", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"> <a href="/modif-produit-<?php echo e($prod->idProduit); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a></button>
									<?php endif; ?>

									<?php if(in_array("delete_prod", session("auto_action"))): ?>
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-produit-<?php echo e($prod->idProduit); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									<?php endif; ?>
									<?php if(in_array("parameterize_taux_menu", session("auto_action"))): ?>
										<button type="button" title="Paramétrer"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/add-Taux-<?php echo e($prod->idProduit); ?>" style="color:white;"><i class="ico fa fa-bars"></i> </button>
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="5"><center>Pas de produit enregistrer!!! </center></td>
								</tr>
								<?php endif; ?>
