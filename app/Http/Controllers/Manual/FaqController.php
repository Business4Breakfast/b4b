<?php

namespace App\Http\Controllers\Manual;

use App\Models\UploadImages;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{

    protected $module = 'faq'; // name of module
    protected $table = 'faq'; // name of module
    protected $prefix = 'manual'; // name of module



    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route($this->prefix .'.' . $this->module . '.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route($this->prefix .'.' . $this->module . '.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Znalostná databáza - (časté otázky)'); //title

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = DB::table($this->table)
            ->select($this->table.'.*', 'users.name AS user_name', 'users.surname AS user_surname' )
            ->join('users','users.id', '=', $this->table.'.user_id_create')
            ->get();

        return view($this->prefix . '.' . $this->module . '.index')
            ->with('items', $items)
            ->with('prefix', $this->prefix)
            ->with('module', $this->module);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->prefix . '.' . $this->module . '.add')
            ->with('prefix', $this->prefix)
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

        $request->merge(['user_id_create' => Auth::user()->id]);

        DB::table($this->table)->insert($request->only('name', 'description', 'user_id_create', 'polarity'));

        return redirect()->route($this->prefix. '.'.$this->module.'.index')->with('message', 'Záznam úspešne vytvorený');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        dd($id. 'show');

        return view($this->prefix . '.' . $this->module . '.add')
            ->with('module', $this->module)
            ->with('prefix', $this->prefix)
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

        $item = DB::table($this->table)->find($id);

        return view($this->prefix . '.' . $this->module . '.edit')
            ->with('module', $this->module)
            ->with('prefix', $this->prefix)
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

        DB::table($this->table)->where( 'id',  $id )->update($request->only('name', 'description', 'polarity'));

        return redirect()->route($this->prefix . '.' . $this->module.'.index')->with('message', 'Záznam úspešne upravený');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table($this->table)->delete($id);

        return redirect()->route($this->prefix . '.' .$this->module.'.index')->with('message', 'Záznam vymazaný');
    }



    public function active(Request $request, $id)
    {
        DB::table($this->table)->where('id', $id)->update(['active' => $request->active]);
        return redirect()->route($this->prefix . '.' . $this->module.'.index');
    }

}
