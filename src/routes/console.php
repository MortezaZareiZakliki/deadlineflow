<?php

use Illuminate\Support\Facades\Schedule;

//Schedule::command('tasks:send-reminders')->dailyAt('09:00');

Schedule::command('tasks:send-reminders --days=1')->dailyAt('00:00');

//Schedule::command('app:clean-old-data')->everyMinute();