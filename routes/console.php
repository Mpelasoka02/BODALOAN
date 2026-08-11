<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('loans:check-overdue')->daily();
Schedule::command('loans:send-reminders')->daily();
