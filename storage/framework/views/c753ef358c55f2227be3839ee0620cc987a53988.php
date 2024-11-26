
<?php 

ini_set('upload_max_filesize', '20M');

echo "bien";

?>

<?php $__env->startSection('content'); ?>

<div class="col-xs-12"></div>

	<div class="col-lg-12 col-md-12 col-xs-12"> 
        <center style="border-radius: 10px;top: 75%;" id="data"></center> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center>
		<div class="box-content bordered info js__card">
			
			<h4 class="box-title with-control">
				Importation manuelle : Calcul en cours...<?php echo e(App\Providers\InterfaceServiceProvider::pourcent()); ?>%
			<span class="controls">
	     		<button type="button" class="control fa fa-minus js__card_minus"></button>
			</span>
			</h4>
			<div class="js__card_content">
				<?php if(in_array("import_commission", session("auto_action"))): ?>
					    
        				    <a href="<?php echo e(route('vcip')); ?>" class="cibutton btn-sm waves-effect waves-light" >
        				        VALIDER IMPORTATION INDIVIDUEL
        				    </a>
					<?php endif; ?>
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
                        
                    	function validii(){
                                console.log("Lancer");
                            
                            document.getElementById("data").innerHTML = '<div class="myButton"> En cours de validation.. ';
                            
                            var xhr = getXMLHttpRequest(); 
                            xhr.open("GET", "<?php echo e(route('vcip')); ?>", true);
                            xhr.send(null);
                            xhr.onreadystatechange = function() {
                              if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                                  var data = JSON.parse(xhr.responseText);  
                                    if(data.response == 1){
                                        console.log(data.message);
                    					document.getElementById("data").innerHTML = '<div class="myButton"> '+data.message;
                    				}
                              }
                            }; 
                                
                        }
                    </script>
				<div class="row small-spacing"> 
			 <div class="col-xs-12">
				<div class="box-content" >
				 <form class="form-horizontal" method="post" action="<?php echo e(route('SlistCommission')); ?>"  enctype="multipart/form-data">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
							
							<div class="form-group">
								<?php if(in_array("import_commission", session("auto_action"))): ?>
								<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Commission : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="comm">
										</div>
								</div>
								<?php endif; ?>
								<?php if(in_array("import_contrat", session("auto_action"))): ?>
									<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Contrat : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="contrat">
										</div>							
								</div>
								<?php endif; ?>
							</div>
							<div class="form-group">
								<?php if(in_array("import_commission_hors", session("auto_action"))): ?>
								<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Commission hors SunShine : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="commhors">
										</div>
								</div>
								<?php endif; ?>
								<?php if(in_array("import_commerciaux", session("auto_action"))): ?>
								<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Taux : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="taux">
										</div>
								</div>
								<?php endif; ?>
							</div>
							<div class="form-group">
								<?php if(in_array("import_commission_hors", session("auto_action"))): ?>
								<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Commission Groupe : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="groupe">
										</div>
								</div>
								<?php endif; ?>
								<div class="col-sm-6">
										<label for="inp-type-1" style="vertical-align:middle; margin-top: 1%;" class="col-sm-12"> Mise à jour : </label>
										<div class="col-sm-12">
											<input type="file" accept=".xlsx" class="form-control" id="inp-type-1"  name="commerciaux">
										</div>
								</div>
							</div>
							
                            <?php if(in_array("import_commission", session("auto_action")) || in_array("import_commission_hors", session("auto_action")) || in_array("import_contrat", session("auto_action"))): ?>
							<div class="form-group" style="display: block;" id="Ajouter">
							    <div class="col-sm-4">
				                    <button type="submit" class="cibutton btn-sm waves-effect waves-light">ENREGISTRER</button>
							    </div>
							    </form>	
							    
							</div>
							<?php endif; ?>
					
        					<!--div class="form-group" style="display: block;" id="Ajouter">
        						<?php if(in_array("calcul_commission_manuelle", session("auto_action"))): ?>
        						   <div class="col-sm-4" id="conmessag">
        				        <a href="#"><button id="cg" class="cibutton btn-sm waves-effect waves-light" >
        				                CALCULER GROUPE
        				            </button>
        				        </a>
        					    </div>
        						<?php endif; ?>
        					</div-->
				</div>
				<!-- /.box-content -->
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
	
	function validii(){
            console.log("Lancer");
            
        }
	   
            
        
	
       const valvicp = document.getElementById("viid"); 
        valvicp.addEventListener("click", function () {
            console.log("Lancer");
            
            
        }, true);

    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstestyle"); ?>
    
    <style>
    
            .cibutton {
            	box-shadow: 0px 0px 0px 2px #9fb4f2;
            	background:linear-gradient(to bottom, #7892c2 5%, #6494ed 100%);
            	background-color:#7892c2;
            	border-radius:10px;
            	border:1px solid #4e6096;
            	display:inline-block;
            	cursor:pointer;
            	color:#ffffff;
            	font-family:Arial;
            	font-size:14px;
            	padding:12px 37px;
            	text-decoration:none;
            	text-shadow:0px 1px 0px #283966;
            }
            .cibutton:hover {
            	background:linear-gradient(to bottom, #6494ed 5%, #7892c2 100%);
            	background-color:#6494ed;
            }
            .cibutton:active {
            	position:relative;
            	top:1px;
            }
            
                  
            .myButton {
            	box-shadow: 0px 7px 14px -7px #d39d0a;
            	background-color:#4e6cad;
            	border-radius:2px;
            	display:inline-block;
            	cursor:pointer;
            	color:#ffffff;
            	font-size:15px;
            	font-weight:bold;
            	padding:12px 41px;
            	text-decoration:none;
            	text-shadow:0px 0px 0px #000203;
            }
            
            .myButton:active {
            	position:relative;
            	top:1px;
            }
            
            .loader{
                content:"";
            	font-size: 0;
            	height: 60px;
            	width: 60px;
            	background: transparent;
            	border: 4px solid #001E5F;
            	border-radius: 50%;
            	border-top-color: transparent;
            	animation: chargem 1s  0.4s linear infinite;
            }
            
            @keyframes  chargem{
            	100%{
            		transform: rotate(360deg);
            	}
            }
            
            .finished{
            	font-size: 0;
            	height: 70px;
            	width: 70px;
            	border: none;
            	background: #fff;
            	position: relative;
            	animation: special 1s ease-in;
            }
            
            @keyframes  special{
            	10%{
            		transform: scale(0.8) translateY(20px);
            	}
            
            	35%{
            		transform: scale(1) translateY(0px);
            	}
            }
            
            .finished:before{
            	content: url('valid.png');
            	position: absolute;
            	top:50%;
            	left: 50%;
            	transform: translate(-50%; -50%);
            }

    </style>
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection("dstejs"); ?>
<script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
<script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
<script type="text/javascript">
    $(".chosen").chosen();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>