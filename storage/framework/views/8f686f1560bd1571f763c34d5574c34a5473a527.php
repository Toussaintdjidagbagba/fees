

<?php $__env->startSection('content'); ?>

<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"><?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>

<?php if(in_array("add_insp", session("auto_action"))): ?>
	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Ajouter une inspection :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				 
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="<?php echo e(route('addi')); ?>" enctype="multipart/form-data" >
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" /> 
              <input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> <!-- Limite 5Mo -->
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="codeh" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Libellé : </label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="inp-type-1"  name="lib">
								</div>
							    </div>			
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Ville : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="ville">
									</div>
							    </div>
							    <div class="col-sm-6">
								<label for="inp-type-2" style="vertical-align:middle;" class="col-sm-12 ">Chef d'inspection :</label>
								<div class="col-sm-12">
									<select type="number" class="chosen form-control" id="inp-type-1" name="manageur" >
										<?php if($com_manageur != ""): ?>
											<option value="<?php echo e($com_manageur->codeCom); ?>"><?php echo e($com_manageur->nomCom); ?> <?php echo e($com_manageur->prenomCom); ?></option>
										<?php endif; ?>
										<?php $__currentLoopData = $listmag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										    <option value="<?php echo e($mag->codeCom); ?>"><?php echo e($mag->nomCom); ?> <?php echo e($mag->prenomCom); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-2" class="col-sm-12 ">Catégorie :</label>
									<div class="col-sm-12">
										<select type="text" class=" chosen form-control" id="inp-type-1" name="cat" >
											<?php $__currentLoopData = $listcat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											    <option value="<?php echo e($cat->codeNiveau); ?>"><?php echo e($cat->libelleNiveau); ?></option>
										    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>
							    </div>
							    <div class="col-sm-6">
									<label for="inp-type-2" class="col-sm-12 ">Supérieur hiérarchie :</label>
									<div class="col-sm-12">
										<select type="text" class=" chosen form-control" id="inp-type-1" name="sup" >
											<?php $__currentLoopData = $listsup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											    <option value="<?php echo e($sup->codeH); ?>"><?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($sup->managerH)); ?></option>
										    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>
							    </div>
							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Référence : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="ref" value="" required>
										</div>
									</div>								
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Description : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="desc" value="" required>
										</div>
									</div>								
								

							</div>
							<div class="form-group">
								
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> La note : </label>
										<div class="col-sm-12">
											<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="note" required>
										</div>
								
								</div>
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Date éffet : </label>
										<div class="col-sm-12">
											<input type="date" class="form-control" id="inp-type-1"  name="dateeffet" value="<?php echo e(date('d/m/Y')); ?>" required>
										</div>
																	
								</div>
							</div>
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-6">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Enregistrer
				                    </button>
							    </div>
							</div>
							<div class="form-group" style="display: none;" id="modify">
							    <div class="col-sm-12">
				                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; margin-top: 20px; margin-left: 15px; width: 25%;">Mettre à jour
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

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control"> 
				Liste des inspections NSIA :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing"> 

					<!------------------------------------------>
					<form class="form-horizontal" action="" id="recherche">
						<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-right: 30px; float: right;">
								<input class=" form-control" type="text" id="search" placeholder="Rechercher "  >
							</div>
						</div>
					</form>
					<script>		
						
						function getXMLHttpRequest() {
							var xhr = null;
							
							if (window.XMLHttpRequest || window.ActiveXObject) {
								if (window.ActiveXObject) {
									try {
										xhr = new ActiveXObject("Msxml2.XMLHTTP");
									} catch(e) {
										xhr = new ActiveXObject("Microsoft.XMLHTTP");
									}
								} else {
									xhr = new XMLHttpRequest(); 
								}
							} else {
								alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
								return null;
							}
							
							return xhr;
						}
						function readData(sData) {
							//alert(sData);
							var oSelect = document.getElementById("data");
							
							oSelect.innerHTML = sData;
						}
						
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "<?php echo e(route('listI')); ?>?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keydown", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "<?php echo e(route('listI')); ?>?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);

						var y = document.getElementById("recherche");
						y.addEventListener("keyup", function () {
						  search = document.getElementById("search").value;
						  var xhr = getXMLHttpRequest(); 
							xhr.open("GET", "<?php echo e(route('listI')); ?>?check="+search+"&rec=1", true);
							xhr.send(null);
							xhr.onreadystatechange = function() {
							    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							        readData(xhr.responseText);
							    }
							};
						}, true);
						
					</script>
					
                    <!------------------------------------------->
					
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th class="textcenter">Code</th>
									<th class="textcenter">Catégorie</th>
									<th class="textcenter" data-priority="1">Libellé</th>
									<th class="textcenter" data-priority="1">Chef d'inspection</th>
									<th class="textcenter" data-priority="1">Supérieur</th>
									<th class="textcenter" data-priority="1">Ville</th>
									<th class="textcenter" data-priority="3">Action Utilisateur</th>
									<th style="text-align: center; vertical-align:middle;" data-priority="6">Actions</th>
								</tr>
							</thead>
							<tbody id="data">

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
									<?php if(in_array("update_insp", session("auto_action"))): ?>
									<button type="button" title="Mutation"  class="btn btn-success btn-circle btn-xs  margin-bottom-10 waves-effect waves-light">
									    <a href="<?php echo e(route('GMutationI', $ins->codeH)); ?>" style="color:white;"><i class="ico fa fa-mail-forward"></i> </a>
									</button>
									<?php endif; ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="8"><center>Pas d'inspections enregistrer!!! </center></td>
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
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
          
          function passer($val) {
			  var x = document.getElementById("modify");
			  var y = document.getElementById("Ajouter"); 
			  if (y.style.display == "block") {
			  	y.style.display = "none";
			    x.style.display = "block";
			  } else {
			    x.style.display = "none";
			    y.style.display = "block"
			  }
			}


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