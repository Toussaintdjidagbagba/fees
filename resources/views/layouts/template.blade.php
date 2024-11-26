<!DOCTYPE html>
<html lang="fr"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, maximum-scale=0.8, initial-scale=0.8, user-scalable=0">
	
	<meta name="description" content="">
	<meta name="author" content="NSIA VIE ASSURANCES"> 

	<title>{{config('app.name')}}</title>

	<!-- Main Styles -->
	<link rel="stylesheet" href="assets/styles/style.min.css">
	<link rel="stylesheet" href="assets/styles/profile.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
	
	<!-- Material Design Icon -->
	<link rel="stylesheet" href="assets/fonts/material-design/css/materialdesignicons.css">

	<!-- mCustomScrollbar -->
	<link rel="stylesheet" href="assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.min.css">

	<!-- Waves Effect -->
	<link rel="stylesheet" href="assets/plugin/waves/waves.min.css">

	<!-- Sweet Alert -->
	<link rel="stylesheet" href="assets/plugin/sweet-alert/sweetalert.css">
	
	<!-- Morris Chart -->
	<link rel="stylesheet" href="assets/plugin/chart/morris/morris.css">

	<!-- FullCalendar -->
	<link rel="stylesheet" href="assets/plugin/fullcalendar/fullcalendar.min.css">
	<link rel="stylesheet" href="assets/plugin/fullcalendar/fullcalendar.print.css" media='print'>
	<!-- Remodal -->
	<link rel="stylesheet" href="assets/plugin/modal/remodal/remodal.css">
	<link rel="stylesheet" href="assets/messtyle.css">
	<link rel="stylesheet" href="assets/plugin/modal/remodal/remodal-default-theme.css">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/images/iconnsia.png">
	<script>
	    body{
	        width:80%;
	    }
	</script>

	@yield('dstestyle')
	 <script src="dste/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="dste/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" type="text/css" href="dste/chosen.css">
    <script type="text/javascript" src="dste/chosen.jquery.min.js"></script>
    @yield('dstjs')

</head>

<body 
oncontextmenu="return true" onkeydown="return true;" onmousedown="return true;" 
style="background-image: url('assets/images/fond.png'); background-repeat: no-repeat; background-size: cover; ">
<div class="main-menu">
	<header class="header">
		<a href="{{ route('home') }}" class="logo"><img src="assets/images/iconnsia.png" alt="user-img" width="25">NSIA VIE ASSURANCES</a>
		<button type="button"  class="button-close fa fa-times js__menu_close"></button>
		<div class="user">
			<a href="#" class="avatar">
				@if(session('utilisateur')->image == "" || session('utilisateur')->image == null)
				<img src="assets/images/defaut.png" alt="">
				@else
				<img src="{{session('utilisateur')->image }}" alt="">
				@endif
				<span class="status online"></span></a>
			<h5 class="name"><a href="#"> {{App\Providers\InterfaceServiceProvider::LibelleUser(session('utilisateur')->idUser)}} </a></h5>
			<h5 class="position">{{App\Providers\InterfaceServiceProvider::LibelleRole(session('utilisateur')->Role)}}</h5>
			<!-- /.name -->
			<div class="control-wrap js__drop_down">
				<i class="fa fa-caret-down js__drop_down_button"></i>
				<div class="control-list">
					<div class="control-item"><a href="{{ route('GPU') }}"><i class="fa fa-user"></i> Profil</a></div>
					<div class="control-item"><a href="{{ route('pas') }}"><i class="fa fa-user"></i> Modifier son mot de passe</a></div>
					<div class="control-item" ><i class="fa fa-sign-out"></i> 
					<button type="button" data-remodal-target="remodal" style="background-color: white" class="btn waves-effect waves-light">Déconnexion</button>
					</div> 
				</div>
				<!-- /.control-list -->
			</div>
			<!-- /.control-wrap -->
		</div>
		<!-- /.user -->
	</header>
	<!-- /.header -->
	<div class="content">

		<div class="navigation">
			<ul class="menu js__accordion" >
				<li class="current" >
					<a class="waves-effect" href="{{ route('home') }}"><i class="menu-icon mdi mdi-view-dashboard"></i><span>Tableau de bord</span></a> 
				</li>

				@if(count(session('auto_menu')) != 0)

				    @for($i=0; $i < count(session('auto_menu')); $i++) 
				        @php( $libelle = App\Providers\InterfaceServiceProvider::infomenu(session('auto_menu')[$i]) )
				        @php( $chv = App\Providers\InterfaceServiceProvider::verifie_ss(session('auto_menu')[$i]) )
				    	<li>
				    		@if($libelle->route != "#")
							<a class="waves-effect " href="{{route($libelle->route)}}"><i class="{{$libelle->iconee}}"></i><span>{{$libelle->libelleMenu}}</span>@if(count(session('auto_ss_menu')) != 0 && $chv) <span class="menu-arrow fa fa-angle-down"></span> @endif </a>
							@else
                            <a class="waves-effect parent-item js__control" href="{{$libelle->route}}"><i class="{{$libelle->iconee}}"></i><span>{{$libelle->libelleMenu}}</span>@if(count(session('auto_ss_menu')) != 0 && $chv) <span class="menu-arrow fa fa-angle-down"></span> @endif </a>
							@endif

							@if(count(session('auto_ss_menu')) != 0 && $chv)
								<ul class="sub-menu js__content">
									@php( $all_ss = App\Providers\InterfaceServiceProvider::sous_menu(session('auto_ss_menu'), session('auto_menu')[$i]) )
									@for($a=0; $a < count($all_ss); $a++) 
									   @php( $lib = App\Providers\InterfaceServiceProvider::infomenu($all_ss[$a]) )
									   
									   <li><a class="waves-effect" href="{{route($lib->route)}}"><span>{{ $lib->libelleMenu }}</span></a></li>
									@endfor
								</ul>
							@endif
						</li>
				    @endfor

				@endif
			</ul>
		</div>
		<!-- /.navigation -->
	</div>
	<!-- /.content -->
</div>
<!-- /.main-menu --> 

<div class="fixed-navbar" style="background-image: url('assets/images/header.png'); background-repeat: no-repeat; background-size: cover; ">
	<div class="pull-left" >
		<button type="button" style="background-color: #D19C0A;" class="menu-mobile-button glyphicon glyphicon-menu-hamburger js__menu_mobile"></button>
		<h1 class="page-title">{{config('app.name')}} </h1>
		<!-- /.page-title -->
	</div>
	<!-- /.pull-left -->
	<div class="pull-right">
		<a data-remodal-target="remodal" class="ico-item mdi mdi-logout "></a>
	</div>
	<!-- /.pull-right -->
</div>
<!-- /.fixed-navbar -->


<div id="wrapper">
	<div class="main-content" > 
		
		@yield('content')		
		
		<!-- /.row -->		
		<footer class="footer">
			<ul class="list-inline">
				<li>{{ date('Y')}} &copy; NSIA Vie Bénin - {{config('app.name')}}</li>
			</ul>
		</footer>
	</div>
	<!-- /.main-content -->
</div><!--/#wrapper -->

@yield("model")
<div class="remodal" data-remodal-id="remodal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
	<button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	<div class="remodal-content">
		<h2 id="modal1Title">Déconnexion</h2>
		<p id="modal1Desc">
		Êtes-vous sûre de vouloir vous déconnecter ?
		</p>
	</div>
	
	<form action="{{ route('offU') }}">
	    <button data-remodal-action="cancel" class="remodal-cancel">NON</button>
	    <button  class="remodal-confirm" style="width:50px; color: white" type="submit">OUI</button>
	</form>
</div>
		

	@yield('js')
	<script>
          $('#flash-overlay-modal').modal();
          $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
    </script>
      
	<script src="assets/scripts/modernizr.min.js"></script>
	<script src="assets/plugin/bootstrap/js/bootstrap.min.js"></script>
	
	
	
	<script src="assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="assets/plugin/nprogress/nprogress.js"></script>
	<script src="assets/plugin/sweet-alert/sweetalert.min.js"></script>
	<script src="assets/plugin/waves/waves.min.js"></script>
	
	<script src="assets/plugin/modal/remodal/remodal.min.js"></script>

	<script src="assets/scripts/main.min.js"></script>
	@yield('partjs')
</body>
@yield('dstejs')
</html>