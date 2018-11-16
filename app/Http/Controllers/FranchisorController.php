<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Company;
use App\Models\Franchisor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FranchisorController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('franchisor.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('franchisor.index'), 'icon' => 'list', 'class' => 'btn-success']
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
        view()->share('backend_title', 'Zoznam franchísorov'); //title

        $franchisors = Franchisor::all();

        return view('franchisor.index')
            ->with('franchisors', $franchisors);

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

        return view('franchisor.add')
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

        $data['company_id'] = $request->company_id;
        $data['valid_from'] = Carbon::createFromFormat('d.m.Y', $request->valid_from )->format('Y-m-d H:i:s');
        $data['valid_to'] =  Carbon::createFromFormat('d.m.Y', $request->valid_to )->format('Y-m-d H:i:s');
        $data['price'] = $request->price;
        $data['email'] = $request->email;
        $data['user_id'] = $request->user;
        $data['date_create'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['date_update'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['description'] = $request->description;

        $rules = [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'company_id' => 'required',
            'price' => 'required|numeric',
             'user' => 'required',
            'email' => 'required|email',
        ];

        $this->validate($request,$rules);

        DB::table('franchisors')->insert($data);

        return redirect()->route('franchisor.index')->with('message', 'Franchisor úspešne vytvorený');

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $franchisor = Franchisor::find($id);

        $companies = Company::all();
        $users = User::where('admin', 1)->whereNotIn('id', [0,1])->orderBy('surname')->get();

        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();

        return view('franchisor.edit')
            ->with('franchisor', $franchisor)
            ->with('companies', $companies)
            ->with('users', $users)
            ->with('clubs', $clubs);
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

        $franchisor = Franchisor::find($id);

        //dd($request);

        $data['company_id'] = $request->company_id;
        $data['valid_from'] = Carbon::createFromFormat('d.m.Y', $request->valid_from )->format('Y-m-d H:i:s');
        $data['valid_to'] =  Carbon::createFromFormat('d.m.Y', $request->valid_to )->format('Y-m-d H:i:s');
        $data['email'] = $request->email;
        $data['user_id'] = $request->user;
        $data['price'] = $request->price;
        $data['description'] = $request->description;

        $rules = [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'company_id' => 'required',
            'price' => 'required|numeric',
            'user' => 'required',
            'email' => 'required|email',
        ];

        $this->validate($request,$rules);

        DB::table('franchisors')->where('id', $id)->update($data);

        return redirect()->route('franchisor.index')->with('message', 'Franchisor úspešne upraveny');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Franchisor::destroy($id);
        return redirect()->route('franchisor.index')->with('message', 'Záznam vymazaný');
    }
}
