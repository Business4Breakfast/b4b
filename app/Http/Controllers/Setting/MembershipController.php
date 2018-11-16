<?php

namespace App\Http\Controllers\Setting;


use App\Helpers\SMS\SmsGateway;
use App\Models\Club;
use App\Models\Company;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoicePdf;
use App\Models\Membership;
use App\Models\Notification\EmailNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Laratrust;
use PHPUnit\Framework\Error\Error;
use SoapClient;

class MembershipController extends Controller
{
    private $content;
    private $emails;
    private $invoices;
    private $request;

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.membership.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('setting.membership.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $req = null;
        $this->request = $request;

        $membership_class = new Membership();
        $loged_user = Auth::user();
        $club_class = new Club();



        $users = User::where('admin', '=', 1)->whereNotIn('id', [0,1])->get();
        $company = Company::all();
        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();

        $date_min = Membership::min('valid_from');
        $date_max = Membership::max('valid_to');

        $req['search_club'] = "";
        $req['search_company'] = "";
        $req['search_user'] = "";

        $q = DB::table('memberships AS m');

        $q->select('m.id', 'co.company_name AS company_name', 'm.price AS price', 'm.active AS active',
                            'm.valid_from AS valid_from', 'm.valid_to AS valid_to');

        $q->join('membership_club AS mc', 'mc.membership_id', '=', 'm.id', 'left');
        $q->join('companies AS co', 'co.id', '=', 'm.company_id', 'left' );


        if(isset($request->date_from) ) {
            if($request->date_from != Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y') ) {
                $q->where('m.valid_from', '>', Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d H:i:s') );
            }
            $req['date_from'] = $request->date_from;
        } else {
            $req['date_from'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y');
        }

        if(isset($request->date_to) ) {
            if($request->date_to != Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y') ) {
                $q->where('m.valid_to', '>', Carbon::createFromFormat('d.m.Y', $request->date_to)->format('Y-m-d H:i:s') );
            }
            $req['date_to'] = $request->date_to;
        } else {
            $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y');
        }

        if (isset($request->search_status) && $request->search_status >= 0 ) {
            if ($request->search_status == 2) {
                $req['search_status'] = 2;
            } elseif ($request->search_status == 1){
                $q->where('m.active', 1);
                $req['search_status']  = 1;
            } else {
                $q->where('m.active', 0);
                $req['search_status']  = 0;
            }
        } else {
            $q->where('m.active', 1);
            $req['search_status'] = "1";
        }

        if (isset($request->search_club) &&  strlen($request->search_club) > 0) {

                $q->whereExists(function ($query) {
                    $query->select('mc.id')
                        ->from('membership_club AS mc')
                        ->join('clubs AS c', 'c.id', '=', 'mc.club_id')
                        ->whereRaw('m.id = mc.membership_id')
                        ->where('mc.club_id', $this->request->search_club  );
                });

            $req['search_club'] = $request->search_club;
        }


        // ak ma opravnenie vidiet vseky eventy
        if (!Laratrust::can('memberships-listing-all')){

            //ak je uzivatel clenom vykonneho tímu vidi len svoje  udalosti
            $user_clubs = $club_class->getClubsFromUsers($loged_user->id);

            if( Auth::user()->hasRole(['franchisee', 'manager', 'executive-member', '']) ){
                $q->whereIn('mc.club_id', $user_clubs->pluck('id') );
            }

        }


        if (isset($request->search_company) &&  strlen($request->search_company) > 0) {
            $q->where('company_id', $request->search_company);
            $req['search_company'] = $request->search_company;
        }


        if (isset($request->search_user) &&  strlen($request->search_user) > 0) {

            $q->whereExists(function ($query) {
                $query->select('mu.id')
                    ->from('membership_user AS mu')
                    ->whereRaw('mu.membership_id = m.id')
                    ->where('mu.user_id', $this->request->search_user );
            });

            $req['search_user'] = $request->search_user;
        }


        $q->groupBy('m.id', 'co.company_name', 'm.price', 'm.active', 'valid_from', 'valid_to');

        $q->orderBy('valid_to','ASC');
        $membership = $q->paginate(100)
                        ->appends( request()->query() );

        // pridame info o useroch
        if ($membership->items() ){
            foreach ($membership->items() as $v){
                $v->users = $membership_class->getUsersFromMembersip($v->id);
                $v->clubs = $membership_class->getClubsFromMembersip($v->id);
            }
        }

        view()->share('backend_title', 'Prehľad členstiev'); //title

        return view('setting.membership.index')
            ->with('type', null)
            ->with('users', $users)
            ->with('company', $company)
            ->with('clubs', $clubs)
            ->with('req', $req)
            ->with('items', $membership);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        view()->share('backend_title', 'Vytvoriť nové členstvo'); //title

        $companies = Company::all();
        $users = User::where('admin', 1)->whereNotIn('id', [0,1])->orderBy('surname')->get();
        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();

        return view('setting.membership.add')
            ->with('companies', $companies)
            ->with('users', $users)
            ->with('clubs', $clubs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'company_id' => 'required',
            '*.email_more' => 'email',
            'email' => 'required|email',
        ];

        $this->validate($request,$rules);

        //zapiseme noveho
        $membership = Membership::create($request->except(['user','club', 'invoice']));

        //pridame info klube
        $clubs = [];
        $res = $request->only('club');
        foreach ( $res['club'] as $v){
            $clubs[$v] = [
                'active' => 1,
                'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
            ];
        }
        $membership->club()->attach($clubs);

        //pridame info o uzivateloch
        $users = [];
        $res = $request->only('user');
        foreach ( $res['user'] as $v){
            $users[$v] = [
                'active' => 1,
                'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
            ];
        }
        $membership->user()->attach($users);

        //ak je checbox generovat proforma
        if($request->invoice == 1) {

            //vygenerujeme fakturu';
            $new_invoice = $this->generateInvoiceFromMembership($request, $membership);

            if ($request->send_email == 1) {

                $emails_to_send = null;
                //defaultna adresa
                if($request->email){
                    $emails_to_send[] = $request->email;
                }

                //dalsie adresy
                if($request->email_more){
                    foreach ($request->email_more as $e){
                        $emails_to_send[] = $e;
                    }
                }

                // members ktory su clenmi membeshipu
                $members = User::whereIn('id', array_keys($users) )->get();

                $email_notification = new EmailNotification();

                //kazdemu memberpvi posleme email s oslovenim
                if($members){
                    foreach ($members as $member){

                        $id = $membership->id;
                        $module = 'proforma';
                        $recipients = $emails_to_send;
                        $content = null;
                        // udaje o fakturach
                        $data['invoice'] = $new_invoice;
                        // udaje o clenoch
                        $data['member'] = $member;
                        $files = null;
                        $date_to_send = now();
                        $account = 'default';

                        $email_notification->addNotificationProformaToCue($id, $module, $recipients, $content, $data, $files, $date_to_send, $account);

                    }
                }
            }
        }

        return redirect()->route('setting.membership.index')->with('message', 'Členstvo úspešne vytvorené');

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Display renew membership.
     *
     */
    public function renewalMembership($id)
    {
        view()->share('backend_title', 'Predĺženie členstva'); //title

        $data = [];

        $companies = Company::all();
        $users = User::where('admin', 1)->whereNotIn('id', [0,1])->orderBy('surname')->get();
        $clubs = Club::all();
        $membership = Membership::findOrFail($id);

        // overime ci uz bolo clenstvo predlzovane
        if (Membership::where('renew_id', $membership->id)->count() > 0 ){

            Session::flash('alert-danger', 'Členstvo už bolo predĺžené');

            return redirect()->route('setting.membership.index');

        }

        $data['valid_from'] = Carbon::parse($membership->valid_from)->format('d.m.Y');
        $data['valid_to'] = Carbon::parse($membership->valid_to)->format('d.m.Y');

        $data['renew_from'] = Carbon::parse($membership->valid_to)->addDay()->format('d.m.Y');
        $data['renew_to'] = Carbon::parse($membership->valid_to)->addYear()->format('d.m.Y');


        // splatnost sa nastavi na zaciatok clenstva, ak je uz po termine na 10 dni od vystavenia
        if(Carbon::parse($membership->valid_to) >= Carbon::now()){
            // ak je v budocnosti cize
            $data['renew_pay_to'] = Carbon::parse($membership->valid_to)->format('d.m.Y');
        }else{
            $data['renew_pay_to'] = Carbon::now()->addDays(10)->format('d.m.Y');
        }

        $membership_users = $membership->user;

        //ak uz nie je clen doplnime do comboboxu
        $users = $users->merge($membership_users);

        return view('setting.membership.renewal')
            ->with('companies', $companies)
            ->with('users', $users)
            ->with('clubs', $clubs)
            ->with('data', $data)
            ->with('membership', $membership);
    }



    /**
     * Store renew membership.
     *
     */
    public function renewalMembershipStore(Request $request)
    {

        $rules = [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'renew_pay_to' => 'required|date',
            'company_id' => 'required',
            '*.email_more' => 'email',
            'email' => 'required|email',

        ];

        $this->validate($request,$rules);

        //zapiseme noveho
        $membership = Membership::create($request->except(['user','club', 'invoice' ]));

        //pridame info klub
        $clubs = [];
        $res = $request->only('club');
        foreach ( $res['club'] as $v){
            $clubs[$v] = [
                'active' => 1,
                'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
            ];
        }
        $membership->club()->attach($clubs);

        //pridame info o uzivateloch
        $users = [];
        $res = $request->only('user');
        foreach ( $res['user'] as $v){
            $users[$v] = [
                'active' => 1,
                'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
            ];
        }
        $membership->user()->attach($users);

        //ak je checbox generovat proforma
        if($request->invoice == 1) {

            //vygenerujeme fakturu';
            $new_invoice = $this->generateInvoiceFromMembership($request, $membership);

            if ($request->send_email == 1) {

                $emails_to_send = null;
                //defaultna adresa
                if($request->email){
                    $emails_to_send[] = $request->email;
                }

                //dalsie adresy
                if($request->email_more){
                    foreach ($request->email_more as $e){
                        $emails_to_send[] = $e;
                    }
                }

                // members ktory su clenmi membeshipu
                $members = User::whereIn('id', array_keys($users) )->get();

                $email_notification = new EmailNotification();

                //kazdemu memberpvi posleme email s oslovenim
                if($members){
                    foreach ($members as $member){

                        $id = $membership->id;
                        $module = 'proforma';
                        $recipients = $emails_to_send;
                        $content = null;
                        // udaje o fakturach
                        $data['invoice'] = $new_invoice;
                        // udaje o clenoch
                        $data['member'] = $member;
                        $files = null;
                        $date_to_send = now();
                        $account = 'default';

                        $email_notification->addNotificationProformaToCue($id, $module, $recipients, $content, $data, $files, $date_to_send, $account);

                    }
                }
            }
        }

        return redirect()->route('setting.membership.index')->with('message', 'Predĺženie členstva úspešne vytvorené');


    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        view()->share('backend_title', 'Úprava členstva'); //title

        $companies = Company::all();
        $users = User::where('admin', 1)->whereNotIn('id', [0,1])->orderBy('surname')->get();
        $clubs = Club::all();
        $membership = Membership::findOrFail($id);

        $membership_users = $membership->user;

        //ak uz nie je clen doplnime do comboboxu
        $users = $users->merge($membership_users);

        return view('setting.membership.edit')
            ->with('companies', $companies)
            ->with('users', $users)
            ->with('clubs', $clubs)
            ->with('membership', $membership);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $membership = Membership::findOrFail($id);

        $membership->update($request->except(['user','club', 'invoice']));

        //pridame info klube
        $clubs = [];
        $res = $request->only('club');
        if ($res['club']){
            foreach ( $res['club'] as $v){
                $clubs[$v] = [
                    'active' => 1,
                    'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                    'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
                ];
            }
        }

        $membership->club()->sync($clubs);

        //pridame info o uzivateloch
        $users = [];
        $res = $request->only('user');
        if (array_key_exists('user', $res)){
            foreach ( $res['user'] as $v){
                $users[$v] = [
                    'active' => 1,
                    'uses_from' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_from . '00:00:01'),
                    'uses_to' => Carbon::createFromFormat('d.m.Y H:i:s', $request->valid_to . '23:59:59'),
                ];
            }
        }

        $membership->user()->sync($users);


        // ak nie su faktury vygenerujeme
       if(collect($membership->invoice)->isEmpty() == true) {

           if($request->invoice == 1) {

               //vygenerujeme fakturu';
               $new_invoice = $this->generateInvoiceFromMembership($request, $membership);

               if ($request->send_email == 1) {

                   $emails_to_send = null;
                   //defaultna adresa
                   if($request->email){
                       $emails_to_send[] = $request->email;
                   }

                   //dalsie adresy
                   if($request->email_more){
                       foreach ($request->email_more as $e){
                           $emails_to_send[] = $e;
                       }
                   }

                   // members ktory su clenmi membeshipu
                   $members = User::whereIn('id', array_keys($users) )->get();

                   $email_notification = new EmailNotification();

                   //kazdemu memberpvi posleme email s oslovenim
                   if($members){
                       foreach ($members as $member){

                           $module = 'proforma';
                           $recipients = $emails_to_send;
                           $content = null;
                           // udaje o fakturach
                           $data['invoice'] = $new_invoice;
                           // udaje o clenoch
                           $data['member'] = $member;
                           $files = null;
                           $date_to_send = now();
                           $account = 'default';

                           $email_notification->addNotificationProformaToCue($id, $module, $recipients, $content, $data, $files, $date_to_send, $account);

                       }
                   }
               }
           }
       }

       return redirect()->route('setting.membership.index')->with('message', 'Záznam úspešne upravený');

    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Membership::destroy($id);
        return redirect()->route('setting.membership.index')->with('message', 'Záznam vymazaný');
    }


    public function generateInvoiceFromMembership($request, $membership)
    {
        //vytvoríme proforma fakturu
        $invoice = new Invoice();

        // ak je renew id predlzujeme clenstvo
        $renew_membership = ( intval($request->renew_id) > 0 ) ? true : false;

        $description = "";

        $data['company_id'] = $request->company_id;
        $company = Company::find($data['company_id']);
        $data['invoice_type'] = config('invoice.prefix.membership.id'); //proforma
        $data['company_title'] = $company->company_name;
        $data['ico'] = $company->ico;
        $data['dic'] = $company->dic;
        $data['ic_dph'] = $company->ic_dph;
        $data['address_street'] = $company->address_street;
        $data['address_psc'] = $company->address_psc;
        $data['address_city'] = $company->address_city;
        $data['address_country'] = $company->address_country;
//
        $description .= "Fakturujeme Vám ročný poplatok za marketingové služby na mítingoch BFORB č: 00" . $membership->id . "  \n";

        if ($membership->club){
            foreach ($membership->club as $club){
                $description .= "Klub: " . $club->title . "  \n";
            }
        }

        if ($membership->user){
            foreach ($membership->user as $usr){
                $description .= "Člen: " . $usr->full_name . "  \n";
            }
        }

        //$description .= "Za obdobie od: " . Carbon::createFromFormat('Y-m-d H:i:s', $membership->valid_from)->format('d.m.Y');
        //$description .= " - do: " . Carbon::createFromFormat('Y-m-d H:i:s', $membership->valid_to)->format('d.m.Y') . "  \n";
        //$description .= "Este nejaky ďalší text ......." . "  \n";

        $data['vat_invoice'] = 0;
        $data['proforma_invoice'] = 1;

        //ak je platba rozdelena na 50% vystavime 2 faktury s roznymi splastnostami
        $return = null;

        $description_1 = "";

        if($request->divide_50 == 1){
            for ($i = 0; $i <= 1; $i++) {
                if($i==0){
                    //prva platba
                    $data['year'] = Carbon::now()->year;
                    $data['date_create'] = Carbon::now()->toDateTimeString();
                    $data['date_paid'] = Carbon::now()->toDateTimeString();
                    $data['paid_description'] = "";
                    $data['price'] = $request->price / 2;
                    $description_1  =  'Poznámka: 1. časť platby členského poplatku' . "  \n";
                    $data['description'] =  $description . $description_1;

                    if($renew_membership){

                        //predlzujeme clenstvo
                        $data['date_delivery'] = Carbon::now()->toDateTimeString();
                        $data['date_pay_to'] = Carbon::createFromFormat('d.m.Y', $request->renew_pay_to)->toDateTimeString();


                    } else {

                        $data['date_delivery'] = Carbon::now()->toDateTimeString();
                        $data['date_pay_to'] = Carbon::now()->addDays(intval($request->pay_to_day))->toDateTimeString();

                    }


                    $res = collect($data);
                    $return[] = $invoice->createInvoiceFromMembershipArray($res);
                }else{
                    $data['year'] = Carbon::now()->year;
                    $data['date_create'] = Carbon::now()->toDateTimeString();
                    $data['date_paid'] = Carbon::now()->toDateTimeString();
                    $data['paid_description'] = "Fakturujeme Vám 2. platbu členskeho";
                    $data['price'] = $request->price / 2;
                    $description_1 =  'Poznámka: 2. časť platby členského poplatku' . "  \n";
                    $data['description'] =  $description . $description_1;

                    if($renew_membership){

                        //predlzujeme clenstvo
                        $data['date_delivery'] = Carbon::now()->toDateTimeString();
                        $data['date_pay_to'] = Carbon::createFromFormat('d.m.Y', $request->renew_pay_to)->addDays(10)->addMonth(intval($request->divide_month))->toDateTimeString();

                    } else {

                        $data['date_delivery'] = Carbon::now()->toDateTimeString();
                        $data['date_pay_to'] = Carbon::now()->addDays(10)->addMonth(intval($request->divide_month))->toDateTimeString();

                    }


                    $res = collect($data);
                    $return[] = $invoice->createInvoiceFromMembershipArray($res);
                }
            }

        } else {
            //jednorazová platba
            $data['year'] = Carbon::now()->year;
            $data['date_create'] = Carbon::now()->toDateTimeString();
            $data['date_paid'] = Carbon::now()->toDateTimeString();
            $data['paid_description'] = "Fakturujeme Vám ročný poplatok za marketingové služby na mítingoch Business for Breakfast";
            $data['price'] = $request->price;
            $data['description'] =  $description;


            if($renew_membership){

                //predlzujeme clenstvo
                $data['date_delivery'] = Carbon::now()->toDateTimeString();
                $data['date_pay_to'] = Carbon::createFromFormat('d.m.Y', $request->renew_pay_to)->toDateTimeString();

            } else {

                $data['date_delivery'] = Carbon::now()->toDateTimeString();
                $data['date_pay_to'] = Carbon::now()->addDays(intval($request->pay_to_day))->toDateTimeString();

            }

            $res = collect($data);
            $return[] = $invoice->createInvoiceFromMembershipArray($res);
        }

        if($return){
            foreach ($return as $r){
                $membership->invoice()->attach([ 'invoice_id' => $r['invoice_id']] );
            }
        }

        return $return;
    }


    public function payment($id){

        view()->share('backend_title', 'Úhrada členstva'); //title

        $membership = Membership::findOrFail($id);
        $types = DB::table('membership_payment_types')->get();

        $payments = DB::table('membership_payment as mp')
            ->select('mp.id', 'mpt.title as type', 'mp.date_payment', 'mp.description',
                DB::raw("CONCAT(u.name,' ', u.surname) as full_name"), 'mp.payment_type')
            ->join('membership_payment_types as mpt', 'mpt.id', 'mp.payment_type', 'left')
            ->join('users as u', 'u.id', 'mp.user_id', 'left outer' )
            ->where('mp.membership_id', $id)->orderBy('mp.id')->get();

        return view('setting.membership.payment.add')
            ->with('types', $types)
            ->with('payments', $payments)
            ->with('membership', $membership);

    }

    public function paymentStore(Request $request){

        $rules = [
            'date_payment' => 'required|date',
            'payment_type' => 'required',
            'membership_id' => 'required',
        ];

        $this->validate($request,$rules);

        $membership = Membership::find($request->membership_id);

        $data['membership_id'] = $request->membership_id;
        $data['date_payment'] = Carbon::createFromFormat('d.m.Y', $request->date_payment)->format('Y-m-d H:i:s');
        $data['description'] = $request->description;
        $data['payment_type'] = $request->payment_type;
        $data['user_id'] = Auth::user()->id;

        // ak je vlozeny zaznam updatujeme clenstvo
        if(DB::table('membership_payment')->insert($data)){

            // zneaktivnenie clenstva
            if(in_array($request->payment_type, [5,6,8])) {

                $membership->update(['active' => 0]);

            }else{

                $membership->update(['active' => 1]);

            }
        }

        return redirect()->route('setting.membership.index')->with('message', 'Záznam úspešne pridaný');

    }




}
