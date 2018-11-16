<?php

namespace App\Http\Controllers\Finance;

use App\Helpers\IMAP\Imap;
use App\Models\Company;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceImapEmail;
use App\Models\Finance\InvoicePdf;
use App\Models\Finance\InvoiceText;
use App\Models\Membership;
use App\User;
use Genkgo\Camt\Config;
use Genkgo\Camt\Reader;
use Genkgo\Camt\Util\StringToUnits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SSilence\ImapClient\ImapClient;
use TCPDF;


class InvoiceController extends Controller
{

    private $request = null;

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('finance.invoice.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('finance.invoice.index'), 'icon' => 'list', 'class' => 'btn-success']
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

        $type = config('invoice.prefix');
        $query = Invoice::query();
        $req = [];

        $this->request = $request;

        if (isset($request->search_category) && $request->search_category > 0 ) {
            if ($request->search_category == 2) {
                $query = $query->whereIn('proforma', [1,9]);
                $req['search_category'] = 2;
            } elseif ($request->search_category == 9){
                $query = $query->where('proforma', 9);
                $req['search_category']  = 9;
            } else {
                $query = $query->where('proforma', 1);
                $req['search_category']  = 1;
            }
        } else {
            $query = $query->whereIn('proforma', [1,9]);
            $req['search_category'] = 2;
        }


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
            $query = $query->where('prefix', $request->search_type);
            $req['search_type']  = $request->search_type;
        } else {
            $req['search_type'] = 0;
        }

        if (isset($request->search_company) &&  strlen($request->search_company) > 0) {
            $query = $query->where('company_title', 'like', '%' . $request->search_company . '%');
            $req['search_company'] = $request->search_company;
        } else $req['search_company'] = null;


        if (isset($request->search_price) &&  strlen($request->search_price) > 0) {
            $query = $query->where(function($query)
            {
                $query->orWhere('price_w_dph', 'like', '%' . $this->request->search_price . '%')
                    ->orWhere('price', 'like', '%' . $this->request->search_price . '%')
                    ->orWhere('variable_symbol', 'like', '%' . $this->request->search_price . '%');
            });

            $req['search_price'] = $request->search_price;
            $req['search_category'] = 2;
            $req['search_type'] = 0;
            $req['search_status'] = 0;

        } else $req['search_price'] = null;

        $items = $query->orderByDesc('variable_symbol')
            ->paginate(30)
            ->appends(request()
            ->query());

        $sum['price'] = $items->sum('price');
        $sum['price_dph'] = $items->sum('price_dph');
        $sum['price_w_dph'] = $items->sum('price_w_dph');


        return view('finance.invoice.index')
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
        view()->share('backend_title', 'Vytvoriť novú faktúru'); //title

        $companies = Company::all();
        $countries = DB::table('countries')->get();
        $texts = InvoiceText::all()->sortBy('name');
        $type = config('invoice.prefix');

        return view('finance.invoice.add')
            ->with('countries', $countries)
            ->with('companies', $companies)
            ->with('type', $type)
            ->with('texts', $texts);

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
            'invoice_type' => 'required',
            //'company_id' => 'required',
            'ico' => 'required',
            'address_street' => 'required',
            'address_psc' => 'required',
            'address_city' => 'required',
            'address_country' => 'required',
            'date_create' => 'required',
            'date_pay_to' => 'required',
            'date_delivery' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'year' => 'required',
        ];

        $this->validate($request,$rules);

        $inv = new Invoice;

        $res = $inv->createInvoiceFromMembership($request);

        return redirect()->route('finance.invoice.index')->with('message', 'Záznam úspešne pridany');


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $inv = new Invoice();

        $created_invoice = $inv->createInvoiceFromProforma($id);

        if($created_invoice){

            // vygenerujeme uhrady do tabulky invoice_payments
            $inv->addNewPaymentFromProformaInvoice($created_invoice);

            // nastavime status faktury a uhradenie
            $inv->payInvoiceCheck($created_invoice['invoice_id'],  $created_invoice['variable_symbol']);

            // TODO odosleme fakturu

        }


        view()->share('backend_title', 'Úprava faktúry'); //title

        $item = Invoice::findOrFail($id);

        $companies = Company::all();
        $countries = DB::table('countries')->get();
        $texts = InvoiceText::all()->sortBy('name');
        $type = config('invoice.prefix');

        $membership_id = DB::table('membership_invoice')->where('invoice_id', $id)->pluck('membership_id');
        $memberships = Membership::whereIn('id', $membership_id)->get();

        return view('finance.invoice.edit')
            ->with('item',$item)
            ->with('countries', $countries)
            ->with('companies', $companies)
            ->with('type', $type)
            ->with('memberships', $memberships)
            ->with('texts', $texts);

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

        $rules = [
            'ico' => 'required',
            'address_street' => 'required',
            'address_psc' => 'required',
            'address_city' => 'required',
            'address_country' => 'required',
            'date_create' => 'required',
            'date_pay_to' => 'required',
            'date_delivery' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
        ];

        $this->validate($request,$rules);

        $inv = Invoice::findOrFail($id);

        $inv->company_title = $request->company_title;
        $inv->ico = $request->ico;
        $inv->dic = $request->dic;
        $inv->ic_dph = $request->ic_dph;
        $inv->address_street = $request->address_street;
        $inv->address_psc = $request->address_psc;
        $inv->address_city = $request->address_city;
        $inv->address_country = $request->address_country;

        $inv->date_create = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_create . '00:00:01')->toDateTimeString();
        $inv->date_pay_to = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_pay_to . '23:59:01')->toDateTimeString();
        $inv->date_delivery = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_delivery . '23:59:01')->toDateTimeString();

        $inv->date_paid = $request->date_paid;
        $inv->paid_description = $request->date_paid;

        $inv->price = $request->price;

        //fakturujeme bez dph
        if($request->vat_invoice == 1){
            $inv->price_dph = 0;
            $inv->price_w_dph = $request->price;
        } else {
            $inv->price_dph = round($request->price / 100 * config('invoice.setting.vat'), 2);
            $inv->price_w_dph = $request->price + round($request->price / 100 * config('invoice.setting.vat'), 2);
        }

        $inv->description = $request->description;
        $inv->save();

        return redirect()->route('finance.invoice.index')->with('message', 'Záznam úspešne upravený');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Invoice::destroy($id);
        return redirect()->route('finance.invoice.index')->with('message', 'Záznam vymazaný');
    }


    public function print($id)
    {

        $inv_pdf = new InvoicePdf();
        $inv_pdf->generatePdfInvoice($id);

    }

    public function payment($id)
    {

        $inv_imap = new InvoiceImapEmail();
        $inv_imap->readImapMail();

    }



}
