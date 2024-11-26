<?php setlocale(LC_ALL, 'fr_FR', 'fra_FRA') ?>
Chèr(e) Monsieur / Madame <br>

Je vous prie de trouver ci-dessous votre fiche de paie pour le compte du mois de <?php echo e(strtoupper(strftime('%B %Y'))); ?> en cliquant sur les liens. <br>

<a href="https://fees.nsiaviebenin.com/<?php echo e($fiche); ?>"> FICHE DE PAIE <?php echo e(strtoupper(strftime('%B %Y'))); ?> </a> <br> <br>

<a href="https://fees.nsiaviebenin.com/<?php echo e($detail); ?>"> DETAIL <?php echo e(strtoupper(strftime('%B %Y'))); ?> </a> <br> <br>

Cordialement.