<?php setlocale(LC_ALL, 'fr_FR', 'fra_FRA') ?>
Chèr(e) Monsieur / Madame <br>

Cher manager, merci de trouver ci-joint le détail des commissions de votre inspection du mois de <?php echo e(utf8_encode(strtoupper(view()->shared('periodelettre')))); ?> en cliquant sur le lien. <br>

<a href="https://fees.nsiaviebenin.com/doc/<?php echo e($pat); ?>"> Commission <?php echo e(utf8_encode(strtoupper(view()->shared('periodelettre')))); ?> </a> <br> <br>


Cordialement.