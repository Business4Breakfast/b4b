<?php

namespace App\Http\Controllers\Finance;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laratrust;

class CashRegisterController extends Controller
{

    private  $request = null;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = null;
        $req = null;
        $req['search_type'] = null;
        $req['search_category'] = null;
        $req['search_description'] = null;
        $req['search_amount'] = null;

        $this->request = $request;

        $type = DB::table('cash_register_type')->where('active', 1)->get();


        $query = DB::table('cash_registers AS cr')
            ->select('cr.*','u.name AS user_name', 'u.surname AS user_surname', 'crt.name AS type_name')
            ->join('cash_register_type AS crt', 'crt.id', '=', 'cr.type_id', 'left')
            ->join('users AS u', 'u.id', '=', 'cr.user_id_create')
            ->orderByDesc('cr.id');


        if($request){

            if (isset($request->search_category) && $request->search_category == 1 ) {
                $query = $query->where('cr.polarity', 1);
                $req['search_category']  = 1;
            } elseif($request->search_category == 2) {
                $query = $query->where('cr.polarity', 0);
                $req['search_category'] = 2;
            }

            if (isset($request->search_type) && $request->search_type > 0 ) {
                $query = $query->where('cr.type_id', $request->search_type);
                $req['search_type']  = $request->search_type;
            } else {
                $req['search_type'] = 0;
            }

            if (isset($request->search_description) &&  strlen($request->search_description) > 0) {
                $query = $query->where('cr.description', 'like', '%' . $request->search_description . '%');
                $req['search_description'] = $request->search_description;
            } else $req['search_description'] = null;

            if (isset($request->search_amount) &&  strlen($request->search_amount) > 0) {
                $query = $query->where(function($query)
                {
                    $query->orWhere('amount', 'like', '%' . $this->request->search_amount . '%')
                            ->orWhere('amount', '=',  $this->request->search_amount );
                });

                $req['search_amount'] = $request->search_amount;
                $req['search_category'] = 0;
                $req['search_type'] = 0;
                $req['search_description'] = "";

            } else $req['search_amount'] = null;

        }


        $items = $query->get();

        // opravnenie na pokladnu
        if(!Laratrust::can('finance-cash-register-index')){

            return redirect()->route('dashboard.index')
                ->with('message', 'Nemáš oprávnenie prezerať túto stránku');
        }

        return view('finance.cash-register.index')
                ->with('type', $type )
                ->with('req', $req)
                ->with('items', $items);

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
            'date_payment' => 'required|date',
            'amount' => "required|regex:/^\d*(\,\d{1,2})?$/",
            'type' => 'required'
        ];

        $this->validate($request,$rules);


        $type = DB::table('cash_register_type')->where('id', $request->type )->first();

        $sub_total = DB::table('cash_registers')
                ->latest()->value('sub_total');
        $sub_total = floatval($sub_total);


        $data['date_payment'] = Carbon::createFromFormat('d.m.Y', $request->date_payment);
        $data['type_id'] = $request->type;
        $data['polarity'] = $type->polarity;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['description'] = $request->description;
        $data['user_id_create'] = Auth::user()->id;

        $amount = str_replace(',','.', $request->amount);

        $amount = floatval($amount);


        // AK JE plusova transakcia
        if($type->polarity == 1){
            if(floatval($request->amount) < 0) {
                $amount = $amount * -1;
            }
        }else{
            if(floatval($request->amount) > 0) {
                $amount = $amount * -1;
            }
        }

        $data['amount'] =$amount;
        $data['sub_total'] = $sub_total + $amount;

        DB::table('cash_registers')->insert($data);



        return redirect()->route('finance.cash-register.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::table('cash_registers')->delete($id);

        return redirect()->route('finance.cash-register.index');

    }
}
