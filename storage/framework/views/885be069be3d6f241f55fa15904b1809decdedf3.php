<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th style="text-align: center; vertical-align:middle;">
										<span class="co-name"><?php echo e($ins->codeH); ?></span>
									</th>
									<td style="text-align: center; vertical-align:middle;"><?php echo e(App\Providers\InterfaceServiceProvider::infoniveau($ins->structureH)->libelleNiveau); ?>

									</td>
									<td style="text-align: center; vertical-align:middle;">
										<?php echo e($ins->libelleH); ?>

									</td>
									<td style="text-align: center; vertical-align:middle;"  
										title="<?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($ins->managerH)); ?>">
									    <?php echo e($ins->managerH); ?>

								    </td>
									<td style="text-align: center; vertical-align:middle;" title="<?php echo e(App\Providers\InterfaceServiceProvider::infosup($ins->superieurH, 'RG')); ?>"><?php echo e($ins->superieurH); ?></td>
									<td style="text-align: center; vertical-align:middle;"><?php echo e($ins->villeH); ?></td>
									<td style="text-align: center; vertical-align:middle;"><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($ins->user_action)); ?></td>
									<td style="text-align: center; vertical-align:middle;">
										<?php if(in_array("update_insp", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-ins-<?php echo e($ins->codeH); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a>
									</button>
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="8"><center>Pas d'inspections enregistrer!!! </center></td>
								</tr>
								<?php endif; ?>