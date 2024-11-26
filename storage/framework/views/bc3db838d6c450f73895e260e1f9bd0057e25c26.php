					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th>Identifiant</th>
									<th data-priority="1">Nom</th>
									<th data-priority="3">Prénom(s)</th>
									<th data-priority="1">Téléphone</th>
									<th data-priority="3">Email</th>
									<th data-priority="3">Rôle</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th><span class="co-name"><?php echo e($user->login); ?></span></th>
									<td><?php echo e($user->nom); ?></td>
									<td><?php echo e($user->prenom); ?></td>
									<td><?php echo e($user->tel); ?></td>
									<td><?php echo e($user->mail); ?></td>
									<td><?php echo e(App\Providers\InterfaceServiceProvider::LibelleRole($user->Role)); ?></td>
									<td>
                                    <?php if(in_array("update_user", session("auto_action"))): ?>
									<button type="button" title="Modifier"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">
										<a href="/modif-users-<?php echo e($user->idUser); ?>" style="color:white;"> <i class="ico fa fa-edit"></i></a> 
										
									</button>
									<?php endif; ?>

									<?php if(in_array("delete_user", session("auto_action"))): ?>
									<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-users-<?php echo e($user->idUser); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									<?php endif; ?>

									<?php if(in_array("reset_user", session("auto_action"))): ?>
									<button type="button" title="Réinitialiser"  class="btn btn-warning btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/reinitialiser-users-<?php echo e($user->idUser); ?>" style="color:white;"> <i class="ico fa fa-circle-o-notch"></i></a></button>
									<?php endif; ?>

									<?php if(in_array("status_user", session("auto_action"))): ?>
									<?php if($user->statut == "0"): ?>
									<button type="button" title="Désactivé ?"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/desactivé-users-<?php echo e($user->idUser); ?>" style="color:white;"> <i class="fa fa-toggle-on" aria-hidden="true"></i></a></button>
									<?php endif; ?>
									<?php if($user->statut == "1"): ?>
									<button type="button" title="Activé ?"  class="btn btn-primary btn-circle btn-xs  margin-bottom-10 waves-effect waves-light" style="background-color:grey"><a href="/activé-users-<?php echo e($user->idUser); ?>" style="color:white;"> <i class="fa fa-toggle-off" aria-hidden="true"></i></a></button>
									<?php endif; ?>
									<?php endif; ?>
									
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="7"><center>Pas d'utilisateur enregistrer!!!</center> </td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<?php echo e($list->links()); ?>


					</div>