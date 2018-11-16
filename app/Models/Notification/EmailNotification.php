<?php

namespace App\Models\Notification;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoicePdf;
use App\Models\Membership;
use File;
use Hashids\Hashids;
use http\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Mail\Markdown;
use Illuminate\Mail\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;
use function Sodium\crypto_box_publickey_from_secretkey;

class EmailNotification extends Model
{

    const EMAIL_SENT = 1;

    private $emails;
    private $invoices;
    private $content;
    private $file_send;

    protected $fillable = [
        'subject',
        'html',
        'text',
        'date_to_send', //datetime
        'module',
        'module_id',
        'files', //JSON
        'data', //json
        'status', //
        'error_log',
        'account',
    ];

    protected $table = 'email_notifications';


    public function __construct()
    {

        // pozvanka je na oodoslanie nie preview
        $this->content['preview'] = false;

    }

    // overi zaznamy v databaze a posle ak uplynie termin odoslania
    public function procesEmailNotification()
    {

        try {


            // najdeme neodoslane zaznamy ktore su v minulosti
            $emails_to_send = EmailNotification::where('status', 0)
                ->where('date_send', '<=', now()) // termin odoslania
                ->get();

            if($emails_to_send){
                foreach($emails_to_send as $em){

                    // ak je proforma faktura
                    if(strcmp($em->module, 'proforma') == 0 ){

                        $this->sendProformaInvoice($em);

                    // send final faktura
                    }elseif(strcmp($em->module, 'final') == 0 ){

                        $this->sendFinalInvoice($em);

                    // send serial pre novych
                    }elseif(strcmp($em->module, 'serial') == 0 ){

                        $this->sendSerialContent($em);

                    }elseif(strcmp($em->module, 'reminder-invoice') == 0 ){

                        $this->sendReminderInvoice($em);

                    }elseif(strcmp($em->module, 'invitation') == 0 ){

                        $this->sendInvitation($em);

                    }elseif(strcmp($em->module, 'thank-you') == 0 ){

                        $this->sendThankYou($em);

                    }elseif(strcmp($em->module, 'transaction') == 0 ){

                        $this->sendTransactionMessage($em);
                    }

                }
            }


        }
        catch ( Exception $e) {
            return $e->getMessage();
        }
    }



    private function sendInvitation($email)
    {
        try {

            $hashids = new Hashids(  env('HASH_SALT'), 10);
            $event_class = new Event();
            $club_class = new Club();

            $data_raw = json_decode($email->data);
            $recipients = json_decode($email->recipients, true);
            $template = $data_raw->template;
            $club = $data_raw->club;

            //typy pozvanok
            // 1. pozvanka pre hosta
            // 2. pozvanka pre clena

            $this->content = null;
            $this->emails = $recipients;

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            // defaul signature TODO doeobit ak budu franchisanti alebo majitelia klubu
            $this->content['signature'] = User::find(23);

            //ak je pozvanka pre clenov
            if ($data_raw->invitation_type == 2)  {

                $template = 'invitation_member';
                $club_manager = $club_class->getExecutiveManagerFromClub($club);

                if ($club_manager > 0){
                    $this->content['signature'] = User::find($club_manager);
                }
            }

            //ak je pozvanka pre hosti
            if ($data_raw->invitation_type == 1)  {

                $club_manager = $club_class->getExecutiveManagerFromClub($club);

                if ($club_manager > 0){
                    $this->content['signature'] = User::find($club_manager);
                }
            }

            // info o evente
            $event_detail = Event::find($data_raw->event->id);

            // ak su subory k eventu
            $event_files = DB::table('files')
                ->where('module', 'event-attach-files')
                ->where('module_id', $event_detail->id)->get();

            //ak su nejake subory
            if($event_files){

                $this->file_send = $event_files;
            }


            $this->content['event'] = $event_detail;

            //ak je udalost mixer
            if ($this->content['event']->event_type  == 2)  {
                $template = 'invitation_mixer';
                $club_manager = $club_class->getExecutiveManagerFromClub($club);

                if ($club_manager > 0){
                    $this->content['signature'] = User::find($club_manager);
                }
            }

            $this->content['activity'] = null;
            // aktivyty eventu vzdelavaci bod alebo zb
            $activity_list = $event_class->getEventActivity($data_raw->event->id);

            if (count($activity_list) > 0 && count($activity_list) > 0 ){
                $this->content['activity'] = $activity_list;
            } else {
                $this->content['activity'] = null;
            }
//
            //extern y event
            if ($this->content['event']->event_type  == 9)  {
                $template = 'invitation_external_event';
                $this->content['signature'] = null;

            }


            $this->content['club'] = Club::find($data_raw->club);
            $this->content['recipient'] = User::find($data_raw->user_to->id);
            $this->content['invite_person'] = User::find($data_raw->user_from->id);
            $this->content['attendance'] = DB::table('attendance_lists')->find($data_raw->attendance);

            $this->content['template'] = $template;
            $this->content['subject'] = "Pozvánka na " . strtolower($this->content['event']->title);
            $this->content['text_header'] = $email->subject;

            $this->content['link_accept'] = route('invite.guest.response', [ $hashids->encode( $this->content['attendance']->id, $this->content['recipient']->id, 2) ] );
            $this->content['link_apology'] = route('invite.guest.response', [ $hashids->encode( $this->content['attendance']->id, $this->content['recipient']->id, 3) ] );
            $this->content['link_refused'] = route('invite.guest.response', [ $hashids->encode( $this->content['attendance']->id, $this->content['recipient']->id, 9) ] );
            $this->content['link_invite_guest'] = route('ext.invite.guest.step1', [  $this->content['recipient']->token_id  ] );

            $this->content['invitation_type']  = $data_raw->invitation_type;

            $map_url = "https://maps.googleapis.com/maps/api/staticmap?";
            $map_url .= "center=" . $this->content['event']->lat . "," . $this->content['event']->lng;
            $map_url .= "&zoom=17&scale=1";
            $map_url .= "&markers=color:red%7Clabel:S%7C"  . $this->content['event']->lat . "," . $this->content['event']->lng;
            $map_url .= "&size=600x300";
            $map_url .= "&maptype=roadmap";
            $map_url .= "&format=png";
            $map_url .= "&visual_refresh=true";
//            $map_url .= "&key=". env('GOOGLE_MAP_API');


            // zistime ci sa da kupit vstupenka online
            $ticket_setting = DB::table('events_ticket_setup')
                ->where('event_id', $event_detail->id)->get();

            // hash zakodovane id a id prijemca a kto pozval
            $hash_ticket = $hashids->encode(
                $event_detail->id,  // id udalosti
                $this->content['recipient']->id,  // id usera komu je pozvanka
                $this->content['invite_person']->id );

            $this->content['link_info_detail'] = route('ext.event.info', [ $hash_ticket ] );

            // ak existuje nastavenie pre kupu listkov
            if ($ticket_setting->count() > 0){
                $this->content['link_buy_ticket'] = route('ext.ticket.event', [ $hash_ticket ] );
            }

            $this->content['map']  = $map_url;

            if(strlen($this->content['event']->image) > 0){
                $this->content['image_event'] = asset('images/event'). '/' . $this->content['event']->id .'/'. $this->content['event']->image;
            } else {

                $this->content['image_event'] = asset('images/event-type/1/'). '/large/ranny-klub-1-476799231.png';
            }

            if(strlen($this->content['club']->image) > 0){
                $this->content['image_club'] = asset('images/club'). '/' . $this->content['club']->id .'/'. $this->content['club']->image;
            } else {
                $this->content['image_club'] = asset('images/event-type/1/'). '/large/ranny-klub-1-476799231.png';
            }


            try {

                Mail::send( ['html' =>'email.html.'. $this->content['template'], 'text' =>  'email.text.' . $this->content['template'] ], ['content' =>  $this->content], function (Message $message) {

                    $message->to($this->emails)
                        ->subject($this->content['subject']);

                    // odosleme subory
                    if(count($this->file_send) > 0){
                        foreach( $this->file_send as $f ){

                            $file_path = public_path($f->path . $f->file);

                            //odosleme prirucku
                            if(File::exists($file_path)){
                                $message->attach($file_path, [ 'mime' => $f->mime ]);
                            }
                        }
                    }

                });

                // ulozime do db ze bol email odoslany
                $email->status = self::EMAIL_SENT;
                $email->updated_at = Carbon::now();

                $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    Log::alert(Mail::failures());
                }
            }
        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }




    private function sendReminderInvoice($email)
    {
        try {

            $data_raw = json_decode($email->data);

            $recipients = json_decode($email->recipients);
            $invoices = $data_raw->invoice;

            // pridame kopiu pre juraja
            if(!is_array($recipients))  $recipients = [$recipients];
            array_push($recipients, "marian@patak.sk", "juraj.bais@bforb.sk");

            $this->content = null;
            $this->emails = $recipients;
            $this->invoices = $invoices;
            $this->content['text_header'] = $email->subject;
            $this->content['subject'] = $email->subject;
            $this->content['count'] = $data_raw->count;


            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            // TODO CACHE
            //$this->content['signature'] = User::find(config('invoice.signature.proforma'));
            $this->content['signature'] = User::find(23);

            try {

                Mail::send(['html' =>'email.html.reminder_invoice', 'text' =>  'email.text.reminder_invoice'], ['content' =>  $this->content], function (Message $message) {

                    $inv_pdf = new InvoicePdf();
                    $message->to($this->emails)
                        ->subject($this->content['subject']);

                        //vygenerujeme fakturu
                        $inv_string = $inv_pdf->generatePdfInvoice($this->invoices->id, 'S');
                        //pripravime ako prilohu
                        $message->attachData( $inv_string, $this->invoices->variable_symbol . '.pdf');

                });

                // ulozime do db ze bol email odoslany
                $email->status = self::EMAIL_SENT;
                $email->updated_at = Carbon::now();

                $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    //dd(Mail::failures());
                }
            }

        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }



    private function sendSerialContent($email)
    {
        try {

            $data_raw = json_decode($email->data);
            $recipients = json_decode($email->recipients, true);
            $template = $data_raw->template;
            $member = $data_raw->member;

            // pridame kopiu pre juraja
            if(!is_array($recipients))  $recipients = [$recipients];
            array_push($recipients, "marian@patak.sk", "juraj.bais@bforb.sk");

            $this->content = null;
            $this->emails = $recipients;
            $this->content['template'] = $template;
            $this->content['subject'] = $email->subject;
            $this->content['member'] = $member;
            $this->content['text_header'] = $email->subject;

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            // TODO CACHE
            //$this->content['signature'] = User::find(config('invoice.signature.proforma'));
            $this->content['signature'] = User::find(23);

            try {

                Mail::send( ['html' =>'email.html.'. $this->content['template'], 'text' =>  'email.text.' . $this->content['template'] ], ['content' =>  $this->content], function (Message $message) {

                    $message->to($this->emails)
                        ->subject($this->content['subject']);
                    });

                    // ulozime do db ze bol email odoslany
                    $email->status = self::EMAIL_SENT;
                    $email->updated_at = Carbon::now();

                    $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    Log::alert(Mail::failures());
                }
            }

        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }



    private function sendTransactionMessage($email)
    {

        try {

            $data_raw = json_decode($email->data);

            $recipients = json_decode($email->recipients, true);
            $template = 'system_transaction';

            $this->content = null;
            $this->emails = $recipients;
            $this->content['template'] = $template;
            $this->content['subject'] = $email->subject;
            $this->content['text_header'] = $email->subject;
            $this->content['text'] = $email->text;
            $this->content['html'] = $email->html;

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;


            if (isset($data_raw->url)){
                $this->content['url'] = $data_raw->url;
            } else {
                $this->content['url'] = "";
            }


            try {

                    Mail::send( ['html' =>'email.html.'. $this->content['template'], 'text' =>  'email.text.' . $this->content['template'] ], ['content' =>  $this->content],

                    function (Message $message) {

                        $message->to($this->emails)
                        ->subject($this->content['subject']);
                    });

                // ulozime do db ze bol email odoslany
                $email->status = self::EMAIL_SENT;
                $email->updated_at = Carbon::now();

                $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    Log::alert(Mail::failures());
                }
            }

        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }


    // pridame do fronty  email notification
    public function addNotificationSystemTransaction( $recipients, $content, $files=null, $account="default")
    {

        // zistime ci je array
        $recipients = json_encode(is_array($recipients) ? $recipients : array($recipients));

        $files = json_encode(is_array($files) ? $files : array($files));

        $url = (isset($content['url'])) ? $content['url'] : null;
        $text = (isset($content['text'])) ? $content['text'] : null;
        $html = (isset($content['html'])) ? $content['html'] : null;
        $id = (isset($content['id'])) ? $content['id'] : null;

        //ak je email odoslanie proforma faktury
        if( intval($content['id']) > 0){

            // odosleme
            $em = new EmailNotification();

            $em->subject = $content['subject'];
            $em->html = $html;
            $em->text = $text;
            $em->account = $account;
            $em->module = 'transaction';
            $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $em->recipients = $recipients;
            $em->data = json_encode([  'url' => $url  ]);
            $em->files = $files;
            $em->module_id = intval($id);
            $em->save();

        } else {

            return false;

        }

    }





    // odosleme serial
    public function addNotificationInfoTextToCue($membership, $account="default")
    {
        $module = 'serial';
        $subject = " o BFORB";
        $files = null;

        // spravy
        for( $i = 1; $i <= 4; $i++) {
            $template = 'new_member_serial_part_' . $i;
            // sprava pre kazdeho clena
            foreach ($membership as $m){
                $em = new EmailNotification();
                $em->subject = $i. '. časť seriálu ' . $subject;
                $em->html = null;
                $em->text = null;
                $em->account = $account;
                $em->module = $module;
                $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now())->addWeeks($i)->format('Y-m-d') . ' 08:30:00';
                $em->files = $files;
                $em->module_id = $i;
                $em->data = json_encode(['template' => $template, 'member' => $m->only('email', 'name', 'surname', 'gender') ]);
                $em->recipients = json_encode($m->email);
                $em->save();
            }
        }
    }





    // ulozenie emailov do cue, odoslabie
    // pri odoslani faktury sa email vysklada az pri odoslani
    public function addNotificationProformaToCue($id, $module, $recipients, $content, $data=null, $files=null, $date_send, $account='default'){

        $recipients = json_encode(is_array($recipients) ? $recipients : array($recipients));
        $files = json_encode(is_array($files) ? $files : array($files));

        //ak je email odoslanie proforma faktury
        if(strcmp($module, 'proforma') == 0){

            if($data['invoice']){

                foreach ($data['invoice'] as $k => $v){
                    if($k > 0){
                        // odosleme druhu cast platby druha faktura aj 10 dni pred splatnostou
                        $em = new EmailNotification();

                        $em->subject = __('email.invoice_new_member_title');
                        $em->html = null;
                        $em->text = null;
                        $em->account = $account;
                        $em->module = $module;
                        $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', $v['date_payment'])->subDays(10);
                        $em->recipients = $recipients;
                        $em->data = json_encode(['invoice' => is_array($v) ? [$v] : array($v), 'member' =>  $data['member']]);
                        $em->files = $files;
                        $em->module_id = intval($id);
                        $em->save();

                    }
                }
            }

            // odosleme proforma pri vygenerovani aj s 1 alebo 2 proforma fakturami
            $em = new EmailNotification();

            $em->subject = __('email.invoice_new_member_title');
            $em->html = null;
            $em->text = null;
            $em->account = $account;
            $em->module = $module;
            $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $em->recipients = $recipients;
            $em->data = json_encode(['invoice' => is_array($data['invoice']) ? $data['invoice'] : array($data['invoice']), 'member' =>$data['member'] ]);
            $em->files = $files;
            $em->module_id = intval($id);
            $em->save();

        }

    }




    // ulozenie emailov do cue, odoslabie
    // pri odoslani faktury sa email vysklada az pri odoslani
    public function addNotificationFinalInvoiceToCue($id, $module, $recipients, $content, $data=null, $files=null, $date_send, $account='default'){

        $recipients = json_encode(is_array($recipients) ? $recipients : array($recipients));
        $files = json_encode(is_array($files) ? $files : array($files));

        $invoice = Invoice::find($id)->first();

        //ak je email odoslanie proforma faktury
        if(strcmp($module, 'final') == 0 && $invoice){

            // odosleme
            $em = new EmailNotification();

            $em->subject = __('email.invoice_final_title');
            $em->html = null;
            $em->text = null;
            $em->account = $account;
            $em->module = $module;
            $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $em->recipients = $recipients;
            $em->data = json_encode(['invoice' => is_array($data['invoice']) ? $data['invoice'] : array($data['invoice']), 'member' => $data['member'] ]);
            $em->files = $files;
            $em->module_id = intval($id);
            $em->save();

        }

    }



    // odosleme ostru fakturu do notification
    public function sendInvoiceMembershipUsers($invoice_id)
    {

        $email_notification = new EmailNotification();

        // faktura na odoslanie
        $invoice = Invoice::where('proforma', 1)->where('status', 5)->where('id', $invoice_id)->first();

        if($invoice){
            // proforma faktura ktora bola vygenerovana k členstvu
            $proforma = Invoice::where('reference', $invoice->id)->first();
            if($proforma){
                // ak existuje zaznam o povodnej proforme
                $membership_invoice = DB::table('membership_invoice')->where('membership_invoice.invoice_id' , $proforma->id)->first();

                // ak neexistuje vazba na memmbership exit
                if(!$membership_invoice){
                    return;
                }

                $membership = Membership::find($membership_invoice->membership_id);

                //ak neexistuje membership a faktura za clenstvo bola vygenerovana samostatne
                if($membership){
                    foreach ($membership->user as $member){

                        $module = 'final';
                        $recipients = [ $member->email, $membership->company->email ];
                        $content = null;
                        // udaje o fakture
                        $data['invoice']['invoice_id'] = $invoice->id;
                        $data['invoice']['variable_symbol'] = $invoice->variable_symbol;
                        $data['invoice']['amount'] = $invoice->price_w_dph;
                        $data['invoice']['date_payment'] = $invoice->date_paid;
                        $data['invoice']['description_payment'] = $invoice->variable_symbol;

                        // udaje o clenoch
                        $data['member'] = $member;

                        //odosleme prirucku
                        if(File::exists(storage_path('app/files/') . 'Clenska_prirucka_2017.pdf')){
                            $files[] = storage_path('app/files/') . 'Clenska_prirucka_2017.pdf';
                        }else{
                            $files = null;
                        }

                        $date_to_send = now();
                        $account = 'default';

                        $email_notification->addNotificationFinalInvoiceToCue($invoice_id, $module, $recipients, $content, $data, $files, $date_to_send, $account);
                    }

                    //odosleme serial ak bola faktura z membershipu
                    $email_notification->addNotificationInfoTextToCue($membership->user);

                }

            }

        }

    }




    private function sendProformaInvoice($email)
    {
        try {

            $invoice_class = new Invoice();

            $data_raw = json_decode($email->data);

            $recipients = json_decode($email->recipients);
            $invoices = $data_raw->invoice;
            $member = $data_raw->member;

            // pridame kopiu pre juraja
            if(!is_array($recipients))  $recipients = [$recipients];

            array_push($recipients, "marian@patak.sk", "juraj.bais@bforb.sk");

            $this->content = null;

            $this->invoices = $invoices;
            $this->content['text_header'] = __('email.invoice_new_member_title');
            $this->content['member'] = $member;
            //aby neodisiel email na rovnake adresy 2x
            $this->emails = array_unique($recipients);

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            // TODO CACHE
            //$this->content['signature'] = User::find(config('invoice.signature.proforma'));
            $this->content['signature'] = User::find(23);

            // zistime membership ak je fa za mbs
            foreach ($this->invoices as $i){
                $membership[] = $invoice_class->getMembershipFromInvoice($i->invoice_id)->pluck('renew_id')->first();
            }

            $renew = false;
            if($membership){
                //unique
                $membership = array_unique($membership);
                if ($membership[0] > 0) $renew = true;
            }

            $template = ( $renew == true ) ?  'renew_membership' :  'new_membership';

                try {

                    Mail::send(['html' =>'email.html.' . $template, 'text' =>  'email.text.' . $template ], ['content' =>  $this->content], function (Message $message) {

                        $inv_pdf = new InvoicePdf();
                        $message->to($this->emails)
                            ->subject($this->content['text_header']);

                        foreach ($this->invoices as $i){
                            //vygenerujeme fakturu
                            $inv_string = $inv_pdf->generatePdfInvoice($i->invoice_id, 'S');
                            //pripravime ako prilohu
                            $message->attachData( $inv_string, $i->variable_symbol . '.pdf');
                        }

                    });

                    // ulozime do db ze bol email odoslany
                    $email->status = self::EMAIL_SENT;
                    $email->updated_at = Carbon::now();

                    $email->save();

                } catch (Exception $e) {

                    if (count(Mail::failures()) > 0) {
                        //dd(Mail::failures());
                    }
                }

            }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }




    private function sendFinalInvoice($email)
    {

        try {

            $data_raw = json_decode($email->data);

            $recipients = json_decode($email->recipients);
            $invoices = $data_raw->invoice;
            $member = $data_raw->member;

            // pridame kopiu pre juraja
            if(!is_array($recipients))  $recipients = [$recipients];
            array_push($recipients, "marian@patak.sk", "juraj.bais@bforb.sk");

            $this->content = null;
            $this->emails = $recipients;
            $this->invoices = $invoices;
            $this->file_send = null;
            $this->file_send = json_decode($email->files);

            $this->content['text_header'] = __('email.invoice_final_title');
            $this->content['member'] = $member;

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            // TODO CACHE
            //$this->content['signature'] = User::find(config('invoice.signature.proforma'));
            $this->content['signature'] = User::find(23);

            try {

                Mail::send(['html' =>'email.html.new_membership_final', 'text' =>  'email.text.new_membership_final'], ['content' =>  $this->content], function (Message $message) {

                    $inv_pdf = new InvoicePdf();
                    $message->to($this->emails)
                        ->subject($this->content['text_header']);

                        //vygenerujeme fakturu
                        $inv_string = $inv_pdf->generatePdfInvoice($this->invoices->invoice_id, 'S');

                        //pripravime ako prilohu
                        $message->attachData( $inv_string, $this->invoices->variable_symbol . '.pdf');

                        // odosleme subory
                        foreach( $this->file_send as $f ){
                            $message->attach($f, [ 'mime' => 'application/pdf' ]);
                        }

                });

                // ulozime do db ze bol email odoslany
                $email->status = self::EMAIL_SENT;
                $email->updated_at = Carbon::now();

                $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    //dd(Mail::failures());
                }
            }

        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }




    // pridame do fronty  email notification
    public function addNotificationThankYou( $recipients, $content, $account="default")
    {

        // zistime ci je array
        $recipients = json_encode(is_array($recipients) ? $recipients : array($recipients));

        $url = (isset($content['url'])) ? $content['url'] : null;
        $text = (isset($content['text'])) ? $content['text'] : null;
        $html = (isset($content['html'])) ? $content['html'] : null;
        $event_id = (isset($content['event_id'])) ? intval($content['event_id']) : null;
        $attendance_id = (isset($content['attendance_id'])) ? $content['attendance_id'] : null;
        $user_to_send = (isset($content['user_to_send'])) ? $content['user_to_send'] : null;
        $user_status_id = (isset($content['user_status_id'])) ? intval($content['user_status_id']) : null;
        $invitation_type = (isset($content['invitation_type'])) ? intval($content['invitation_type']) : null;

        //ak je email odoslanie
        if( intval($content['id']) > 0){

            // odosleme
            $em = new EmailNotification();

            $em->subject = $content['subject'];
            $em->html = $html;
            $em->text = $text;
            $em->account = $account;
            $em->module = 'thank-you';
            $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $em->recipients = $recipients;

            $em->data = json_encode([  'url' => $url,
                                        'user_to_send' => $user_to_send,
                                        'attendance_id' => $attendance_id ,
                                        'event_id' =>  $event_id,
                                        'user_status_id' =>  $user_status_id,
                                        'invitation_type' =>  $invitation_type
                                    ]);

            $em->files = null;
            $em->module_id = $attendance_id;
            $em->save();

        } else {

            return false;

        }

    }



    private function sendThankYou($email)
    {

        try {

            $data_raw = json_decode($email->data);

            $recipients = json_decode($email->recipients, true);
            $template = 'thank_you';

            $event_id = intval($data_raw->event_id);

            $images = DB::table('events_images')->where('event_id', intval($event_id))
                                    ->orderByDesc('id')->limit(3)->get();

            $this->content = null;
            $this->emails = $recipients;
            $this->content['template'] = $template;
            $this->content['subject'] = $email->subject;
            $this->content['text_header'] = $email->subject;
            $this->content['text'] = $email->text;
            $this->content['html'] = $email->html;
            $this->content['user_to_send'] = $data_raw->user_to_send;
            $this->content['images_from_event'] = ($images->count() > 0) ?  $images : null;

            if (intval($data_raw->invitation_type) == 2) {
                //podakovanie pre clena
                $this->content['markdown_text'] = Markdown::parse(getEventText(4));
            } else {
                //podakovanie pre neclena
                $this->content['markdown_text'] = Markdown::parse(getEventText(2));
            }

            // pozvanka je na oodoslanie nie preview
            $this->content['preview'] = false;

            $this->content['url'] = (isset($data_raw->url)) ? $data_raw->url : '';

            //dd($this->content);

            try {

                Mail::send( ['html' =>'email.html.'. $this->content['template'], 'text' =>  'email.text.' . $this->content['template'] ], ['content' =>  $this->content],

                    function (Message $message) {

                        $message->to($this->emails)
                            ->subject($this->content['subject']);
                    });

                // ulozime do db ze bol email odoslany
                $email->status = self::EMAIL_SENT;
                $email->updated_at = Carbon::now();
                $email->save();

            } catch (Exception $e) {

                if (count(Mail::failures()) > 0) {
                    Log::alert(Mail::failures());
                }
            }

        }
        catch ( Exception $e) {
            return $e->getMessage();
        }

    }



}
