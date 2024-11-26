<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name"><?php echo e($rg->codeH); ?></span>
									</th>
									<td style="text-align: center; vertical-align:middle;">
										Région
									</td>
									<td style="text-align: center; vertical-align:middle;">
										<?php echo e($rg->libelleH); ?>

									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="<?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($rg->managerH)); ?>">
									    <?php echo e($rg->managerH); ?>

								    </td>
									<td style="text-align: center; vertical-align:middle;" title="<?php echo e(App\Providers\InterfaceServiceProvider::infosup($rg->superieurH, 'CD')); ?>"><?php echo e($rg->superieurH); ?></td>
									
									<td style="text-align: center; vertical-align:middle;"><?php echo e($rg->villeH); ?></td>
									<td style="text-align: center; vertical-align:middle;"><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($rg->user_action)); ?></td>
									<td style="text-align: center; vertical-align:middle;">
										<?php if(in_array("update_region", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-rg-<?php echo e($rg->codeH); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button>
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="8"><center>Pas de région enregistrer!!! </center></td>
								</tr>
								<?php endif; ?>