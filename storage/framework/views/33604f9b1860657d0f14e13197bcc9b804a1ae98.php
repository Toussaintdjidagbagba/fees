

<?php $__env->startSection('content'); ?>

	<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>

<?php if(in_array("add_menu", session("auto_action"))): ?>
	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Ajouter un menu :
			<span class="controls"> 
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				 
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="<?php echo e(route('AddMenu')); ?>" >
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                            
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Libellé menu : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="lib" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Titre page : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-2" name="titre" required>
								</div>
							    </div>			
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Route : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="rout" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Icon : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-2" name="icon" >
								</div>
							    </div>			
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Menu parent : </label>
									<div class="col-sm-12">
										<select type="number" class="form-control" id="inp-type-3" name="parent" required>
										<option value="0">Sélectionner un élément</option>
										<?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $par): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($par->Topmenu_id == 0): ?>
												<option value="<?php echo e($par->idMenu); ?>"><?php echo e($par->libelleMenu); ?></option>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Position : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-2" name="pos" required>
									</div>
							    </div>				
							</div>
							
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-6">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Enregistrer
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
<?php endif; ?>

	<div class="col-lg-12 col-md-12 col-xs-12" > 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des Menus NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				
				<div class="row small-spacing">
                <!------------------------------------------>
					<form class="form-horizontal" action="<?php echo e(route('listM')); ?>" method="GET" id="recherche">
						<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-right: 0px; float: right;">
								<input class=" form-control" type="hidden" name="rec" value="1">
								<div class="search col-sm-12">
									<input class=" form-control" type="text" name="check" value="<?php echo e($search); ?>" id="search" placeholder="Rechercher ">
								    <input class=" form-control" type="submit" id="sub">
								</div>
							</div>
						</div>
					</form>
					<script>		
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
							const input = document.getElementById("sub")
                            input.click()
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keydown", function () {
						  const input = document.getElementById("sub")
                            input.click()
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keyup", function () {
						  const input = document.getElementById("sub")
                          input.click()
						}, true);
						
					</script>
					
                    <!------------------------------------------->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1">Libellé Menu</th>
									<th>Titre Page</th>
									<th>Menu parent</th>
									<th>Route</th>
									<th>Icon</th>
									<th>Position</th>
									<th data-priority="3">Action Utilisateur</th>
									<th data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody >

								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<th><span class="co-name"><?php echo e($menu->libelleMenu); ?></span></th>
									<td><?php echo e($menu->titre_page); ?></td>
									<td><?php echo e(App\Providers\InterfaceServiceProvider::Libmenu($menu->Topmenu_id)); ?></td>
									<td><?php echo e($menu->route); ?></td>
									<td><?php echo e($menu->iconee); ?></td>
									<td><?php echo e($menu->num_ordre); ?></td>
                                    <td><?php echo e(App\Providers\InterfaceServiceProvider::LibelleUser($menu->user_action)); ?></td>
   									<td>
   										<?php if(in_array("update_menu", session("auto_action"))): ?>
											<button type="button" title="Modifier"  class="btn btn-primary identifymenu btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/modif-menu-<?php echo e($menu->idMenu); ?>" style="color:white;"><i class="ico fa fa-edit"></i> </a>
											</button>
									    <?php endif; ?>
									    <?php if(in_array("delete_menu", session("auto_action"))): ?>
											<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-menu-<?php echo e($menu->idMenu); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
										<?php endif; ?>

									<?php 
									$actions = App\Providers\InterfaceServiceProvider::recupactions($menu->idMenu);
									$json = json_encode(array('actions' => $actions, 'idMenu' => $menu->idMenu ))
									?>
										<?php if(in_array("manage_menu", session("auto_action"))): ?>
											<button type="button" title="Action"  class="btn btn-xs margin-bottom-10 waves-effect waves-light"><a class="action" href="#add" data-target="#add" data-toggle="modal" data-id="<?php echo e($json); ?>acti" style="color:black;">Actions</a> </button>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="8"><center>Pas de menu enregistrer!!! </center></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<?php echo e($list->links()); ?>


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

      <script type="text/javascript">
	    $(function () {
	        $(".action").click(function () {
	            var id = $(this).data('id');
	            var div = document.getElementById('tst');
	            
	            if (id != 0) {

                    if(id.substr(-4, 4) == "acti"){
                        identifiant = id.slice(0, id.length - 4);
                        var json = JSON.parse(identifiant);
                        var idMenu = json.idMenu;
                        var tab_actions = json.actions;
                        var bout = "";
                        for (var i = 0; i < tab_actions.length; i++) {
                        	//console.log(tab_actions[i].code_dev);
                        	bout += '<label style="float:left; margin:10px; padding:10px; background:#c1cdcd; border-radius :10%; color:black;">'+tab_actions[i].code_dev+'</label>';
                        }
                        div.innerHTML = '' +
                            '<div class="modal-header">' +
                            '   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '   <h4 class="modal-title" id="myModalLabel">Les actions du menu : </h4>' +
                            '</div>' +
                            '<div class="modal-body">' +
                            '   <form class="form-horizontal" method="post" action="<?php echo e(route("ActionMenu")); ?>">' +
                            '      <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />' +
                            '      <input type="hidden" name="menu" value="'+idMenu+'" />' +
                            '      <div class="form-group">  ' +
                            '           <label for="inp-type-2" class="col-sm-12 ">Action :</label>' +
                            '           <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="actio" value="" ></div>' +
                            '           <label for="inp-type-2" class="col-sm-12 ">Action dev :</label>' +
                            '           <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="actiondev"> </div>  ' +
                            '      </div>       ' +
                            '      <div class="form-group">  ' +
	                            '           <label for="inp-type-2" class="col-sm-12 ">Les actions du menu actuel :</label>' +
	                            '              <div class="col-sm-12"> '+bout+
	                            '            '+
	                            '              </div>'+
                                '      </div>'+
                            '    <div class="modal-footer">   ' +
                            '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:black;">AJOUTER</button>' +
                            '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
                            '    waves-light" style="float:right; color:white;">FERMER</button></div>';
                    }
	            }
	        })
	    });
	</script>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('model'); ?>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">

		<div class="modal-content" id="tst">
			</form>
		</div>

	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('dstestyle'); ?>
<style>
	.search input[type="text"]
	{
	  padding: 4px 10px;
      height: 52px;
	  background: none;
	  border: 0.5 none;
	  float: left;
	  line-height: 1.5em;
	  margin: 0;
	  width: 180px;
	}

	.search input[type="submit"]
	{
		padding: 4px 10px;
      height: 52px;
	  background: #f0f8ff url(assets/images/seach.png);
	  background-position: center;
	  border: 0.5 none;
	  margin: 0;
	  text-indent: 100px;
	  width: 50px;
	  background-repeat: no-repeat;
	  display: block;

	}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>