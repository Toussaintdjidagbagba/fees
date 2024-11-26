

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				<?php setlocale(LC_ALL, 'fr_FR', 'fra_FRA') ?>
				Liste Commissions <?php echo e(view()->shared('periodelettre')); ?> NSIA ( <?php echo e($libelleRole); ?> ) : 
			
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">
					<!------------------------------------------>
					<form class="form-horizontal" action="<?php echo e(route('GCT')); ?>" method="GET" id="recherche">
						<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
						<div class="form-group">
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin-right: 0px; float: right;">
								<input class=" form-control" type="hidden" name="rec" value="1">
								<div class="search col-sm-12">
									<input class=" form-control" type="text" name="check" placeholder="<?php echo e($search); ?> ">
								    <input class=" form-control" type="submit" id="sub">
								</div>
							</div>
						</div>
					</form>
					
					
                    <!------------------------------------------->
					<form class="form-horizontal" method="post" action="<?php echo e(route('SCT')); ?>" id="regle">
										<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />

										<div class="form-group">
										<div class="col-sm-6 col-xs-12 col-lg-6" style="margin-top:-70px">
											<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Trier par réglement : </label>
											<div class="col-sm-5">
												<select type="number" class="form-control" id="inp-type-3" name="reglement" required>
													<?php if($reglement != null && $reglement=="" && $reglement != "all"): ?>
													<option value="<?php echo e($reglement); ?>"><?php echo e($reglement); ?></option>
													<?php endif; ?>
												<option value="all">Tous les règlements</option>
												<?php $__currentLoopData = $listPayement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												    <option value="<?php echo e($pay->sigle); ?>"><?php echo e($pay->libelle); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											</div>
											<div class="col-sm-2">
												<button type="submit" class="form-control" name="recherche" value="rech" id="rech">
													<i class="fa fa-search"></i>
												</button>
											</div>
									    </div>	
									</div>
				    <div id="ref">
						Ref  <b style="color:#001e60">SP <img src="<?php echo e($signaturesp); ?>" id="signataire"></b>/<b style="color:#001e60">CSP <img src="<?php echo e($signaturecsp); ?>" id="signataire"> </b>/<b style="color:#001e60">DT  <img src="<?php echo e($signaturedt); ?>" id="signataire"> </b>/ <b style="color:#001e60">DG <img src="<?php echo e($signaturedg); ?>" id="signataire"> </b>/ <b style="color:#001e60">CDAF <img src="<?php echo e($signaturecdaf); ?>" id="signataire"> </b> / <b style="color:#001e60">T </b>/<?php echo e($sigle); ?>

					</div>
					<?php if(in_array("rejet_com", session("auto_action"))): ?>
					<a class="btn-danger dropdown-item autrecom" id="valid" href="#autrCom"
					   data-toggle="modal" data-id="<?php echo e($libelleRole); ?>reje" title="">Rejeter <i class="fa fa-ban" aria-hidden="true"></i> </a>
					<?php endif; ?>
                    <?php if(in_array("reglement_com", session("auto_action"))): ?>
					<a class="btn-warning dropdown-item " id="valid" href="<?php echo e(route('GRT')); ?>"
					   >Règlement <i class="fa fa-bars" aria-hidden="true"></i> </a>
					<?php endif; ?>
					<?php if(in_array("export_pdf_com", session("auto_action"))): ?>
					<button class="btn-warning dropdown-item " id="valid" type="submit" name="PDF" value="PDF">
						Exporter en PDF <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
					</button>
					<?php endif; ?>
					<?php if(in_array("export_excel_com", session("auto_action"))): ?>
					<button class="btn-warning dropdown-item " id="valid" type ="submit" name="Excel" value="Excel"> 
						Exporter en Excel <i class="fa fa-file-excel-o" aria-hidden="true"></i> 
					</button>
					<?php endif; ?>
					</form>
					<script>
					/*
						var y = document.getElementById("recherche");
						y.addEventListener("blur", function () {
							const input = document.getElementById("sub")
                            input.click()
						}, true);

						var x = document.getElementById("regle");
						x.addEventListener("change", function () {
							const input = document.getElementById("rech")
                            input.click()
						}, true); */

						/*
						var y = document.getElementById("recherche");
						y.addEventListener("keydown", function () {
						  const input = document.getElementById("sub")
                            input.click()
						}, true);*/

							/*
						var y = document.getElementById("recherche");
						y.addEventListener("keyup", function () {
						  const input = document.getElementById("sub")
                          input.click()
						}, true); */
						
					</script>

			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<?php
								$apayer = 0;
									// Calcul des Totales
								if (isset($list)) {
									foreach($list as $com){
									    $comp = App\Providers\InterfaceServiceProvider::RecupCompte($com->Commercial);
										$apayer += $comp->compte;
									}
								}
							 ?>
							<tr>
								<th colspan="4" rowspan="2" data-priority="1" style="vertical-align:middle;">
								</th>
								<th colspan="1" data-priority="1" style="vertical-align:middle; text-align: center;">Montant Total</th>
							</tr>
							<tr>
								
								<th data-priority="1" style="vertical-align:middle;text-align: center;"> <?php echo e($apayer); ?> CFA </th>

							</tr>
							<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Code Apporteur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Nom et prénom Apporteur</th>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Libellé Règlement</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Numéro Règlement</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commission Nette à Payer</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1; ?>
								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
								    <?php
								        $comp = App\Providers\InterfaceServiceProvider::RecupCompte($com->Commercial);
								    ?>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->Commercial); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::infomanageur($com->Commercial)); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($comp->ModePayement); ?> <?php echo e($comp->libCompte); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($comp->numCompte); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($comp->compte); ?> CFA</td>
									
								</tr>
								<?php $i++; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="9"><center>Commission indisponible pour ce mois!!! </center></td>
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

<?php $__env->startSection('js'); ?>
	<script>
        $('#flash-overlay-modal').modal();
        $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
    </script>
    <script type="text/javascript">
	    $(function () {
	        $(".autrecom").click(function () {
	            var id = $(this).data('id');
	            var div = document.getElementById('tst');
	            if(id.substr(-4, 4) == "autr"){
	            	identifiant = id.slice(0, id.length - 4);
	            	var json = JSON.parse(identifiant);

	            	div.innerHTML = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Imputer une autre commission : </h4></div><div class="modal-body"><form class="form-horizontal" method="post" action="<?php echo e(route("SAC")); ?>"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" /><input type="hidden" name="codecom" value="'+json.com+'" />                                <div class="form-group"> <div class="col-sm-12">  <label for="inp-type-2" class="col-sm-12 ">Autre commission actuelle :</label>   <div class="col-sm-12"><input type="text" class="form-control" id="inp-type-2" name="solde" value="'+json.autresolde+'" disabled="true"></div> </div> </div>        <div class="form-group"> <div class="col-sm-12">  <label for="inp-type-2" class="col-sm-12">Saisir autre commission à ajouter : </label><div class="col-sm-12"><input type="number" class="form-control" id="inp-type-2" min="0" name="soldeautre"></div> </div>                         </div><div class="modal-footer">   <button type="submit" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;">IMPUTER</button>   <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button></div>';
				}

            if(id.substr(-4, 4) == "reje"){
            	identifiant = id.slice(0, id.length - 4);

                div.innerHTML = '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">Rejeter : </h4>' +
                    '</div><div class="modal-body">' +
                    '<form class="form-horizontal" method="post" action="<?php echo e(route("RCT")); ?>">' +
                        '<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />' +
                        
                    '<div class="form-group"> ' +
                    '<div class="col-sm-12" style="font-size:20px"> ' +
                	' <label for="inp-type-2" class="col-sm-12">Motif du rejet : </label>' +
					'<div class="col-sm-12"><input type="text" maxlength="300" class="form-control" id="inp-type-2" name="motif">' +
					'</div> ' +
                    '</div>' +
                    
                    '<button type="submit" class="btn btn-danger btn-sm waves-effect waves-light" style="float:left; margin-left:30px; margin-top:15px; color:white;">' +
					'Rejeter <i class="ico fa fa-check"></i></button>' +
                    '</div> '+
                    '</div>' +
                    '<div class="modal-footer">   ' +
                    
                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                    '</div> </form>';
            }

            if(id.substr(-4, 4) == "conf"){
                div.innerHTML = '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title" id="myModalLabel">Confirmation : </h4>' +
                    '</div><div class="modal-body">' +
                    ' <div class="form-group"> ' +
                    '<div class="col-sm-12" style="font-size:20px"> ' +
                    '  Voulez-vous vraiment confirmer les commissions calculées ? <br><br> ' +
                    '</div>' +
                    '</div>    </div>' +
                    '<div class="modal-footer">   ' +
                    '<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" style="float:left; color:white;"><a href="<?php echo e(route("listCommConfirm")); ?>">' +
                    'Confirmer <i class="ico fa fa-check"></i></a></button>  ' +
                    ' <button type="button" data-dismiss="modal" class="btn btn-primary btn-sm waves-effect waves-light" style="float:right; color:white;">FERMER</button>' +
                    '</div>';
            }
            });
	    });
	</script>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('model'); ?>

<div class="modal fade" id="autrCom" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="tst">
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('dstestyle'); ?>
<style>
.dropdown-item{
	font-family: "Open Sans", sans-serif;
  font-size: 15px;
  font-weight: 400;
  color: #333;
  display: inline-block;
  padding: 15px;
  position: relative;
}
#ref{
	float:left; margin-left: 30px; font-size: 20px;
}
#valid{
	box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px; color: black; float:right; padding: 10px; margin-right: 30px
}
#signataire{
	height: 20px;
	width: 20px;
	margin-left: -15px;
	margin-top: -28px;
}
#rech{
	background-image: url("");
	height: 100%;
	width: 50px;
}
.search input[type="text"]
	{
	  padding: 4px 10px;
      height: 42px;
	  background: none;
	  border: 0.5 none;
	  float: left;
	  line-height: 1.5em;
	  margin: 0;
	  width: 200px;
	}

	.search input[type="submit"]
	{
		padding: 4px 10px;
      height: 42px;
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