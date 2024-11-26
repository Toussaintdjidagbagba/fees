

<?php $__env->startSection('content'); ?>
		<div class="row small-spacing">
			<div class="col-lg-4 col-md-6 col-xs-12">
				<div class="box-content text-white" style="background: #D19C0A ">
					<div class="statistics-box with-icon">
						<i class="ico small fa fa-money" style="margin-top:-27px; margin-left: -20px;"></i>
						<p class="text text-white" style="margin-top:-20px; ">CUMUL DES COMMISSIONS</p>
						<h2 class="counter" style="margin-left:-80px; margin-top:20px; font-size:20px"><?php echo e(number_format($allCommission, 0, '.', ' ')); ?> CFA</h2>
					</div>
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-3 col-md-6 col-xs-12 -->
			<div class="col-lg-4 col-md-6 col-xs-12">
				<div class="box-content text-white" style="background: #f2b200; ">
					<div class="statistics-box with-icon">
						<i class="ico small fa fa-money"style="margin-top:-27px; margin-left: -20px;"></i>
						<p class="text text-white" style="margin-top:-20px; ">DERNIERE COMMISSION</p>
						<h2 class="counter" style="margin-left:-80px; margin-top:20px; font-size:20px"><?php echo e(number_format($derniere, 0, '.', ' ')); ?> CFA</h2>
					</div>
				</div>
				<!-- /.box-content -->
			</div>
			<!-- /.col-lg-3 col-md-6 col-xs-12 -->
			<div class="col-lg-4 col-md-6 col-xs-12">
				<div class="box-content text-white" style="background:  #D19C0A ">
					<div class="statistics-box with-icon">
						<i class="ico small fa fa-user" style="margin-top:-27px; margin-left: -20px;"></i>
						<p class="text text-white" style="margin-top:-20px; ">NOMBRE APPORTEUR</p>
						<h2 class="counter" style="margin-left:-80px; margin-top:20px; font-size:20px"><?php echo e($allCom); ?></h2>
					</div>
				</div>
				<!-- /.box-content -->
			</div>
		</div>
		<!-- .row -->
		
		<div class="row small-spacing">
		    <div class="col-lg-12 col-md-12 col-xs-12">
		        <h5>Etat d'avancement des commissions Individuelle </h5>
		     <section class="step-wizard">
                <ul class="step-wizard-list">
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(-1) == 1 && App\Providers\InterfaceServiceProvider::niveauVal(-1) != 3): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <?php if(date('d-m') > date('01-m') && date('d-m') <= date('26-m')): ?>
                            <?php if(App\Providers\InterfaceServiceProvider::niveauVal(-1) != 1 && 
                                App\Providers\InterfaceServiceProvider::niveauVal(0) != 1 &&
                                App\Providers\InterfaceServiceProvider::niveauVal(1) != 1 &&
                                App\Providers\InterfaceServiceProvider::niveauVal(2) != 1 &&
                                App\Providers\InterfaceServiceProvider::niveauVal(3) != 1 &&
                                App\Providers\InterfaceServiceProvider::niveauVal(4) != 1 &&
                                App\Providers\InterfaceServiceProvider::niveauVal(5) != 1): ?>
                            <li class="step-wizard-item current-item" > 
                            <?php else: ?>
                            <li class="step-wizard-item " > 
                            <?php endif; ?>
                                <span class="progress-count">i</span>
                                <span class="progress-label">Début</span>
                            </li>
                        <?php endif; ?>
                        <li class="step-wizard-item">
                    <?php endif; ?>
                        <span class="progress-count">0</span>
                        <span class="progress-label">Système</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(0) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item">
                    <?php endif; ?>
                        <span class="progress-count">1</span>
                        <span class="progress-label">SP</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(1) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">2</span>
                        <span class="progress-label">CSP</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(2) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">3</span>
                        <span class="progress-label">DT</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(3) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">4</span>
                        <span class="progress-label">DG</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(4) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">5</span>
                        <span class="progress-label">CDAF</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(5) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        
                        <li class="step-wizard-item">
                    <?php endif; ?>
                        <span class="progress-count">6</span>
                        <span class="progress-label">Trésorerie</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauVal(6) == 1): ?>
                        <li class="step-wizard-item ">
                    <?php else: ?>
                        
                        <li class="step-wizard-item current-item">
                    <?php endif; ?>
                        <span class="progress-count">7</span>
                        <span class="progress-label">Fin</span>
                    </li>
                </ul>
            </section> 
            </div>
		</div>
		
		<br><br>
		
		<div class="row small-spacing">
		    <div class="col-lg-12 col-md-12 col-xs-12">
		        <h5>Etat d'avancement des commissions Groupe</h5>
		     <section class="step-wizard">
                <ul class="step-wizard-list">
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(-1) == 1 && App\Providers\InterfaceServiceProvider::niveauValG(-1) != 3): ?>
                        <li class="step-wizard-item current-item"> 
                    <?php else: ?>
                        
                        <?php if(App\Providers\InterfaceServiceProvider::niveauValG(-1) != 1 && 
                            App\Providers\InterfaceServiceProvider::niveauValG(0) != 1 &&
                            App\Providers\InterfaceServiceProvider::niveauValG(1) != 1 &&
                            App\Providers\InterfaceServiceProvider::niveauValG(2) != 1 &&
                            App\Providers\InterfaceServiceProvider::niveauValG(3) != 1 &&
                            App\Providers\InterfaceServiceProvider::niveauValG(4) != 1 &&
                            App\Providers\InterfaceServiceProvider::niveauValG(5) != 1): ?>
                        <li class="step-wizard-item current-item" > 
                        <?php else: ?>
                        <li class="step-wizard-item " > 
                        <?php endif; ?>
                            <span class="progress-count">i</span>
                            <span class="progress-label">Début</span>
                        </li>
                        
                        <li class="step-wizard-item" > 
                    <?php endif; ?>
                        <span class="progress-count">0</span>
                        <span class="progress-label">Système</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(0) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item">
                    <?php endif; ?>
                        <span class="progress-count">1</span>
                        <span class="progress-label">SP</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(1) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">2</span>
                        <span class="progress-label">CSP</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(2) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">3</span>
                        <span class="progress-label">DT</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(3) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">4</span>
                        <span class="progress-label">DG</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(4) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        <li class="step-wizard-item ">
                    <?php endif; ?>
                        <span class="progress-count">5</span>
                        <span class="progress-label">CDAF</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(5) == 1): ?>
                        <li class="step-wizard-item current-item">
                    <?php else: ?>
                        
                        <li class="step-wizard-item">
                    <?php endif; ?>
                        <span class="progress-count">6</span>
                        <span class="progress-label">Trésorerie</span>
                    </li>
                    <?php if(App\Providers\InterfaceServiceProvider::niveauValG(6) == 1): ?>
                        <li class="step-wizard-item ">
                    <?php else: ?>
                        
                        <li class="step-wizard-item current-item">
                    <?php endif; ?>
                        <span class="progress-count">7</span>
                        <span class="progress-label">Fin</span>
                    </li>
                </ul>
            </section> 
            </div>
		</div>
		<!-- .row -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('dstestyle'); ?>

    <style>
        .step-wizard {
            background-color: #6495ed;
            background-image: linear-gradient(19deg, #6495ed 0%, #d19c0a 100%);
            height: 20vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .step-wizard-list{
            background: #fff;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            color: #333;
            list-style-type: none;
            border-radius: 10px;
            display: flex;
            padding: 20px 10px;
            position: relative;
            z-index: 10;
        }
        
        .step-wizard-item{
            padding: 0 20px;
            flex-basis: 0;
            -webkit-box-flex: 1;
            -ms-flex-positive:1;
            flex-grow: 1;
            max-width: 80%;
            display: flex;
            flex-direction: column;
            text-align: center;
            min-width: 70px;
            position: relative;
        }
        
        @media  screen and (max-width: 1200px) {
            .step-wizard-item{
                padding: 0 20px;
                flex-basis: 0;
                -webkit-box-flex: 1;
                -ms-flex-positive:1;
                flex-grow: 1;
                max-width: 10px;
                display: flex;
                flex-direction: column;
                text-align: center;
                min-width: 20px;
                position: relative;
            }
        }
        
        @media  screen and (max-width: 875px) {
            .step-wizard-item{
                padding: 0 20px;
                flex-basis: 0;
                -webkit-box-flex: 1;
                -ms-flex-positive:1;
                flex-grow: 1;
                max-width: 80%;
                display: flex;
                flex-direction: column;
                text-align: center;
                min-width: 10px;
                position: relative;
            }
        }
        
        @media  screen and (max-width: 576px){
            .step-wizard-item{
                padding: 0 10px;
                flex-basis: 0;
                -webkit-box-flex: 1;
                -ms-flex-positive:1;
                flex-grow: 1;
                max-width: 80%;
                display: flex;
                flex-direction: column;
                text-align: center;
                min-width: 5px;
                position: relative;
            }
        }
        .step-wizard-item + .step-wizard-item:after{
            content: "";
            position: absolute;
            left: 0;
            top: 19px;
            background: #6495ed;
            width: 100%;
            height: 2px;
            transform: translateX(-50%);
            z-index: -10;
        }
        .progress-count{
            height: 40px;
            width:40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
            margin: 0 auto;
            position: relative;
            z-index:10;
            color: transparent;
        }
        .progress-count:after{
            content: "";
            height: 40px;
            width: 40px;
            background: #6495ed;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            z-index: -10;
        }
        .progress-count:before{
            content: "";
            height: 10px;
            width: 20px;
            border-left: 3px solid #fff;
            border-bottom: 3px solid #fff;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -60%) rotate(-45deg);
            transform-origin: center center;
        }
        .progress-label{
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }
        .current-item .progress-count:before,
        .current-item ~ .step-wizard-item .progress-count:before{
            display: none;
        }
        .current-item ~ .step-wizard-item .progress-count:after{
            height:10px;
            width:10px;
        }
        .current-item ~ .step-wizard-item .progress-label{
            opacity: 0.5;
        }
        .current-item .progress-count:after{
            background: #fff;
            border: 2px solid #6495ed;
        }
        .current-item .progress-count{
            color: #6495ed;
        }
    </style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>