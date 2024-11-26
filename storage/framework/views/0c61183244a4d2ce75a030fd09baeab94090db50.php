

<?php $__env->startSection('content'); ?>

	<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;" id="infoconfirme"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>


<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Règlement des commissions groupe:
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				 
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >
                    <form class="form-horizontal" method="post" action="<?php echo e(route('GRTSG')); ?>" id="vue">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                        <div class="form-group">
								<div class="col-sm-5">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Code QR des commissions concernées : </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1" value="<?php echo e(session('qrg')); ?>" name="qr" id="qr" required>
										<input type="hidden" name="recherchevue" value="1">
									</div>
									
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Montant global : </label>
								<div class="col-sm-10">
									<input type="number" class="form-control" id="inp-type-2" value="<?php echo e(session('montallg')); ?>" name="mont" readonly>
								</div>
								<div class="col-sm-2">
							    	<button type="submit" class="form-control btn btn-primary" style="font-size:25px"  name="vue" value="vue">
							    		<i class="bi bi-eye"></i>
							    	</button>
							    </div>
							    </div>			
							</div>
				    </form>
				    <script>
					
						var y = document.getElementById("vue");
						y.addEventListener("blur", function () {
							document.getElementById("vue").submit();
						}, true);
						
						

						/*var y = document.getElementById("vue");
						y.addEventListener("keydown", function () {
						  document.getElementById("vue").submit();
						}, true);*/

						/*
						var y = document.getElementById("recherche");
						y.addEventListener("keyup", function () {
						  const input = document.getElementById("sub")
                          input.click()
						}, true); */
						
					</script>
				</div>
				<!-- /.box-content -->
			</div>
			</div>
			</div>

			
		</div>

		
	</div>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Enregistrement des règlements par mode de paiement :
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<?php if(in_array("save_reglement_com", session("auto_action"))): ?>
			<div class="js__card_content">
				 
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >

					<form class="form-horizontal" method="post" action="<?php echo e(route('SRTG')); ?>">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
							<input type="hidden" value="<?php echo e(session('qrg')); ?>" name="qr">
                    
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Référence de paiement: </label>
									<div class="col-sm-12">
										<input type="text" class="form-control" id="inp-type-1"  name="refpaiement" required>
									</div>
							    </div>
								<div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Date : </label>
								<div class="col-sm-12">
									<input type="date" class="form-control" id="inp-type-2" name="datpaiement" required>
								</div>
							    </div>			
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12  ">Mode de paiement : </label>
									<div class="col-sm-12">
										<select type="number" class="form-control" id="inp-type-3" name="modepaiement" required>
										<?php $__currentLoopData = $listPayement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										        <option value="<?php echo e($pay->sigle); ?>"><?php echo e($pay->libelle); ?></option>
										    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
									</div>
							    </div>	
							    <div class="col-sm-6">
								<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12">Montant règlement : </label>
								<div class="col-sm-12">
									<input type="number" class="form-control" id="inp-type-2" name="montreglement" required>
								</div>
							    </div>		
							</div>
							
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-6">
				                    <button type="submit" name="enr" value="enr" class="btn btn-primary btn-sm waves-effect waves-light" style="float:left; margin-top: 20px; margin-left: 15px; width: 25%;">Enregistrer
				                    </button>
							    </div>
							</div>
					</form>	
				</div>
				<!-- /.box-content -->
			</div>
			</div>
			</div>
			<?php endif; ?>

			<div class="js__card_content">
				
			<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<?php
								$total = 0;
									// Calcul des Totales
								if (session('allreglementg') != "") {
									foreach($allreglementg as $com){
										$total += $com->Montant;
									}
								}
							 ?>
								<tr>
									<th colspan="3"> </th>
									<<th data-priority="3">Montant Total : </th>
									<th data-priority="6"><center> <?php echo e(number_format($total, 0, '.', ' ')); ?> CFA </center></th>
								</tr>
								<tr>
									<th data-priority="1">Moyen de paiement</th>
									<th>Référence</th>
									<th data-priority="3">Montant du règlement</th>
									<th data-priority="3">Date</th>
									<th data-priority="6"><center>Actions</center></th>
								</tr>
							</thead>
							<tbody>

								<?php if(session('allreglementg') != ""): ?>
									<?php $__currentLoopData = session('allreglementg'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reglement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
									<th data-priority="1"><?php echo e($reglement->ModePaiement); ?></th>
									<th><?php echo e($reglement->RefPaiement); ?></th>
									<th data-priority="3"><?php echo e(number_format($reglement->Montant, 0, '.', ' ')); ?></th>
									<th data-priority="3"><?php echo e($reglement->Date); ?></th>
									<th data-priority="6"><center>
										<button type="button" title="Supprimer"  class="btn btn-danger btn-circle btn-xs  margin-bottom-10 waves-effect waves-light"><a href="/delete-reglementgroupe-<?php echo e($reglement->idReglement); ?>" style="color:white;"><i class="ico fa fa-trash"></i></a> </button>
									</center></th>
								</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>
							</tbody>
							<?php if(in_array("save_reglement_com", session("auto_action"))): ?>
							<tfoot>
								<tr>
									<th colspan="4"></th>
									<th><center id="ferm"><a onClick="validecomm();" href="#"  id="validerreglement" class="btn btn-primary">VALIDER</a></center></th>
								</tr>
							</tfoot>
							<?php endif; ?>
							
						</table>
						
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
              var oSelect = document.getElementById("infoconfirme");
              
              oSelect.innerHTML = '<div class="alert alert-info" role="alert">'+sData+'</div>';
            }
            
            function validecomm(){
                readData("Lancement des calcules.."); 
                var d = document.getElementById("validerreglement");
                d.onclick = null; d.style.display="none"; 
                var f = document.getElementById("ferm");
                f.innerHTML = "Veuillez patienter..";
            
                var xhr = getXMLHttpRequest(); 
                  xhr.open("GET", "<?php echo e(route('SCTVG')); ?>", true);
                  xhr.send(null);
                  xhr.onreadystatechange = function() {
                      if (xhr.readyState == 1 || xhr.readyState == 2 || xhr.readyState == 3 ) {
                          console.log("En cours de traitement des commissions. Le processus peut prendre quelques minutes. Veuillez patienter jusqu'à la fin.  ");
                          readData("En cours de traitement des commissions. Le processus peut prendre quelques minutes. Veuillez patienter jusqu'à la fin. "); 
                          var g = document.getElementById("ferm");
                          g.innerHTML = "En cours de traitement des commissions. Le processus peut prendre quelques minutes. Veuillez patienter jusqu'à la fin.";
                      } 
                          
                      if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) { 
                          console.log(xhr.responseText);
                          readData(xhr.responseText);
                          var g = document.getElementById("ferm");
                          g.innerHTML = xhr.responseText;
                          
                      }
                      
                  };
              
            }
            
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>