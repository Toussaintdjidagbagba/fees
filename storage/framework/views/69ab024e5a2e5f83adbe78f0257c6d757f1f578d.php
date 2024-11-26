

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			 
			<h4 class="box-title with-control">
			
				Adhéré une coordination NSIA :
			<span class="controls">
					<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">   
				
					<button type="button" style="float:right;" class="btn btn-icon btn-icon-right btn-warning btn-sm waves-effect waves-light"><a href="<?php echo e(route('listC')); ?>" style="color:white"><i class="ico fa fa-mail-reply"></i>Retour</a></button>
			<div class="col-xs-12">

				<div class="box-content">
					
					<form class="form-horizontal" method="post" action="<?php echo e(route('SAdhCoord')); ?>" enctype="multipart/form-data"  >
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
							<!-- Formulaire -->
							<div class="col-sm-6">
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code commercial : </label>
										<div class="col-sm-12">
											<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> <!-- Limite 5Mo -->
											<input type="hidden"  name="codeC" value="<?php echo e($affcom->codeCom); ?>">
											<input type="hidden"  name="codeeexistant" value="<?php echo e($affcom->codeEquipe); ?>">
											<input type="text" class="form-control" id="inp-type-1"  name="codeh" value="<?php echo e($affcom->codeCom); ?>" disabled="true">
										</div>
										</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Nom et prénom : </label>
										<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" disabled="true"  name="lib" value="<?php echo e($affcom->nomCom); ?> <?php echo e($affcom->prenomCom); ?>">
									</div>
									</div>			
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Coordination actuelle : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" disabled="true" id="inp-type-1"  name="ville" value="<?php echo e($affcom->codeCD); ?>">
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Référence : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="ref" value="" required>
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Description : </label>
										<div class="col-sm-12">
											<input type="text" class="form-control" id="inp-type-1"  name="desc" value="" required>
										</div>
									</div>								
								</div>
								
								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> La note : </label>
										<div class="col-sm-12">
											<input type="file" accept=".pdf" class="form-control" id="inp-type-1"  name="note" required>
										</div>
									</div>								
								</div>

								<div class="form-group">
									<div class="col-sm-12">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  "> Date éffet : </label>
										<div class="col-sm-12">
											<input type="date" class="form-control" id="inp-type-1"  name="dateeffet" value="<?php echo e(date('d/m/Y')); ?>" required>
										</div>
									</div>								
								</div>
								
								<div class="form-group" style="display: block;" >
										<div class="col-sm-12">
															<button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Affecter
															</button>
											
										</div>
								</div>
						</div>
						<!-- Listes des  équipes -->
						<div class="col-sm-6">
								<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Recherche par code équipe">

								<table id="myTable">
									<tr>
										<th></th>
										<th>
											Code Coordination
										</th>
										<th>
											Nom Chef Coordination
										</th>
									</tr>
									<?php $__empty_1 = true; $__currentLoopData = $allcoord; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eqp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
										<tr>
									  	<td><input type= "radio" name="coordselect" value="<?php echo e($eqp->codeH); ?>" required></td>
									    <td><?php echo e($eqp->codeH); ?></td>
									    <td><?php echo e(App\Providers\InterfaceServiceProvider::infosup($eqp->codeH)); ?></td>
									  </tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
									<tr>
										<td colspan="3"><center>Pas de coordination disponible !! </center></td>
									</tr>
									<?php endif; ?>
							  
							  
							</table>

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
		<script>
			
		</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstejs"); ?>
<script type="text/javascript">
		$(".chosen").chosen();
</script>
<script type="text/javascript">
	function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>