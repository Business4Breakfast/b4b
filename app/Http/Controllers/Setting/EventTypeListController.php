<?php

namespace App\Http\Controllers\Setting;

use App\Models\UploadImages;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventTypeListController extends Controller
{

    protected $module = 'event-type'; // name of module
    protected $table = 'event_types'; // name of module


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
        view()->share('backend_title', 'Typy udalostí'); //title

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = DB::table($this->table)->get();

        return view('events.event_types.index')
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
        return view('events.event_types.add')
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

        $item_last_id = DB::table($this->table)->
        insertGetId($request->only('name', 'description', 'user_id_create',
            'invite_own_club', 'invite_guests', 'change_status_guest', 'invite_other_executives',
            'invite_other_clubs', 'color', 'email_text_custom'));


        //if update succes and is files
        if ($request->hasFile('files') && $item_last_id > 0){

            $image = new UploadImages();
            $image->deleteImage($this->module, $item_last_id);
            $image->procesImage($request->files, $this->module, $item_last_id, $request->name . '-' . $item_last_id );
        }

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

        return view('events.event_types.add')
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

        $item = DB::table($this->table)->find($id);

        return view('events.event_types.edit')
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

        $item = DB::table($this->table)->find($id);

        DB::table($this->table)->where( 'id',  $id )->update($request->only('name', 'description', 'user_id_create',
            'invite_own_club', 'invite_guests',
            'change_status_guest',
            'invite_other_executives',
            'invite_other_clubs',
            'btn_invite_guest',
            'btn_confirm_attend',
            'btn_refused_attend',
            'btn_deleted_guest',
            'email_text_custom',
            'color'
        ));

        //if update succes and is files
        if ($request->hasFile('files')){

            $image = new UploadImages();
            $image->deleteImage($this->module, $id);
            $image->procesImage($request->files, 'event_type', $item->id, $item->name . '-' . $item->id );
        }

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
        DB::table($this->table)->delete($id);

        return redirect()->route('setting.'.$this->module.'.index')->with('message', 'Záznam vymazaný');
    }



    public function active(Request $request, $id)
    {
        DB::table($this->table)->where('id', $id)->update(['active' => $request->active]);
        return redirect()->route('setting.'.$this->module.'.index');
    }

}
