<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Extract::class,
        Commands\EnvoiFichePaieMail::class,
        Commands\CalculCommission::class,
        Commands\ValiderCommission::class,
        Commands\AffectationComTwoManag::class,
        Commands\AttributionManag::class,
        Commands\EnvoieFicheCommission::class,
		Commands\CommissionAuto::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Commission:fichepaie')
                  ->timezone('Africa/Porto-Novo')
                  ->everyFiveMinutes();
        
        $schedule->command('Commission:calculer')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
                  //->everyThirtyMinutes();
        
        $schedule->command('Commission:validercalculer')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
        
        $schedule->command('Commission:attribuer')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
                  
        $schedule->command('Commission:affectation')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
        
        $schedule->command('Commission:generefiche')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
                  //->everyThirtyMinutes();
                  
                  //->between('07:00', '23:59');
                  //->emailOutputTo('rogerkpovi@gmail.com');
                  
        $schedule->command('Commission:extra')
                  ->timezone('Africa/Porto-Novo')
                  ->everyMinute();
		
		$schedule->command('Commission:auto')
                  ->timezone('Africa/Porto-Novo')
                  ->dailyAt('13:00');
                  
        // /usr/local/bin/ea-php73 /var/www/vhosts/nsiaviebenin.com/public_html/NSIAFEES/artisan schedule:run
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
