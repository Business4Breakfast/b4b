<?php

namespace App\Http\Controllers\Setting;

use App\Helpers\VATCheck\VatCheck;
use App\Models\Setting\Industry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndustryController extends Controller
{

    protected $module = 'industry'; // name of module


    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.' . $this->module . '.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('setting.' . $this->module . '.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Odvetvia podnikania'); //title

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Industry::orderBy('name')->get();

        return view('setting.item_list.index')
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
        return view('setting.item_list.add')
            ->with('module', $this->module);
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

        Industry::create($request->all());

        return redirect()->route('setting.'.$this->module.'.index')->with('message', 'Záznam úspešne vytvorený');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return view('setting.item_list.add')
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
        $item = Industry::findOrFail($id);

        return view('setting.item_list.edit')
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

        $item = Industry::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('setting.'.$this->module.'.index')->with('message', 'Záznam úspešne upravený');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Industry::destroy($id);
        return redirect()->route('setting.'.$this->module.'.index')->with('message', 'Záznam vymazaný');
    }


    public function active(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);
        $industry->update($request->all());
        return redirect()->route('setting.'.$this->module.'.index');
    }

}
