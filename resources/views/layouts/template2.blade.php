<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="">
    <meta name="description"
        content="">
    <meta name="robots" content="noindex,nofollow">
    <title>Exemple2</title>

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="csstemplate/images/iconnsia.png">
    <!-- Bootstrap Core CSS -->
    <link href="csstemplate/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="csstemplate/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="csstemplate/bower_components/morrisjs/morris.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="csstemplate/css/style.css" rel="stylesheet">
    
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" style="margin-bottom: 0">
            <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg "
                    href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i
                        class="ti-menu"></i></a>
                <div class="top-left-part"><a class="logo" href="#"><img src="csstemplate/images/iconnsia.png" alt="user-img" width="25">&nbsp;<span class="hidden-xs" style="font-size: small;">NSIA VIE ASSURANCE</span></a></div>
                <ul class="nav navbar-top-links navbar-left hidden-xs">
                    <li><a href="javascript:void(0)" class="open-close hidden-xs hidden-lg waves-effect waves-light"><i class="ti-arrow-circle-left ti-menu"></i>
                        </a></li>
                </ul>
                <ul class="nav navbar-top-links navbar-right pull-right">
                    
                    <li>
                        <a class="profile-pic" href="#"> <img src="csstemplate/images/defaut.jpg" alt="user-img" width="36"
                                class="img-circle"><b class="hidden-xs">Admin</b> </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="navbar-default sidebar nicescroll" role="navigation">
            <div class="sidebar-nav navbar-collapse ">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search hidden-sm hidden-md hidden-lg">
                        
                    </li>
                    <li>
                        <a href="#" style="background-color:#001e60" class="waves-effect"><i class="glyphicon glyphicon-th-large"></i>
                            Tableau de bord</a>
                    </li>
                    <li>
                        <a href="/admin" class="waves-effect"><i class="ti-layout fa-fw"></i>Prépare commission</a>
                    </li>
                    
                </ul>
                <div class="center p-20">
                    <span class="hide-menu"><a href="#" target="_blank"
                            class="btn btn-info btn-block btn-rounded waves-effect waves-light">Paramètre</a></span>
                </div>
            </div>
            <!-- /.sidebar-collapse -->
        </div>

        <!-- Page Content -->
        <div id="page-wrapper"> <!-- @yield("content") -->
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-12">
                        <h4 class="page-title">Bienvenue</h4>
                        <ol class="breadcrumb">
                            <li><a href="#">Tableau de bord</a></li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="white-box">
                            <div class="row row-in">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->


        <footer class="footer text-center"> {{ date('Y')}} &copy; Nsia Vie Bénin - Commission </footer>
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="csstemplate/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="csstemplate/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="csstemplate/bower_components/metisMenu/dist/metisMenu.min.js"></script>
    <!--Nice scroll JavaScript -->
    <script src="csstemplate/js/jquery.nicescroll.js"></script>
    <!--Morris JavaScript -->
    <script src="csstemplate/bower_components/raphael/raphael-min.js"></script>
    <script src="csstemplate/bower_components/morrisjs/morris.js"></script>
    <!--Wave Effects -->
    <script src="csstemplate/js/waves.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="csstemplate/js/myadmin.js"></script>
    <script src="csstemplate/js/dashboard1.js"></script>
</body>

</html>