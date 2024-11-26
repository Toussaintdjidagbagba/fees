<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>{{ Config('app.name') }} </title>
    <link rel="stylesheet" href="csslogin/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="csrf-token" content="{{csrf_token()}}" charset="utf-8">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/iconnsia.png">

  </head>
  <body>
      
      <center style="border-radius: 10px; margin-left: 50%; z-index: 1; left: 50%;top: 75%; transform: translate(-50%, 15%); width: 360px;"> @include('flash::message')</center> 


  <section class="ftco-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="login-wrap py-5">
            <div class="img d-flex align-items-center justify-content-center" style="background-image: url(logo.png);"></div>
            <h3 class="text-center mb-0">Modifier mon mot de passe</h3>
            <p class="text-center"></p>
            <form action="{{ url('/login') }}" method="post" class="login-form">
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <input type="hidden" name="libelle" value="modifier" />
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-user"></span></div>
                <input type="text" name="login" class="form-control" placeholder="Identifiant" required>
              </div>
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-lock"></span></div>
                <input type="password" name="ancien_pass" class="form-control" placeholder="Ancien mot de passe" required>
              </div>
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-lock"></span></div>
                <input type="password" name="new_pass" class="form-control" placeholder="Nouveau mot de passe" required>
              </div>
              <div class="form-group">
                <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-lock"></span></div>
                <input type="password" name="confir_pass" class="form-control" placeholder="Confirmer nouveau mot de passe" required>
              </div>
              <div class="form-group d-md-flex">
                <div class="w-100 text-md-right">
                  <a style=" margin-top: 5px; padding-left: 10px; float:right; font-weight: bold;" href="{{ route('log') }}">Se connecter</a>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn form-control btn-primary rounded submit px-3"> Valider</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

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
