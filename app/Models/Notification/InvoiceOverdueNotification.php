<?php

namespace App\Models\Notification;

use App\Models\Company;
use App\Models\Finance\Invoice;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceOverdueNotification extends Model
{

    const FIRST_REMINDER_DAYS = 5;
    const SECOND_REMINDER_DAYS = 10;
    const THIRD_REMINDER_DAYS = 15;

    public function setOverdueInvoicesToNotification()
    {

        $inv = [];
        for ($i = 1; $i <= 3; $i++) {
            if($i==1){

                $inv = $this->getOverdueInvoices(self::FIRST_REMINDER_DAYS);
                $this->setOverdueInfoToDB($inv, self::FIRST_REMINDER_DAYS, $i );

            }elseif ($i==2){

                $inv = $this->getOverdueInvoices(self::SECOND_REMINDER_DAYS);
                $this->setOverdueInfoToDB($inv, self::SECOND_REMINDER_DAYS, $i );

            }elseif ($i==3){

                $inv = $this->getOverdueInvoices(self::THIRD_REMINDER_DAYS);
                $this->setOverdueInfoToDB($inv, self::THIRD_REMINDER_DAYS, $i );

            }

        }

    }


    public function setOverdueInfoToDB($invoice, $reminder, $count)
    {

        if($invoice){
            foreach ($invoice as $inv){

                $exist_record = DB::table('invoice_reminders')
                        ->where('invoice_id', '=', $inv->id)
                        ->where('reminder_number', '=', $reminder)
                        ->exists();

                //ak uz bola poslana upomienka 1,2,3 k fakture nezapise sa do notification
                if($exist_record == false) {

                    $data['invoice_id'] = $inv->id;
                    $data['reminder_number'] = intval($reminder);
                    $data['date_pay_to'] = $inv->date_pay_to;
                    $data['date_create'] = Carbon::now()->format('Y-m-d H:i:s');

                    DB::table('invoice_reminders')->insert($data);

                    // najdeme emailove adrecy ko;mu posleme upomienku
                    $recipients = $this->getUsersFromInvoices($inv->id);

                    //ak su adresy vytvorime zaznamy v notification
                    if ($recipients){

                        $this->addNotificationReminderInvoiceToCue( $inv, 'reminder-invoice', $recipients, $reminder, $count,  'default');

                    }

                }

            }
        }
    }


    // ulozenie emailov do cue, odoslabie
    // pri odoslani faktury sa email vysklada az pri odoslani
    public function addNotificationReminderInvoiceToCue( $invoice, $module, $recipients, $reminder, $count, $account='default'){

        $recipients = json_encode(is_array($recipients) ? $recipients : array($recipients));

        //ak je email odoslanie proforma faktury
        if(strcmp($module, 'reminder-invoice') == 0 ){

            $date_to_send  = Carbon::createFromFormat('Y-m-d H:i',
                Carbon::createFromFormat('Y-m-d H:i:s' , now()->addDay())->format('Y-m-d') . '08:30')
                ->format('Y-m-d H:i:s');

            $em = new EmailNotification();

            $em->subject = 'Upomienka č: ' . $count;
            $em->html = null;
            $em->text = null;
            $em->account = $account;
            $em->module = $module;
            $em->date_send = $date_to_send;
            $em->recipients = $recipients;
            $em->data = json_encode(['invoice' => $invoice, 'reminder' => $reminder, 'count' => $count ]);
            $em->files = null;
            $em->module_id = intval($invoice->id);
            $em->save();

        }

    }




    public function getUsersFromInvoices($invoice_id)
    {
        $invoice_class = new Invoice();
        $inv = $invoice_class->find($invoice_id);

        $users =  $invoice_class->getUsersFromMembersipInvoice( $invoice_id, 1);
        $company = Company::find($inv->company_id);

        $recipients = null;

        if ($users){
            foreach ($users as $u){
                $recipients[] = $u->email;
            }
        }

        if($company->email) $recipients[] = $company->email;

        //odstranime duplicity
        $recipients = array_unique($recipients);

        return $recipients;
    }



    // neuhradene fakury po splatnosti podla dni
    private function getOverdueInvoices($days){

        $query = Invoice::query();

        $query = $query->where('status', '!=', 5);
        $query = $query->where('date_pay_to', '<' , Carbon::now()->subDays($days));
        $items = $query->get();

        return $items;

    }





}
