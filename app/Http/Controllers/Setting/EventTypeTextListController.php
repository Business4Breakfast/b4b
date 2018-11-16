<?php

namespace App\Http\Controllers\Setting;

use App\Models\Event\EventTypes;
use App\Models\UploadImages;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventTypeTextListController extends Controller
{

    protected $module = 'event-type-text'; // name of module
    protected $table = 'event_type_text'; // name of table


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
        view()->share('backend_title', 'Udalosti texty email'); //title

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items_raw = DB::table($this->table)
            ->get();

        $event_types = DB::table('event_types')->get();

        $items = [];

        foreach ($items_raw as $k => $v){

            $json = (strlen($v->event_type) >2)  ?  json_decode($v->event_type) : [];
            $type = DB::table('event_types')->whereIn('id', $json)->pluck('name')->toArray();
            $membership = (strlen($v->membership) >2)  ?  json_decode($v->membership) : [];

            if( $request->search_type && in_array( $request->search_type,  $json) ){
                $items[$k] = $v;
                $items[$k]->json = $json;
                $items[$k]->type = $type;
                $items[$k]->membership = $membership;

            }elseif (!$request->search_type){
                $items[$k] = $v;
                $items[$k]->json = $json;
                $items[$k]->type = $type;
                $items[$k]->membership = $membership;

            }


        }

        $items = collect($items);

        return view('events.event_type_text.index')
            ->with('event_types', $event_types)
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

        $event_type = EventTypes::all();

        return view('events.event_type_text.add')
            ->with('event_type', $event_type)
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
            'description' => 'required'
        ];

        $this->validate($request,$rules);

        $request->merge([
                        'user_id_create' => Auth::user()->id,
                        'event_type'=> json_encode($request->event_type),
                        'membership'=> json_encode($request->membership)
                        ]);

        DB::table($this->table)->insert($request->only('description', 'user_id_create', 'event_type', 'membership'));

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

        return view('events.event_type_text.add')
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
        $event_type = EventTypes::all();

        return view('events.event_type_text.edit')
            ->with('event_type', $event_type)
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

        $module = 'event-type-text';

        $rules = [
            'description' => 'required'
        ];

        $this->validate($request,$rules);

        $data['description'] = $request->description;
        $data['event_type'] = json_encode($request->event_type);
        $data['updated_at'] = Carbon::now();
        $data['membership'] = json_encode($request->membership);


        $type_text = DB::table($this->table)->where( 'id',  $id )->update($data);

        if ($request->hasFile('files')){

            $image = new UploadImages();
            $image->deleteImage($module, $id);
            $image->procesImage( array($request->files->get('files')) , $module, $id, $request->name . '-' . $id );
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
