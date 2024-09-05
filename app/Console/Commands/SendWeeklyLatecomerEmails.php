<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyLatecomerEmails extends Command
{
    protected $signature = 'email:weekly-latecomers';

    protected $description = 'Send weekly email report of latecomers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \Log::info('WeeklyLatecomerEmails command triggered at: ' . now());
        // Get the previous week's dates and latecomers data as before
        $startDate = Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d');

        $latecomers = DB::table('attendances')
            ->join('users', 'attendances.employee_id', '=', 'users.emp_no')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereTime('real_check_in', '>', '08:45:00')
            ->select('users.name', 'attendances.date', 'attendances.real_check_in')
            ->orderBy('attendances.date')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('l, d F Y');
            })
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'real_check_in' => $item->real_check_in
                    ];
                });
            });

        if ($latecomers->isEmpty()) {
            $this->info('No latecomers for the previous week.');
            return;
        }

        // Email recipients
        $recipients = [
            'tharusha.singh@arrogance.lk',
            'thileeka.sewmini@arrogance.lk',
            'gayani.medawatta@arrogance.lk',
        ];

        // Send email using the Blade template
        Mail::send('emails.weekly_report', ['latecomers' => $latecomers], function ($message) use ($recipients) {
            $message->from('info@myarrogance.lk', 'HRM System - Arrogance Technologies (Pvt) Ltd');
            $message->to($recipients);
            $message->subject('Weekly Latecomers Report');
        });

        $this->info('Weekly latecomers email sent successfully.');

        \Log::info('WeeklyLatecomerEmails command completed at: ' . now());
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:weekly-latecomers')->everyMinute();
    }
    
    
}