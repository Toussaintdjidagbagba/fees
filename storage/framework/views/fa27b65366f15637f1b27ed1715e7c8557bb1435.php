

<?php $__env->startSection('content'); ?>



	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
			 Liste des documents de fiches de paie NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button> 
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">  

					<form class="form-horizontal" action="<?php echo e(route('listdocumentset')); ?>" method="post">
						<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
						<div class="form-group">
							<div class="col-sm-3" style="margin-left: 40px;">
								<label style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Mois calculé : </label>
								<input class="form-control" type="month" name="check" value="" min="2021-12" max="<?php echo e(date('Y-m', strtotime('-1 month'))); ?>">
								
							</div>
							<div class="col-sm-3" style="margin-left: 40px;">
								<label style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">Apporteur : </label>

								<input class=" form-control" type="text" name="agentcheck" placeholder="Rechercher.."  >
							</div>
							<div class="col-sm-2" style="margin-left: 0px;">
							    <label style="vertical-align:middle; margin-top: 1%; font-style: bold;" class="col-sm-12">. </label>
								
									<button style="width : 50px" class="btn btn-primary form-control" type="submit" name="rec" valu="1" id="search"> 
									    <span style="font-size : 40px; margin-top: -5px; margin-left: -13px; " id="idsearch" class="iconify" data-icon="ci:search"></span>
									</button>
								
							</div>
						</div>
					</form>
					
			<div class="col-xs-12">
				<div class="box-content" id="data">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Apporteur</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Fiche de paie</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Détail</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Fiche de paie (Duplicata)</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Détail (Duplicata)</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Période</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Statut Mail </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Renvoyer Mail </th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Observations </th>
								</tr>
							</thead>
							<tbody >
								<?php if(isset($list) && count($list) != 0 ): ?>
								<?php for($i = 0; $i < count($list); $i++): ?>
				
								<tr>
									
									<td style="vertical-align:middle; text-align: center;"><?php echo e($list[$i]->Agent); ?></td> 
									<?php if(in_array("downloader_fiche", session("auto_action"))): ?>
									<td style="vertical-align:middle; text-align: center;"><a href="<?php echo e(url($list[$i]->path)); ?>" target="_blank">Télécharger</a></td>
									<?php else: ?>
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									<?php endif; ?>
									<?php if(in_array("downloader_detail", session("auto_action"))): ?>
									<td style="vertical-align:middle; text-align: center;"><a href="<?php echo e(url($list[$i]->pathD)); ?>" target="_blank">Télécharger</a></td>
									<?php else: ?>
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									<?php endif; ?>
									<?php if(in_array("downloader_fiche_duplicata", session("auto_action"))): ?>
									<td style="vertical-align:middle; text-align: center;"><a href="<?php echo e(url($list[$i]->pathFD)); ?>" target="_blank">Télécharger</a></td>
									<?php else: ?>
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									<?php endif; ?>
									<?php if(in_array("downloader_detail_duplicata", session("auto_action"))): ?>
									<td style="vertical-align:middle; text-align: center;"><a href="<?php echo e(url($list[$i]->pathDD)); ?>" target="_blank">Télécharger</a></td>
									<?php else: ?>
									<td>Vous n'avez pas l'autorisation de télécharger.</td>
									<?php endif; ?>

									<td data-priority="1" style="vertical-align:middle; text-align: center;"><?php echo e($list[$i]->periode); ?></td>
									<?php if($list[$i]->statut =="true"): ?>                                 
									    <td data-priority="1" style="vertical-align:middle; text-align: center;">Envoyé</td>
									<?php else: ?>
    									<?php if($list[$i]->type != null): ?>
    									    <td data-priority="1" style="vertical-align:middle; text-align: center;">Non envoyé</td>
    									<?php else: ?>
    									    <td data-priority="1" style="vertical-align:middle; text-align: center;">En cours..</td>
    									<?php endif; ?>
									<?php endif; ?>
									<?php 
									    $name = App\Providers\InterfaceServiceProvider::Libellecom($list[$i]->Agent);
					    			    $compte = array('Agent' => $list[$i]->Agent, 'nom' => $name, 'periode' => $list[$i]->periode);
										$comptejson = json_encode($compte);
									?>
									<td data-priority="1" style="vertical-align:middle; text-align: center;">
									    <a class="dropdown-item identifyingeqp" href="#add" data-target="#add" data-toggle="modal" data-id="<?php echo e($comptejson); ?>renv" title="Renvoyé ?">Renvoyé ?</a>
									</td>
									<td data-priority="1" style="vertical-align:middle; text-align: center;">
									    <?php echo e($list[$i]->type); ?>

									</td>
								</tr>
								<?php endfor; ?>
								<?php else: ?>
								<tr>
									<td colspan="10"><center>Pas de document disponible!!! </center></td>
								</tr>
								<?php endif; ?>
								
							</tbody>
						</table>
						<?php echo e($list->links()); ?>



						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

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

<?php $__env->startSection('js'); ?>
    <script type="text/javascript">
	    $(function () {
	        $(".identifyingeqp").click(function () {
	            var id = $(this).data('id');
	            var div = document.getElementById('tst');
	            
	            if (id != 0) {

	            	if(id.substr(-4, 4) == "renv"){
	            	    identifiant = id.slice(0, id.length - 4);
	            	    var json = JSON.parse(identifiant);

                    	div.innerHTML = '<div class="modal-header">' +
							'<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button>' +
                            '<h4 class="modal-title" id="myModalLabel">Renvoyer FICHE DE PAIE : </h4>' +
                            '</div><div class="modal-body">' +
                            '   <form class="form-horizontal" method="post" action="<?php echo e(route("RFPC")); ?>">' +
	                        '      <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />' +
	                        '      <input type="hidden" name="codeagent" value="'+json.Agent+'" />' +
	                        '      <input type="hidden" name="periode" value="'+json.periode+'" />' +
	                        '      <div class="form-group">  ' +
	                        '           <label for="inp-type-2" class="col-sm-12 ">Voulez-vous vraiment renvoyer la fiche de paie du commercial '+json.nom+' ( '+json.Agent+' ) de la période du '+json.periode+' ?</label>' +
	                        '      </div>       ' +
	                        '    <div class="modal-footer">   ' +
	                        '    <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">OUI</button>' +
	                        '    <button type="bouton" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect ' +
	                        '    waves-light" style="float:right; color:white;">NON</button></div>';
                            
	            	}
	            	    
	            }

      			
	        })
	    });
	</script>
	            	
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
    </script>

    <script type="text/javascript">
	    $(function () {
	    	$("#add").on('hidden.bs.modal', function () {
		        window.location.reload();
		    });
	    });
	</script>
	<script src="https://code.iconify.design/2/2.1.2/iconify.min.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("dstestyle"); ?>
  <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    
    <style>

    	.btn {
      background: #6495ed;
      background-image: -webkit-linear-gradient(top, #6495ed, #2980b9);
      background-image: -moz-linear-gradient(top, #6495ed, #2980b9);
      background-image: -ms-linear-gradient(top, #6495ed, #2980b9);
      background-image: -o-linear-gradient(top, #6495ed, #2980b9);
      background-image: linear-gradient(to bottom, #6495ed, #2980b9);
      -webkit-border-radius: 7;
      -moz-border-radius: 7;
      border-radius: 7px;
      text-shadow: 7px 22px 15px #8a7c8a;
      font-family: Arial;
      color: #ffffff;
      font-size: 12px;
      padding: 10px 20px 10px 20px;
      text-decoration: none;
    }
    
    .btn:hover {
      background: #212f68;
      background-image: -webkit-linear-gradient(top, #212f68, #6495ed);
      background-image: -moz-linear-gradient(top, #212f68, #6495ed);
      background-image: -ms-linear-gradient(top, #212f68, #6495ed);
      background-image: -o-linear-gradient(top, #212f68, #6495ed);
      background-image: linear-gradient(to bottom, #212f68, #6495ed);
      text-decoration: none;
    }

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('model'); ?>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tst">

		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstejs"); ?>
<script type="text/javascript">
    $(".chosen").chosen();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>