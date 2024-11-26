

<?php $__env->startSection('content'); ?>

	<div class="col-lg-12 col-md-12 col-xs-12"> 

		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Liste des quittances NSIA : 
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<div class="col-xs-12"><center style="border-radius: 10px;top: 75%;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center></div>
				<div class="row small-spacing">
				    <?php if(in_array("add_commercial", session("auto_action"))): ?> 
    					<a class="btn-primary dropdown-item" style="color: white; margin-right: 10px; padding: 8px;float:right;" 
					href="" id="exp">EXPORTER QUITTANCES <i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>
    				<?php endif; ?>
    				
    				
						<input type="hidden" name="_token" id="_token" value="<?php echo e(csrf_token()); ?>" />
						<div class="form-group">
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<input class="form-control"  type="search" name="check" onsearch="rechercher()" onblur="rechercher()" id="check" placeholder="Rechercher police ou quittance">
								</div>
							</div>
							<div class="col-sm-12 col-xs-12 col-lg-3" style="margin: 10px;">
								<div class="search col-sm-12">
									<select class="form-control" type="search" name="checktype" onsearch="trietype()" onchange="trietype()" id="checktype">
									    <option value="">Trier par période</option>
									    <option></option>
									</select>
								</div>
							</div>
						</div>
					
					<script>
					    async function rechercher(){
					        token = document.getElementById("_token").value;
					        check = document.getElementById("check").value;
					        
						    try {
        						  let response = await fetch("https://fees.nsiaviebenin.com/seachquittance?_token="+token+"&seach="+check, 
        						  {
        						    method: 'GET',
                            		headers: {
                            			'Access-Control-Allow-Origin': 'https://fees.nsiaviebenin.com/seachquittance',
                            			'Access-Control-Allow-Credentials': true,
                            			'Content-Type': 'application/json',
                            			'Accept': 'application/json',
                            		},
        						  });
                                let html = "";
                              if(response.status == 200)
                              {
                                  data = await response.text();
                                  suc = JSON.parse(data).success;
                                  list = JSON.parse(data).data;
                                  if(suc == true){
                                      list.forEach(function(quittance){
                                            html += "<tr>";
                                            html += '<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom('+quittance.app+')); ?>('+quittance.app+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.police+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.quittance+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.payeur+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.nom+' '+quittance.prenom+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.periodequittance+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.produit+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.periode+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">';
        									if(quittance.etat == '2' || quittance.etat == 2) 
        									html += 'Payer'; 
        									else
        									    if(quittance.etat == 1 || quittance.etat == '1') 
        									        html += 'En cours de payement';
        									    else
        									        if(quittance.etat == 3 || quittance.etat == '3')
        									            html += 'Retenue';
        									        else
        									            html += 'Non payer'; 
        									html += '</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.base+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.montcons+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom('+quittance.equipe+')); ?>('+quittance.equipe+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.montceq+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom('+quittance.ins+')); ?>('+quittance.ins+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.montins+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom('+quittance.region+')); ?>('+quittance.region+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.montrg+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom('+quittance.cd+')); ?>('+quittance.cd+')</td>';
        									html += '<td style="vertical-align:middle; text-align: center;">'+quittance.montcd+'</td>';
        									html += '<td style="vertical-align:middle; text-align: center;"><i class="ico fa fa-edit"></i></td>';
                                            html += "</tr>";
                                      });
                                  }else{
                                      
                                      html += "<tr>";
                                      html += "<td class='textcenter' colspan='20'> Cette quittance n'existe pas. </td>";
                                      html += "</tr>";
                                  }
                                  document.getElementById("data").innerHTML = html;
                              }
						    } catch (error) {
                                let html = "";
                                html += "<tr>";
                                html += "<td class='textcenter' colspan='20'> Cette quittance n'existe pas.</td>";
                                html += "</tr>";
                                document.getElementById("data").innerHTML = html;
                            }
					    }
						
					</script>
					
                    <!------------------------------------------->
			<div class="col-xs-12">
				<div class="box-content">
					<div class="table-responsive" data-pattern="priority-columns">
						<table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
							<thead>
								<tr>
									<th data-priority="1" style="vertical-align:middle; text-align: center;">Apporteur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Police</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Quittance</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Payeur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Client</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Période Quittance</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Produit</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Période</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Etat</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Base de Commission</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commission</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Chef d'Equipe</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commission CEQ</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Inspecteur</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commission Ins.</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Chef Région</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commission Reg.</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Chef Coordination</th>
									<th data-priority="1" style="vertical-align:middle;text-align: center;">Commissions Coord.</th>
									<th data-priority="6" style="vertical-align:middle;text-align: center;">Actions</th>
								</tr>
							</thead>
							<tbody id="data">
								
								<?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom($com->app)); ?>(<?php echo e($com->app); ?>)</td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->police); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->quittance); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->payeur); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->nom); ?> <?php echo e($com->prenom); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->periodequittance); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->produit); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->periode); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php if($com->etat == 2): ?> Payer <?php else: ?> <?php if($com->etat == 1): ?> En cours de payement <?php else: ?> <?php if($com->etat == 3): ?> Retenue <?php else: ?>  Non payer <?php endif; ?> <?php endif; ?> <?php endif; ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->base); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->montcons); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom($com->equipe)); ?>(<?php echo e($com->equipe); ?>)</td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->montceq); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom($com->ins)); ?>(<?php echo e($com->ins); ?>)</td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->montins); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom($com->region)); ?>(<?php echo e($com->region); ?>)</td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->montrg); ?></td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e(App\Providers\InterfaceServiceProvider::Libellecom($com->cd)); ?>(<?php echo e($com->cd); ?>)</td>
									<td style="vertical-align:middle; text-align: center;"><?php echo e($com->montcd); ?></td>
									<td style="vertical-align:middle; text-align: center;"><i class="ico fa fa-edit"></i></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr>
									<td colspan="20"><center>Pas de quittance disponible!!! </center></td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<?php echo e($list->links()); ?>

					</div> 
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('model'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('dstestyle'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css" />
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>