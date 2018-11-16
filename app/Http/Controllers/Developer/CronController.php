<?php

namespace App\Http\Controllers\Developer;

use App\Models\Developer\SystemEvent;
use App\Models\Finance\InvoiceImapEmail;
use App\Models\Notification\EmailNotification;
use App\Models\Notification\InvoiceOverdueNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{


    public function checkRequest(){

        $date_cron = Carbon::now();

        if($date_cron->minute == 0){

            // Log::info('cron Hour Invoice Imap:'. Carbon::now()->toDateTimeString());
            //prejdeme emaily kvoli uhradam v banke
            $imap = new InvoiceImapEmail();
            Log:info($imap->readImapMail());

        }

        // cron sa spusta raz za 24 hod
        if($date_cron->minute == 0 && $date_cron->hour == 23  ){

            Log::info('cron Day Overdue Invoice:'. Carbon::now()->toDateTimeString());

            //prejdeme emaily kvoli neuhradenym fakturam
            $overdue = new InvoiceOverdueNotification;
            $overdue->setOverdueInvoicesToNotification();
            Log::info($overdue);

            //prejdeme clenstva
            $system_event_class = new SystemEvent();
            $overdue_membership = $system_event_class->setInactiveExpiredMembership();
            Log::info($overdue_membership);

        }


        $email = new EmailNotification();
        $res_email = $email->procesEmailNotification();

        Log::info($res_email);

    }

}
