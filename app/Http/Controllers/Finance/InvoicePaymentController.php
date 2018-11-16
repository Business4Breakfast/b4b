<?php

namespace App\Http\Controllers\Finance;

use App\Models\Company;
use App\Models\Finance\Invoice;
use App\Models\Notification\EmailNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class InvoicePaymentController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Prehľad faktúr', 'link' => route('finance.invoice.index'), 'icon' => 'list', 'class' => 'btn-success'],
                ['name' => 'Úhrady faktúr', 'link' => route('finance.invoice-payment.index'), 'icon' => 'money', 'class' => 'btn-info'],
            ]
        ];

        view()->share('action_menu', $action);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = DB::table('invoice_payments')
            ->join('users', 'users.id', '=', 'invoice_payments.user_id')
            ->join('invoices as i', 'i.id', '=', 'invoice_payments.invoice_id')
            //->where('invoice_payments.invoice_id','=', $invoice->id)
            ->select(['invoice_payments.*', 'users.name as user_name', 'users.surname as user_surname',
                    'i.variable_symbol', 'i.company_title', 'i.price'])
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('finance.invoice-payment.index')
            ->with('items', $items);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        view()->share('backend_title', 'Pridanie úhrady'); //title

        $inv = new Invoice();
        $invoice = $inv::findOrFail($id);
        // ak existuje k fakture spolocnost
        $company = Company::find($invoice->company_id);

        //platby faktury , prejdeme ci existuju platby pre konkretnu fakturu
        $inv_payment = $inv->getInvoicePayments($id);

        $due_payment = $invoice->price_w_dph - $inv_payment->sum('price_payment');

        return view('finance.invoice-payment.edit')
            ->with('invoice', $invoice)
            ->with('inv_payment', $inv_payment)
            ->with('company', $company)
            ->with('due_payment', $due_payment);
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

        $invoice = new Invoice();

        $rules = [
            'date_payment' => 'required|date',
            'price_payment' => 'numeric',
            'email' => 'email'
        ];

        $this->validate($request,$rules);


        $paid_desc = "Manual payment ";
        $invoice_id = intval($request->invoice_id);

        $data['invoice_id'] = $invoice_id;
        $data['price_payment'] = $request->price_payment;
        $data['date_payment'] = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_payment . '22:59:59')->toDateTimeString();
        $data['description_payment'] = $paid_desc . $request->description;
        $data['user_id'] = Auth::user()->id;
        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();

        if( $data['price_payment'] != 0) {
            $last_id = DB::table('invoice_payments')->insertGetId($data, 'id');

            // uhradime faktúru na zaklade vlozenej platby
            $invoice->payInvoiceCheck($invoice_id, $paid_desc);

            // uhradime membership ak bola faktura za memmberhip
            // TODO nastavime uhradu clenského, ak je cela suma na rok ak 1/2 tak len do splatnosti druhej faktury
            $invoice->payMembershipFromPayInvoice($invoice_id);

            // ak je proforma vygenerujeme ostru fakturu
            $new_invoice = $invoice->createInvoiceFromProforma($invoice_id);

            //ak bola vygenerovana nova ostra faktura z proforma faktury
            if ($new_invoice) {
                // vytvorime uhrady z proforma faktury do uhrad faktúr
                $invoice->addNewPaymentFromProformaInvoice($new_invoice);

                // uhradime novu ostru faktúru na zaklade vlozenej platby
                $invoice->payInvoiceCheck($new_invoice['invoice_id'], $new_invoice['variable_symbol']);

                // odosleme ostru fakturu emailom a posleme serial ak je faktura za členské
                $email_notification = new EmailNotification();
                $email_notification->sendInvoiceMembershipUsers($new_invoice['invoice_id']);
            }

        }

        return redirect()->route('finance.invoice-payment.index')->with('message', 'Platba úspešne vytvorená');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd($id);
    }



}
