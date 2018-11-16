<?php

namespace App\Console\Commands;

use App\Models\Finance\InvoiceImapEmail;
use Illuminate\Console\Command;

class UpdateBmailStatement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bforb:bmail-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment on bank account Tatrabanka';

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
        // prejdeme emaily kvoli uhradam
        $imap = new InvoiceImapEmail();
        $imap->readImapMail();

    }
}
