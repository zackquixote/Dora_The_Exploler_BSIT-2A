<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\BlotterHearingModel;

class MarkPastHearingsNotified extends BaseCommand
{
    protected $group       = 'Notifications';
    protected $name        = 'hearings:mark-notified';
    protected $description = 'Mark hearings with date < today as notified to prevent reminders.';

    public function run(array $params)
    {
        $model = new BlotterHearingModel();
        $today = date('Y-m-d');
        $updated = $model->where('hearing_date <', $today)
                         ->where('notification_sent', 0)
                         ->set(['notification_sent' => 1])
                         ->update();
        CLI::write("Marked {$updated} past hearings as notified.", 'green');
    }
}