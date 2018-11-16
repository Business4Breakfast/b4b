<?php

namespace App\Models\Finance;

use App\Models\Membership;
use App\User;
use Genkgo\Camt\Config;
use Genkgo\Camt\Reader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    /**
     * The database table name industry used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    protected $fillable = [

        'serial_number',
        'proforma',
        'variable_symbol',
        'year',
        'prefix',
        'suplier_ico', 'suplier_dic', 'suplier_ic_dph',
        'suplier_registration',
        'suplier_address_street',
        'suplier_address_psc',
        'suplier_address_city',
        'suplier_address_country',

        'company_id',
        'ico', 'dic', 'ic_dph',
        'address_street',
        'address_psc',
        'address_city',
        'address_country',
        'date_create',
        'date_pay_to',
        'description',
        'status',
        'date_payed',
        'price',
        'price_dph',
        'price_w_dph'

    ];



    // na zaklade cisla faktury vratime membersip a k tomu prisluchajuci useri
    public function getUsersFromMembersipInvoice($invoice_id, $active=1)
    {

        $invoice_id = intval($invoice_id);
        $active = intval($active);

        $membership_invoice = DB::table('membership_invoice AS mi')
            ->select('mu.user_id' )
            ->join('memberships AS m', 'm.id', '=', 'mi.membership_id')
            ->join('membership_user AS mu', 'm.id', '=', 'mu.membership_id' )
            ->where('mi.invoice_id', $invoice_id)
            ->where('m.active', $active )
            ->distinct()
            ->pluck('mu.user_id' );

        $users = User::whereIn('id' , $membership_invoice->toArray())->get();

        return $users;

    }




    public function createInvoiceFromMembership($data, $membership=null)
    {

        $inv = new Invoice;

        $proforma =  ($data->proforma_invoice == 1) ? config('invoice.setting.proforma_prefix') : 1;
        $year = $data->year;
        $prefix = intval($data->invoice_type);

        $company_id  =  ($data->company_id > 0) ? $data->company_id : 0;

        $last_number = Invoice::where('prefix',  $prefix)
            ->where('year', $year)
            ->where('proforma', $proforma)
            ->max('serial_number');

        $last_number = $last_number + 1;
        $variable_symbol = $proforma . $data->invoice_type . substr($year, 2) . str_pad($last_number, config('invoice.setting.lenght_serial_number'), '0', STR_PAD_LEFT);

        $inv->variable_symbol = $variable_symbol;
        $inv->serial_number = $last_number;
        $inv->prefix = $prefix;
        $inv->year = $year;
        $inv->proforma = $proforma;

        $inv->suplier_ico = config('invoice.accounts.default.ico');
        $inv->suplier_dic = config('invoice.accounts.default.dic');
        $inv->suplier_ic_dph = config('invoice.accounts.default.ic_dph');
        $inv->suplier_registration = config('invoice.accounts.default.registration');
        $inv->suplier_address_street = config('invoice.accounts.default.street');
        $inv->suplier_address_psc = config('invoice.accounts.default.psc');
        $inv->suplier_address_city = config('invoice.accounts.default.city');
        $inv->suplier_address_country = config('invoice.accounts.default.country');
        $inv->suplier_company = config('invoice.accounts.default.company');

        $inv->company_id = $company_id;
        $inv->company_title = $data->company_title;
        $inv->ico = $data->ico;
        $inv->dic = $data->dic;
        $inv->ic_dph = $data->ic_dph;
        $inv->address_street = $data->address_street;
        $inv->address_psc = $data->address_psc;
        $inv->address_city = $data->address_city;
        $inv->address_country = $data->address_country;

        $inv->date_create = Carbon::createFromFormat('d.m.Y H:i:s', $data->date_create . '00:00:01')->toDateTimeString();
        $inv->date_pay_to = Carbon::createFromFormat('d.m.Y H:i:s', $data->date_pay_to . '23:59:01')->toDateTimeString();
        $inv->date_delivery = Carbon::createFromFormat('d.m.Y H:i:s', $data->date_create . '23:59:01')->toDateTimeString();

        $inv->date_paid = $data->date_paid;
        $inv->paid_description = $data->date_paid;
        $inv->user_id_create = Auth::getUser()->id;
        $inv->price = $data->price;

        //fakturujeme bez dph
        if($data->vat_invoice == 1){
            $inv->price_dph = 0;
            $inv->price_w_dph = $data->price;
        } else {
            $inv->price_dph = round($data->price / 100 * config('invoice.setting.vat'), 2);
            $inv->price_w_dph = $data->price + round($data->price / 100 * config('invoice.setting.vat'), 2);
        }

        $inv->description = $data->description;
        $inv->save();

        $res = ['id' => $inv->id, 'variable_symbol' => $variable_symbol];

        return $res;
    }



    // vytvorenie faktury z array data
    public function createInvoiceFromMembershipArray($data, $membership=null)
    {

        $inv = new Invoice;

        $proforma =  ($data['proforma_invoice'] == 1) ? config('invoice.setting.proforma_prefix')  : 1;
        $year = $data['year'];
        $prefix = intval($data['invoice_type']);
        $company_id  =  ($data['company_id'] > 0) ? $data['company_id'] : 0;

        $last_number = Invoice::where('prefix',  $prefix)
            ->where('year', $year)
            ->where('proforma', $proforma)
            ->max('serial_number');

        $last_number = $last_number +1;
        $variable_symbol = $proforma . $prefix . substr($year, 2) . str_pad($last_number, config('invoice.setting.lenght_serial_number'), '0', STR_PAD_LEFT);

        $inv->variable_symbol = $variable_symbol;
        $inv->serial_number = $last_number;
        $inv->prefix = $prefix;
        $inv->year = $year;
        $inv->proforma = $proforma;

        $inv->suplier_ico = config('invoice.accounts.default.ico');
        $inv->suplier_dic = config('invoice.accounts.default.dic');
        $inv->suplier_ic_dph = config('invoice.accounts.default.ic_dph');
        $inv->suplier_registration = config('invoice.accounts.default.registration');
        $inv->suplier_address_street = config('invoice.accounts.default.street');
        $inv->suplier_address_psc = config('invoice.accounts.default.psc');
        $inv->suplier_address_city = config('invoice.accounts.default.city');
        $inv->suplier_address_country = config('invoice.accounts.default.country');
        $inv->suplier_company = config('invoice.accounts.default.company');

        $inv->company_id = $company_id;
        $inv->company_title = $data['company_title'];
        $inv->ico = $data['ico'];
        $inv->dic = $data['dic'];
        $inv->ic_dph = $data['ic_dph'];
        $inv->address_street = $data['address_street'];
        $inv->address_psc = $data['address_psc'];
        $inv->address_city = $data['address_city'];
        $inv->address_country = $data['address_country'];
        $inv->date_create = Carbon::createFromFormat('Y-m-d H:i:s', $data['date_create'])->toDateTimeString();
        $inv->date_pay_to = Carbon::createFromFormat('Y-m-d H:i:s', $data['date_pay_to'])->toDateTimeString();
        $inv->date_delivery = Carbon::createFromFormat('Y-m-d H:i:s', $data['date_create'])->toDateTimeString();

        $inv->user_id_create = Auth::getUser()->id;
        $inv->price = $data['price'];

        //fakturujeme bez dph
        if($data['vat_invoice'] == 1){
            $inv->price_dph = 0;
            $inv->price_w_dph = $data['price'];
        } else {
            $inv->price_dph = round($data['price'] / 100 * config('invoice.setting.vat'), 2);
            $inv->price_w_dph = $data['price'] + round($data['price'] / 100 * config('invoice.setting.vat'), 2);
        }

        $inv->description = $data['description'];

        $inv->save();

        $res = [    'invoice_id' => $inv->id,
                    'variable_symbol' => $variable_symbol,
                    'amount' => $data['price'],
                    'date_payment' => Carbon::createFromFormat('Y-m-d H:i:s', $data['date_pay_to'])->toDateTimeString(),
                    'description_payment' => $variable_symbol,
                ];

        return $res;
    }


    // uhradime vygenerovanu fakturu z proforma
    public function addNewPaymentFromProformaInvoice($payment)
    {
        $res = null;

        $data['invoice_id'] = $payment['invoice_id'];
        $data['price_payment'] = $payment['amount'];
        $data['date_payment'] =  $payment['date_payment'];
        $data['description_payment'] =  $payment['description_payment'];

        if (Auth::check()) {
            $data['user_id'] = Auth::user()->id;
        }else{
            $data['user_id'] = 0;
        }

        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();

        //dd($data);

        if( $data['price_payment'] != 0){
            $last_id = DB::table('invoice_payments')->insertGetId($data,'id');
            // vratime id akt zaznamu
            $res = ['id' => $last_id];
        }

        return $res;

    }


    // parsovanie emailovej spravy t b-banking
    function parseEmailTatrabankaStatement($from, $subject, $text)
    {
       // vyextrahujeme emaolpvu adres odosielatela
        preg_match("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i", $from, $matches);
        $email_from  = $matches[0];
        // povolene adresy
        $email_address =['marianpatak67@gmail.com', 'b-mail@tatrabanka.sk', 'marian@directreal.sk', 'statement@y9.sk', 'system@bforb.sk'];

        // accept len z overenych email adries
        if(!in_array($email_from, $email_address)){
            return false;
        }

        $res = null;

        //subject
        if(preg_match('/Kredit(.*?) .*[)]\\B/' , $subject, $match) && count($match)>1){

            //odstranime rozdelene na riadky
            $str = str_replace("="."\r\n", " ", $text);
            $rows = explode("\n", $str);
            $rows = str_replace("\r", "", $rows);
            $rows = str_replace("=20", "", $rows);

//            dump($rows);

            //prejdeme riadky
            foreach ($rows as $row){

                // popis transakcie
                if(preg_match('/([1-9]|0[1-9]|[1-2][0-9]|3[0-1]).([1-9]|1[0-2]).[0-9]{4}(.*?)$/', $row, $match)){

                    $row_transaction = stripcslashes($match[0]);

                    //ci je dátum v regularnom vyraze
                    if(preg_match('/(.*?) bol/', $row_transaction, $match_date) && count($match_date)>1){
                        //datum string format
                        $res['date_payment'] = Carbon::createFromFormat("j.n.Y H:i", trim($match_date[1]))->toDateTimeString();
//                        $res['date_payment'] = trim($match_date[1]);
                    };

                }



                // popis transakcie
                if(preg_match('/Popis transakcie(.*?):/', $row)){
                    if(preg_match('/: (.*?)$/', $row, $match) && count($match)>1){
                        $res['description_payment'] = $match[1];
                    }
                }

                // referencia platitela
                if(preg_match('/Referencia platitela(.*?):/', $row)){

                    //najdeme string symbolov /VS20179045/SS/KS0308
                    if(preg_match('/: (.*?)$/', $row, $match) && count($match)>1){

                        $reference = explode('/', trim($match[1]));
                        if(count($reference)>2){
                            //VS
                            $res['variable_symbol'] = ltrim(substr($reference[1], 2), '0');
                            //SS
                            $res['specific_symbol'] = ltrim(substr($reference[2], 2), '0');
                            //KS
                            $res['constant_symbol'] = ltrim(substr($reference[3], 2), '0');
                        }
                    }

                }


                //ci je suma v regularnom vyraze
                if(preg_match('/ o (.*?) EUR/', $row, $match) && count($match)>1){
                    //suma float

//                    dump($match);

                    $match_result = trim(str_replace(' ', '', $match[1]));
                    $amount = floatval(str_replace(',', '.', $match_result));
                    $res['amount_payment'] =  $amount;
                };

                //Informacia pre prijemcu
                if(preg_match('/Informacia pre prijemcu(.*?):/', $row)){
                    if(preg_match('/: (.*?)$/', $row, $match) && count($match)>1){
                        $res['recipient_info'] = $match[1];
                    }
                }

                if(preg_match('/Kredit(.*?) .*[)]\\B/', $subject, $match)) {
                    $res['subject'] = $match[0];
                }else{
                    $res['subject'] = null;
                }

                $res['from'] = $email_from;

            }

        } else {
            return false;
        }

        return $res;
    }



    // parsovanie bankovych vypisov xml
    public function readBankStatement()
    {

        $reader = new Reader(Config::getDefault());

        $message = $reader->readFile(storage_path('app/public/bank_statements').'/2623179245_171001_171031_592136BA2B.xml');
        $statements = $message->getRecords();

        foreach ($statements as $statement) {
            //$entries = $statement->getId();
            $i=0;
            foreach ($statement->getEntries() as $key => $item){
                //len prijmy
                if(!strcmp(substr($item->getAmount()->getAmount(), 0,1), '-') == 0){
                    //len ak je var symbol spravny
                    if(strcmp(substr($item->getTransactionDetail()->getReference()->getEndToEndId(), 0,3), '/VS')==0){

                        $symbols = explode('/', $item->getTransactionDetail()->getReference()->getEndToEndId() );
                        $res[$i]['VS'] = substr($symbols[1], 2);
                        $res[$i]['SS'] = substr($symbols[2], 2);
                        $res[$i]['KS']= substr($symbols[3], 2);
                        $res[$i]['CUR'] = $item->getAmount()->getCurrency()->getName();
                        $res[$i]['SUM'] = number_format(substr($item->getAmount()->getAmount(), 0, -2) . '.' .
                            substr($item->getAmount()->getAmount(), -2), 2);
                        $res[$i]['DATE'] = Carbon::createFromTimestamp($item->getValueDate()->getTimestamp())->toDateTimeString();
                        $i++;
                    }
                }
            }
        }

        //dump($res);
    }



    // zaplatenie faktury
    public function payInvoiceCheck($invoice_id, $paid_desc="")
    {
        //zistime ci faktura este nie je uhradena
        $invoice = Invoice::findOrFail($invoice_id);

        $res = false;

        //ak je nezaplatena alebo ciastocne
        if(in_array( $invoice->status,[config('invoice.status.unpaid'), config('invoice.status.partial-paid'), config('invoice.status.paid-more') ]) == 1 ) {

            //spocitame kolko uhrad uz ma faktura
            $inv_payment = $this->getInvoicePayments($invoice_id);

            //kolko je uz zaplatene
            $inv_paid_sum = $inv_payment->sum('price_payment');

            // nastavime datum uhrady
            $invoice->date_paid = Carbon::now();
            $invoice->paid_description = $paid_desc;

            //ak zaplatena suma spolu s rozdielom je suma faktury
            if ($inv_paid_sum == floatval($invoice->price_w_dph)) {
                $invoice->status = config('invoice.status.paid');
                $invoice->save();

                $res = config('invoice.status.paid');

            } elseif($inv_paid_sum < floatval($invoice->price_w_dph)) {
                $invoice->status = config('invoice.status.partial-paid');
                $invoice->save();
                $res = config('invoice.status.partial-paid');

            } elseif($inv_paid_sum > floatval($invoice->price_w_dph)) {
                $invoice->status = config('invoice.status.paid-more');
                $invoice->save();
                $res = config('invoice.status.paid-more');
            }
        }

        return $res;
    }





    // zaplatenie (aktivne) clenstva na zaklade uhrady proformafaktury
    public function payMembershipFromPayInvoice($invoice_id)
    {
        //zistime ci faktura  je uhradena status
        $invoice = Invoice::findOrFail($invoice_id);
        $loged_user = 0;

        //ak je zaplatena
        if ($invoice->status == config('invoice.status.paid')) {
            $membership_invoice = $this->getMembershipFromInvoice($invoice_id);

            if($membership_invoice->isNotEmpty()){

                if (Auth::check()) {
                    $loged_user = Auth::user()->id;
                }

                foreach ($membership_invoice as $m){

                    $membership = Membership::findOrFail($m->id);

                    //Vytvorime zaznam v tabulke uhrad
                    $data['membership_id'] = $m->id;
                    $data['date_payment'] = Carbon::now()->format('Y-m-d H:i:s');
                    $data['description'] = 'Internal transfer to bank account';
                    $data['payment_type'] = 7;
                    $data['user_id'] = $loged_user;
                    DB::table('membership_payment')->insert($data);

                    // nastavime clenstvo aktivne
                    $membership->active = 1;


                    // ak predlzujeme clenstvo nezmenime datumy zaciatku a konca clenstva
                    if($membership->renew_id == 0){
                        //nastavime na 12 mesiacov od uhrady faktury
                        $membership->valid_from = Carbon::createFromFormat('Y-m-d H:i:s', $invoice->date_paid)->format('d.m.Y');
                        $membership->valid_to = Carbon::createFromFormat('Y-m-d H:i:s', $invoice->date_paid)->addYear()->format('d.m.Y');

                    }

                    $membership->save();

                }

            }

        }

    }


    public function createInvoiceFromProforma($invoice_id)
    {

        $inv = new Invoice;
        $prof = $inv->findOrFail($invoice_id);
        $res = null;

        // status zaplatena, je proforma a splatnost max 30 dni po splatnosti
        if(intval($prof->status) == 5 && intval($prof->proforma) == 9  && $prof->reference == 0 ){

            // TODO overit 30dni po splatnosti

            $proforma =  1;
            $prefix = $prof->prefix;
            $company_id  =  ($prof->company_id > 0) ? $prof->company_id : 0;
            $year = $prof->year;

            $last_number = Invoice::where('prefix',  $prefix)
                ->where('year', $year)
                ->where('proforma', 1)
                ->max('serial_number');
            $last_number = $last_number +1;

            $variable_symbol = $proforma . $prefix . substr($year, 2) . str_pad($last_number, config('invoice.setting.lenght_serial_number'), '0', STR_PAD_LEFT);

            $inv->variable_symbol = $variable_symbol;
            $inv->serial_number = $last_number;
            $inv->prefix = $prefix;
            $inv->year = $year;
            $inv->proforma = $proforma;

            $inv->suplier_ico = config('invoice.accounts.default.ico');
            $inv->suplier_dic = config('invoice.accounts.default.dic');
            $inv->suplier_ic_dph = config('invoice.accounts.default.ic_dph');
            $inv->suplier_registration = config('invoice.accounts.default.registration');
            $inv->suplier_address_street = config('invoice.accounts.default.street');
            $inv->suplier_address_psc = config('invoice.accounts.default.psc');
            $inv->suplier_address_city = config('invoice.accounts.default.city');
            $inv->suplier_address_country = config('invoice.accounts.default.country');
            $inv->suplier_company = config('invoice.accounts.default.company');

            $inv->company_id = $company_id;
            $inv->company_title = $prof->company_title;
            $inv->ico = $prof->ico;
            $inv->dic = $prof->dic;
            $inv->ic_dph = $prof->ic_dph;
            $inv->address_street = $prof->address_street;
            $inv->address_psc = $prof->address_psc;
            $inv->address_city = $prof->address_city;
            $inv->address_country = $prof->address_country;

            $inv->date_create = Carbon::createFromFormat('Y-m-d H:i:s', $prof->date_paid )->toDateTimeString();
            $inv->date_pay_to = Carbon::createFromFormat('Y-m-d H:i:s', $prof->date_paid )->toDateTimeString();
            $inv->date_delivery = Carbon::createFromFormat('Y-m-d H:i:s', $prof->date_paid)->toDateTimeString();

            $inv->date_paid = $prof->date_paid;
            $inv->paid_description = $prof->variable_symbol;

            if (Auth::check()) {
                $inv->user_id_create = Auth::user()->id;
            }else{
                $inv->user_id_create = 0;
            }

            $inv->price = $prof->price;

            //fakturujeme bez dph

            $payment_amount = 0;
            if($prof->price_dph  == 0){
                $inv->price_dph = 0;
                $payment_amount = $prof->price;
                $inv->price_w_dph = $payment_amount;
            } else {
                $inv->price_dph = round($prof->price / 100 * config('invoice.setting.vat'), 2);
                $payment_amount = $prof->price + round($prof->price / 100 * config('invoice.setting.vat'), 2);
                $inv->price_w_dph = $payment_amount;
            }


            // zistime ci je k fakture membership ak ano doplnime termin platnosti clenstva do oszrej faktury
           $membership =  DB::table('membership_invoice AS mi')
                ->select()
                ->join('memberships AS m', 'mi.membership_id', '=', 'm.id', 'left')
                ->where('mi.invoice_id', $prof->id )
                ->first();

            $inv->description = $prof->description;

            if($membership){
                $inv->description .= "Za obdobie od: " . Carbon::createFromFormat('Y-m-d H:i:s', $membership->valid_from)->format('d.m.Y');
                $inv->description .= " - do: " . Carbon::createFromFormat('Y-m-d H:i:s', $membership->valid_to)->format('d.m.Y') . "  \n";
            }

            $inv->description .= "\n\r"."Faktúra vytvorená z proforma faktúry číslo: " . $prof->variable_symbol;

            // zapiseme id proforma faktury z ktorej bola vytvorena
            $inv->reference = $prof->id;
            $inv->save();

            // do proforma faktury zapiseme id vzniknutej faktury
            $prof->reference = $inv->id;
            $prof->save();

            $res = [ 'invoice_id' => $inv->id,
                'variable_symbol' => $variable_symbol,
                'amount' => $payment_amount,
                'date_payment' => Carbon::createFromFormat('Y-m-d H:i:s', $prof->date_paid )->toDateTimeString(),
                'description_payment' => $variable_symbol,
            ];

        }

        return $res;
    }


    // ziskame vsetky platby k fakture
    public function getInvoicePayments($invoice_id)
    {
        $inv_payment = DB::table('invoice_payments')
            ->join('users', 'users.id', '=', 'invoice_payments.user_id')
            ->where('invoice_payments.invoice_id','=', $invoice_id)
            ->get(['invoice_payments.*', 'users.name as user_name', 'users.surname as user_surname']);

        return $inv_payment;
    }


    // zistime ci faktura je za membership
    public function getMembershipFromInvoice($invoice_id)
    {
        $membership_invoice = DB::table('membership_invoice')
            ->join('memberships', 'memberships.id', '=', 'membership_invoice.membership_id')
            ->where('membership_invoice.invoice_id','=', $invoice_id)
            ->get(['memberships.*']);

        return $membership_invoice;
    }

    // zistime ci faktura je za membership
    public function getIdInvoiceFromVariableSymbol($variable_symbol)
    {
        return Invoice::where('variable_symbol', $variable_symbol)->first();
    }




}
