<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th>Code</th>
									<th data-priority="1">Libelle</th>
									<th data-priority="3">Action Utilisateur</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody>

								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th><span class="co-name"><?php echo e($role->code); ?></span></th>
									<td><?php echo e($role->libelle); ?></td> 
									<td><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($role->user_action)); ?></td>
									<td>
                                    <?php if(in_array("update_role", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-roles-<?php echo e($role->idRole); ?>" style="color:white;"><i class="ico fa fa-edit"></i></a>
									</button>
									<?php endif; ?>

									<?php if(in_array("delete_role", session("auto_action"))): ?>
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-roles-<?php echo e($role->idRole); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									<?php endif; ?>

                                    <?php if(in_array("menu_role", session("auto_action"))): ?>
									<button type="button" title="Menu"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/menu-roles-<?php echo e($role->idRole); ?>" style="color:white;"><i class="ico fa fa-bars"></i></a> 
									</button>
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="4"><center>Pas de rôle enregistrer!!! </center> </td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<?php echo e($list->links()); ?>


					</div>