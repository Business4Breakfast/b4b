<?php

namespace App\Http\Controllers\Finance;

use App\Models\Finance\InvoiceText;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceTextController extends Controller
{

    protected $module = 'invoice-text'; // name of module


    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('finance.' . $this->module . '.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('finance.' . $this->module . '.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Texty do fakturácie'); //title

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = InvoiceText::all()->sortBy('name');

        return view('finance.invoice-text.index')
            ->with('items', $items)
            ->with('module', $this->module);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('finance.invoice-text.add')
            ->with('module', $this->module)
            ;
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
            'name' => 'required'
        ];

        $this->validate($request,$rules);

        InvoiceText::create($request->all());

        return redirect()->route('finance.'.$this->module.'.index')->with('message', 'Záznam úspešne vytvorený');
        //dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return view('finance.invoice-text.add')
            ->with('module', $this->module)
            ->with('item',null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = InvoiceText::findOrFail($id);

        return view('finance.invoice-text.edit')
            ->with('module', $this->module)
            ->with('item', $item);
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
            'name' => 'required'
        ];
        $this->validate($request,$rules);

        $item = InvoiceText::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('finance.'.$this->module.'.index')->with('message', 'Záznam úspešne upravený');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        InvoiceText::destroy($id);
        return redirect()->route('finance.'.$this->module.'.index')->with('message', 'Záznam vymazaný');
    }


    public function active(Request $request, $id)
    {
        $industry = InvoiceText::findOrFail($id);
        $industry->update($request->all());
        return redirect()->route('finance.'.$this->module.'.index');
    }

}
