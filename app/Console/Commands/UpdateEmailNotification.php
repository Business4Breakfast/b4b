<?php

namespace App\Console\Commands;

use App\Models\Notification\EmailNotification;
use Illuminate\Console\Command;

class UpdateEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bforb:email-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Odoslanie notifikacnych emailov';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = new EmailNotification();
        $email->procesEmailNotification();

    }
}
