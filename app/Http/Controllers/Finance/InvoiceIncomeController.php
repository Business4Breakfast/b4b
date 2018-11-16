<?php

namespace App\Http\Controllers\Finance;

use App\Helpers\VATCheck\VatCheck;
use App\Models\Club;
use App\Models\Company;
use App\Models\Finance\InvoiceIncome;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceIncomeController extends Controller
{

    private $request = null;

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('finance.invoice-income.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('finance.invoice-income.index'), 'icon' => 'list', 'class' => 'btn-success']
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

        $type = DB::table('invoice_income_type')->where('active', 1)->get();

        $query = InvoiceIncome::query();
        $req = [];

        $this->request = $request;


        if (isset($request->search_status) && $request->search_status >= 0 ) {
            $req['search_status'] = $request->search_status;
            switch ($request->search_status) {
                case 1:
                    $query = $query->where('status', 0);
                    break;
                case 2:
                    $query = $query->where('status', 5);
                    break;
                case 3:
                    $query = $query->where('status', 2);
                    break;
                case 4:
                    $query = $query->where('date_pay_to', '>' , Carbon::now()->subMonths(1));
                    $query = $query->where('date_pay_to', '<' , Carbon::now());
                    $query = $query->where('status', 0);
                    break;
                case 5:
                    $query = $query->where('date_pay_to', '<' , Carbon::now()->subMonths(1));
                    $query = $query->where('status', 0);
                    break;
            }

        } else {
            $query = $query->where('status', 0);
            $req['search_status'] = 1;
        }

        if (isset($request->search_type) && $request->search_type > 0 ) {
            $query = $query->where('type', $request->search_type);
            $req['search_type']  = $request->search_type;
        } else {
            $req['search_type'] = 0;
        }

        if (isset($request->search_company) &&  strlen($request->search_company) > 0) {
            $query = $query->where('suplier_company', 'like', '%' . $request->search_company . '%');
            $req['search_company'] = $request->search_company;
        } else $req['search_company'] = null;


        if (isset($request->search_price) &&  strlen($request->search_price) > 0) {

            $query = $query->where(function($query)
            {
                $query->orWhere('price_dph', 'like', '%' . $this->request->search_price . '%')
                    ->orWhere('price', 'like', '%' . $this->request->search_price . '%')
                    ->orWhere('variable_symbol', 'like', '%' . $this->request->search_price . '%');
            });

            $req['search_price'] = $request->search_price;
            $req['search_type'] = 0;
            $req['search_status'] = 0;

        } else $req['search_price'] = null;

        $items = $query->orderByDesc('internal_id')->get();

        $sum['price_paid'] = $items->sum('price_paid');
        $sum['price_dph'] = $items->sum('price_dph');
        $sum['price_w_dph'] = $items->sum('price_w_dph');


        return view('finance.invoice-income.index')
            ->with('req', $req)
            ->with('sum', $sum)
            ->with('type', $type)
            ->with('items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        view()->share('backend_title', 'Pridanie dodávateľskej faktúry'); //title

        $companies = Company::all();
        $users = User::where('admin', 1)->whereNotIn('id', [0,1])->orderBy('surname')->get();
        $countries = DB::table('countries')->get();
        $texts = DB::table('invoice_income_type')->where('active', 1)->get();


        return view('finance.invoice-income.add')
            ->with('companies', $companies)
            ->with('texts', $texts)
            ->with('users', $users)
            ->with('countries', $countries);

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
            'type' => 'required',
            'title' => 'required',
            'ico' => 'required',
            'dic' => 'required',
            'address_street' => 'required',
            'address_psc' => 'required',
            'address_city' => 'required',
            'address_country' => 'required',
            'date_delivery' => 'required|date|date_format:d.m.Y',
            'date_pay_to' => 'required|date|date_format:d.m.Y',
            'price' => 'required|numeric',
            'dph' => 'required|numeric',
            'price_dph' => 'required|numeric',
            'description' => 'required',
        ];

        $this->validate($request,$rules);

        $inv = new InvoiceIncome();

        $inv->variable_symbol = $request->var_symbol;
        $inv->internal_id = $request->internal_id;
        $inv->suplier_ico = $request->ico;
        $inv->suplier_dic = $request->dic;
        $inv->suplier_ic_dph = $request->ic_dph;
        $inv->suplier_address_street = $request->address_street;
        $inv->suplier_address_psc =$request->address_psc;
        $inv->suplier_address_city = $request->address_city;
        $inv->suplier_address_country = $request->address_country;
        $inv->suplier_company = $request->company_name;

        $inv->suplier_company_id = (empty($request->company_id)) ? 0 : $request->company_id;

        $inv->suplier_contact_person = $request->contact_person;
        $inv->suplier_email= $request->email;
        $inv->suplier_phone = $request->phone;

        $inv->date_pay_to = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_pay_to . '23:59:01')->toDateTimeString();
        $inv->date_delivery = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_delivery . '23:59:01')->toDateTimeString();

        $inv->date_paid = $request->date_paid;
        $inv->paid_description = $request->date_paid;
        $inv->user_id_create = Auth::user()->id;
        $inv->price = $request->price;
        $inv->price_dph = $request->dph;
        $inv->price_w_dph = $request->price_dph;
        $inv->price_paid = 0;

        $inv->type = $request->type;
        $inv->description = $request->description;

        $inv->save();

        return redirect()->route('finance.invoice-income.index')->with('messge', 'Záznam vytvorený');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        view()->share('backend_title', 'Úhrada dodávateľskej faktúry'); //title

        $countries = DB::table('countries')->get();
        $texts = DB::table('invoice_income_type')->where('active', 1)->get();
        $inv = InvoiceIncome::find($id);

        return view('finance.invoice-income.payment')
            ->with('texts', $texts)
            ->with('inv', $inv);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        view()->share('backend_title', 'Úprava dodávateľskej faktúry'); //title

        $countries = DB::table('countries')->get();
        $texts = DB::table('invoice_income_type')->where('active', 1)->get();
        $inv = InvoiceIncome::find($id);

        return view('finance.invoice-income.edit')
            ->with('texts', $texts)
            ->with('inv', $inv)
            ->with('countries', $countries);


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


        if($request->action && strcmp($request->action, 'payment') == 0 ){


            $rules = [
                'date_payment' => 'required|date|date_format:d.m.Y',
                'price_payment' => 'required',
                'description' => 'required',
            ];

            $this->validate($request,$rules);

            $payment_price =  floatval(str_replace( " ", "", $request->price_payment));

            $inv = InvoiceIncome::find($id);

            $inv->date_paid = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_payment . '23:59:01')->toDateTimeString();


            if($payment_price + $inv->price_paid  ==  $inv->price_w_dph ){
                //uhradena presna ciastka
                $inv->price_paid = $inv->price_w_dph;
                $inv->status = 5;
                $inv->paid_description = $inv->paid_description . " \n Uhrada:" . $inv->price_w_dph . " Dátum: " . $inv->date_paid . " \n " . $request->description;

            }elseif ($payment_price + $inv->price_paid  < $inv->price_w_dph ){

                $inv->price_paid = $payment_price + $inv->price_paid ;
                $inv->status = 3;
                $inv->paid_description = $inv->paid_description . " \n Uhrada:" . $payment_price . " Dátum: " . $inv->date_paid . " \n " . $request->description;

            }elseif ($payment_price + $inv->price_paid  > $inv->price_w_dph ){

                $inv->price_paid = $payment_price + $inv->price_paid ;
                $inv->status = 2;
                $inv->paid_description = $inv->paid_description . " \n Uhrada:" . $payment_price . " Dátum: " . $inv->date_paid . " \n " . $request->description;

            }

            $inv->update();

        } else {

            $rules = [
                'type' => 'required',
                'title' => 'required',
                'ico' => 'required',
                'dic' => 'required',
                'address_street' => 'required',
                'address_psc' => 'required',
                'address_city' => 'required',
                'address_country' => 'required',
                'date_delivery' => 'required|date|date_format:d.m.Y',
                'date_pay_to' => 'required|date|date_format:d.m.Y',
                'price' => 'required|numeric',
                'dph' => 'required|numeric',
                'price_dph' => 'required|numeric',
                'description' => 'required',
            ];

            $this->validate($request,$rules);

            $inv = InvoiceIncome::find($id);

            $inv->variable_symbol = $request->var_symbol;
            $inv->internal_id = $request->internal_id;
            $inv->suplier_ico = $request->ico;
            $inv->suplier_dic = $request->dic;
            $inv->suplier_ic_dph = $request->ic_dph;
            $inv->suplier_address_street = $request->address_street;
            $inv->suplier_address_psc =$request->address_psc;
            $inv->suplier_address_city = $request->address_city;
            $inv->suplier_address_country = $request->address_country;
            $inv->suplier_company = $request->title;

            $inv->suplier_contact_person = $request->contact_person;
            $inv->suplier_email= $request->email;
            $inv->suplier_phone = $request->phone;

            $inv->date_pay_to = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_pay_to . '23:59:01')->toDateTimeString();
            $inv->date_delivery = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_delivery . '23:59:01')->toDateTimeString();

            $inv->date_paid = $request->date_paid;
            $inv->paid_description = $request->date_paid;
            $inv->price = $request->price;
            $inv->price_dph = $request->dph;
            $inv->price_w_dph = $request->price_dph;

            $inv->type = $request->type;
            $inv->description = $request->description;

            $inv->update();


        }

        return redirect()->route('finance.invoice-income.index')->with('messge', 'Záznam vytvorený');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        InvoiceIncome::destroy($id);
        return redirect()->route('finance.invoice-income.index')->with('message', 'Záznam vymazaný');    }
}
