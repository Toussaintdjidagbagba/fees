<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo e(Config('app.name')); ?> </title>
    <link rel="stylesheet" href="csslogin/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" charset="utf-8">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/iconnsia.png">
    
  </head>
  <body>
<center style="border-radius: 10px; margin-left: 50%; z-index: 1; left: 50%;top: 75%; transform: translate(-50%, 15%); width: 360px;"> <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></center> 
      <section class="ftco-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="login-wrap py-5">
            <div class="img d-flex align-items-center justify-content-center" style="background-image: url(logo.png);"></div>
            <h3 class="text-center mb-0">Bienvenue</h3>
            <p class="text-center">Connectez-vous en saisissant les informations ci-dessous</p>
            <form action="<?php echo e(url('/login')); ?>" method="post" class="login-form">
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="libelle" value="connexion" />
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-user"></span></div>
                <input type="text" name="login" class="form-control" placeholder="Identifiant" required>
              </div>
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-lock"></span></div>
                <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
              </div>
              
              <div class="form-group">
                  
                  <a  style=" margin-top: 5px; padding-left: 10px; float:right; font-weight: bold;" class="  waves-effect"  href="<?php echo e(route('pas')); ?>">  Modifier mot de passe ? </a>
                    <br> <br>
                    
                <button type="submit" class="btn form-control btn-primary rounded submit px-3">Se connecter</button>
              </div>
              <div class="form-group">
                        <button type="button" id="pt" style="background: #001e60; color: #d29f13 ;  float: right;" 
                        class="btn  form-control btn-primary rounded submit px-1" data-color="deep-orange" data-toggle="modal" data-target="#add">PORTAIL NSIA VIE</button>
                        <br> <br>
                    </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header"style="background:#001e60">
			    
				<h1 class="modal-title" style="text-align:center; color:#d29f13;" > <i style="font-size: large;">PORTAIL NSIA VIE ASSURANCES : </i> </h1>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div> 
            <form method="post" action="<?php echo e(route('logapi')); ?>"> <!-- https://172.17.192.20/apinsia/aidnsiamdpfees.php || https://172.17.192.20/apinsia/aidnsiamdp.php -->
			<div class="modal-body" style="background-image : url('fondprofil.png'); background-repeat: no-repeat; background-size: cover; -webkit-background-size: cover;
            -moz-background-size: cover; -o-background-size: cover;">
			        
					<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
				
                    <div style="margin:30px">
                    <div class="row clearfix">
                        <div class="col-md-12">
                        	<label for="lib"> <i style="font-size: x-large;color:#d29f13">Identifiant : </i></label>
                           <div class="form-group">
                            <div class="form-line">
                                <input type="text" id="lib" name="login" class="form-control" placeholder="">
                            </div>
                           </div>
                        </div>
                        <div class="col-md-12">
                            <label for="temp"><i style="font-size: x-large;color:#d29f13">Mot de passe :</i></label>
                           <div class="form-group">
                            <div class="form-line">
                                <input type="password" id="temp" name="mdp" class="form-control" placeholder="">
                            </div>
                           </div>
                        </div>
                    </div>
                    </div>
			</div>
			<div class="modal-footer" style="background:#d29f13">
				<button type="button" class="btn btn-default btn-sm waves-effect waves-light" style="color:#001e60" data-dismiss="modal">FERMER</button>
				<button type="submit" class="btn waves-effect" style="background:#001e60; color:#d29f13">SE CONNECTER</button>
			</div>
            </form>
		</div>
	</div>
	</div>

    <script>
        //La fonction servant à effecuter le test
        var TestConnection_js = function (){
            var xhr = new XMLHttpRequest();
            xhr.open('HEAD', 'https://172.17.192.20/apinsia/aidnsiamdpfees.php'); //Adresse à modifier (google n'accepte pas cette requête)
            xhr.onreadystatechange = function(){
                console.log(xhr.readyState);        
                if (xhr.readyState == 4 && xhr.status == 200) {
                    //La requête a fonctionnée
                    //alert('Connectio ');
                } else if(xhr.readyState == 4) { //La requête est terminée et une erreur a eu lieu
                    //alert('Connection Error '+xhr.status);
                }
            };
            xhr.send(null);
            return xhr;
        }
        //L'excécution de cette fonction
        TestConnection_js();
    </script>

  <script src="csslogin/js/jquery.min.js"></script>
  <script src="csslogin/js/popper.js"></script>
  <script src="csslogin/js/bootstrap.min.js"></script>
  <script src="csslogin/js/main.js"></script>
  <script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
      </script>
  </body>

</html>
