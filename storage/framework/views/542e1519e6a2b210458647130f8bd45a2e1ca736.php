
								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eqp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name"><?php echo e($eqp->codeH); ?></span>
									</th>
									<td style="text-align: center; vertical-align:middle;">CEQP
									</td>
									<td style="text-align: center; vertical-align:middle;">
										<?php echo e($eqp->libelleH); ?>

									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="<?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($eqp->managerH)); ?>">
									    <?php echo e($eqp->managerH); ?>

								    </td>
									<td style="text-align: center; vertical-align:middle;" title="<?php echo e(App\Providers\InterfaceServiceProvider::infosup($eqp->superieurH)); ?>"><?php echo e($eqp->superieurH); ?></td>
									
									<td style="text-align: center; vertical-align:middle;"><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($eqp->user_action)); ?></td>
									<td style="text-align: center; vertical-align:middle;">
										<?php if(in_array("update_equipe", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-equipe-<?php echo e($eqp->codeH); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button> 
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="8"><center>Pas d'équipe enregistrer!!! </center></td>
								</tr>
								<?php endif; ?>
