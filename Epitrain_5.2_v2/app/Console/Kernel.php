<?php

namespace App\Console;

use DB;
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
        Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $file = 'output.log';
         $schedule->call(function () {
            $today = date("Y-m-d");

            $courses = \DB::table('course')
                ->get();

            DB::table('sessions')
                    ->where('user_id', '4')
                    ->update(['loggedIn' => 1]);

            foreach($courses as $course) {
                if ($course->startDate >= $today && $course->isActive == 0) {
                    DB::table('course')
                        ->where('courseID', $course->courseID)
                        ->update(['isActive' => 1]);

                    DB::table('enrolment')
                        ->where('courseID', $course->courseID)
                        ->update(['isActive' => 1]);

                } elseif ($course->endDate > $today && $course->isActive == 1) {
                    DB::table('course')
                        ->where('courseID', $course->courseID)
                        ->update(['isActive' => 0]);

                    DB::table('enrolment')
                        ->where('courseID', $course->courseID)
                        ->update(['isActive' => 0]);
                }
            }

        })->everyMinute()->sendOutputTo('/etc/cron.d/'.$file);
    }
}
