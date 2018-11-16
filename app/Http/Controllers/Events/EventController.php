<?php

namespace App\Http\Controllers\Events;

use App\Models\Club;

use App\Models\Event\Event;
use App\Models\Event\EventTypes;
use App\Models\UploadFiles;
use App\Models\UploadImages;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust;

class EventController extends Controller
{


    private $emails = null;
    private $invoices = null;
    private $content = null;
    private $file_send = null;


    public function __construct()
    {

        $this->hashid = new Hashids('salt', 10);


        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať novú udalosť', 'link' => route('events.listing.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('events.listing.index'), 'icon' => 'list', 'class' => 'btn-success']
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

        view()->share('backend_title', 'Prehľad udalostí klubu'); //title

        $this->request = $request;

        $club_class = new Club();

        $loged_user = Auth::user();


        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();
        $event_types = EventTypes::all();

        $date_min = Event::min('event_from');
        $date_max = Event::max('event_to');
        $req = [];

        $req['search_status'] = 1;
        $req['search_club'] = null;
        $req['search_type'] = 1;

        $query = DB::table('events as e')
            ->select(
                ['e.*', 'c.id as club_id', 'c.title as club_title', 'e.event_type', 'e.title AS event_title', 'et.name as event_type_name','c.address_city as city',
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id ) as attend_count'),
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id AND al.status = 2) as attend_count_confirm'),
                    DB::raw('( SELECT count(id) FROM  reference_coupons AS rc WHERE rc.reference_id = e.id ) as reference_coupons_count'),
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id AND al.user_attend = 1) as attended_count')

                ])
            ->join('clubs as c', 'c.id','=', 'e.club_id' )
            ->join('event_types as et', 'et.id','=', 'e.event_type', 'left' );

        // ak ma opravnenie vidiet vseky eventy
        if (!Laratrust::can('events-listing-all')){

            //ak je uzivatel clenom vykonneho tímu vidi len svoje  udalosti
            $user_clubs = $club_class->getClubsFromUsers($loged_user->id);
            if( Auth::user()->hasRole(['franchisee', 'manager', 'executive-member', '']) ){
                $query->whereIn('e.club_id', $user_clubs->pluck('id') );
            }
        }


        if (isset($request->search_status)) {
            if ($request->search_status == 2) {
               // $query = $query->whereIn('active', [1,9]);
                $query->where('e.active', 2);
                $req['search_status'] = 2;
            } elseif ($request->search_status == 1){
                $query->where('e.active', 1);
                $req['search_status']  = 1;
            } elseif ($request->search_status == 0){
                $query->where('e.active', 0);
                $req['search_status']  = 0;
            } else {
                //$query->where('e.active', 0);
                $req['search_status']  = 9;
            }
        } else {
            $req['search_status']  = 1;
        }



        if(isset($request->date_from) ) {
            if($request->date_from != Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y') ) {
                $query->where('e.event_from', '>', Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d H:i:s'));
            }
            $req['date_from'] = $request->date_from;
        } else {
            $req['date_from'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y');
        }

        if(isset($request->date_to) ) {
            if($request->date_to != Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y') ) {
                $query->where('e.event_to', '<', Carbon::createFromFormat('d.m.Y', $request->date_to)->format('Y-m-d H:i:s'));
            }
            $req['date_to'] = $request->date_to;
        } else {
//            $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y');
            $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', now())->addMonths(3)->format('d.m.Y');
            $query->where('e.event_to', '<', Carbon::now()->addWeeks(2)->format('Y-m-d H:i:s'));
        }

        if (isset($request->search_club) &&  strlen($request->search_club) > 0) {
            $query->where( 'club_id', $request->search_club);
            $req['search_club'] = $request->search_club;
        }

        if (isset($request->search_type) &&  strlen($request->search_type) > 0) {
            $query->where( 'event_type', $request->search_type);
            $req['search_type'] = $request->search_type;
        }

        $query->orderByDesc('e.event_from', 'e.id');

        $items = $query->paginate(20)
                        ->appends(request()
                        ->query());

        return view('events.index')
            ->with('req',$req)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('items', $items);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $club_class = new Club();

        $user_clubs = $club_class->getClubsFromUsers(Auth::user()->id);

        $query = Club::where('active', 1);

        //ak je uzivatel clenom vykonneho moze vytvorit udalost len pre svoj klub
        if( Auth::user()->hasRole(['franchisee', 'manager', 'executive-member', '']) ){
            $query->whereIn('id', $user_clubs->pluck('id') );
        }

        $clubs = $query->orderBy('short_title')->get();

        $countries = DB::table('countries')->get();
        $counties = DB::table('counties')->orderBy('name')->get();
        $districts = DB::table('districts')->orderBy('name')->get();
        $event_types = EventTypes::all();

        return view('events.add')
            ->with('counties', $counties)
            ->with('districts', $districts)
            ->with('countries', $countries)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $module = 'event';

        $rules = [
            'title' => 'required',
            'event_type' => 'required',
            'price' => 'numeric',
            'district_id' => 'required',
            'county_id' => 'required',
            'club_id' => 'required',
            'host_name' => 'required',
            'date_create' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'address_street' => 'required',
            'address_psc' => 'required',
            'address_city' => 'required',
            'address_country' => 'required',
            'url' => 'required|url',
            'lat' => 'required',
            'lng' => 'required',
        ];

        $this->validate($request,$rules);

        $count_record = $request->repeat_count;
        $interval_days = $request->repeat_interval;

        $event_from = Carbon::createFromFormat( 'd.m.Y H:i', $request->date_create . $request->time_from);
        $event_to = Carbon::createFromFormat( 'd.m.Y H:i', $request->date_create . $request->time_to);

        if($event_to->diffInMinutes($event_from, true) < 60){
            return redirect()->back()
                ->withErrors('Dátum do nemôže byť skôr ako dátum od');
        }

        if($request->repeat_event_check != 1){
            $count_record = 1;
        }

        if( $count_record > 0){
            $interval = 0;
            for ($i = 1; $i <= $count_record; $i++) {

                $date_from = Carbon::createFromFormat( 'd.m.Y H:i', $request->date_create . $request->time_from)->addDay($interval);
                $date_to = Carbon::createFromFormat( 'd.m.Y H:i', $request->date_create . $request->time_to)->addDay($interval);

                $event = new Event();

                $event->active = $request->input('active', 0);
                $event->title = $request->title;
                $event->event_type = $request->event_type;
                $event->club_id = $request->club_id;
                $event->host_name = $request->host_name;

                $event->event_from = $date_from;
                $event->event_to = $date_to;

                $event->address_street = $request->address_street;
                $event->address_psc = $request->address_psc;
                $event->address_city = $request->address_city;
                $event->address_country = $request->address_country;
                $event->address_description = $request->address_description;

                $event->district_id = $request->district_id;
                $event->county_id = $request->county_id;
                $event->lat = $request->lat;
                $event->lng = $request->lng;
                $event->url = $request->url;
                $event->description = $request->description;
                $event->image = $request->image;
                $event->user_created = Auth::user()->id;
                $event->capacity = $request->capacity;
                $event->price = $request->price;

                $event->save();

                $last_id = $event->id;

                //if update succes and is files
                if ($event && $request->hasFile('files') && $last_id > 0){

                    $image = new UploadImages();
                    $image->deleteImage($module, $last_id);
                    $image->procesImage($request->files, $module, $last_id, $request->title . '-' . $last_id );
                }

                $interval += $interval_days;
            }
        }

        return redirect()->route('events.listing.index')->with('message', 'Záznam č:' . $last_id .' úspešne vytvorený');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        view()->share('backend_title', 'Editácia udalosti'); //title

        $event_class = new Event();

        $event = Event::find($id);
        $cl = new Club();

        $users = $cl->getUsersFromClub($event->club_id);

        $clubs = Club::orderBy('active','DESC')->orderBy('short_title')->get();

        $event_types = EventTypes::all();
        $event_activities = DB::table('events_activities_list')->get();

        //stavy v prezencnej listinde
        $events_guest_status = DB::table('events_guest_status')->get();

        $activities = DB::table('events_activities AS ea')
            ->select('ea.id AS id', 'ea.description AS description', 'u.id AS user_id',
                        DB::raw( 'CONCAT(u.name, " ", u.surname) AS full_name'),
                        'eal.name as activity_name', 'eal.id AS activity_id')
            ->join('users AS u', 'u.id', '=', 'ea.user_id')
            ->join( 'events_activities_list AS eal', 'eal.id', 'ea.activity_id' )
            ->where('ea.event_id', $id )
            ->orderByDesc('id')
            ->get();

        //zoznam pozvanych hosti
        $attendance = $event_class->getEventAttendanceList($id);

        $tickets = DB::table('reference_coupons as rc')
            ->where('rc.reference_id', $id)
            ->select('uf.name as from_name', 'uf.surname as from_surname', 'ut.name as to_name', 'ut.surname as to_surname',
                'rc.id', 'rc.reference_type', 'rc.date as date', 'rc.price AS value_1', 'rl.name as ref_name', 'rc.description', 'rc.*')
            ->join('references_list AS rl','rc.reference_type','=','rl.id')
            ->join('users  as uf', 'rc.user_from', '=', 'uf.id', 'LEFT')
            ->join('users  as ut', 'rc.user_to', '=', 'ut.id', 'LEFT')
            ->orderByDesc('rc.id')
            ->get();

        $images = DB::table('events_images')->where('event_id', intval($id))->orderByDesc('id')->get();

        return view('events.detail')
            ->with('events_guest_status' , $events_guest_status)
            ->with('activities', $activities)
            ->with('attendance', $attendance)
            ->with('tickets', $tickets)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('event_activities', $event_activities)
            ->with('users', $users)
            ->with('images', $images)
            ->with('event', $event);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {

        view()->share('backend_title', 'Vytvorenie kópie udalosti'); //title

        $event = Event::find($id);
        $clubs = Club::orderBy('active','DESC')->orderBy('short_title')->get();
        $countries = DB::table('countries')->get();
        $counties = DB::table('counties')->get();
        $districts = DB::table('districts')->get();
        $event_types = EventTypes::all();

        return view('events.duplicate')
            ->with('counties', $counties)
            ->with('districts', $districts)
            ->with('countries', $countries)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('event', $event);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $file_classs = new UploadFiles();
        $files = null;
        $data_encode = null;
        $role = null;

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať novú udalosť', 'link' => route('events.listing.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('events.listing.index'), 'icon' => 'list', 'class' => 'btn-success'],
                ['name' => 'Detail', 'link' => route('events.listing.show', ['id' => $id ]), 'icon' => 'search', 'class' => 'btn-info'],
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Editácia udalosti'); //title

        $event = Event::find($id);
        $clubs = Club::orderBy('active','DESC')->orderBy('short_title')->get();
        $countries = DB::table('countries')->get();
        $counties = DB::table('counties')->get();
        $districts = DB::table('districts')->get();
        $event_types = EventTypes::all();

        $files_raw = DB::table('files')
            ->where('module', 'event-attach-files')->where('module_id', $id)->get();

        if ($files_raw){
            foreach ($files_raw as $k => $file){

                $files[$k] = $file;
                $files[$k]->icon =  $file_classs->getIconMimeText($file->ext);

                //odstranime hash v nazve suboru
                $file_arr = explode('-',$file->file);
                $files[$k]->short_file_name =  str_replace("-" . $file_arr[count($file_arr) - 1], "", $file->file) . "." . $file->ext;


                //timestamp
                $data_encode =  [ time(), $file->module_id,$file->id ];
                $files[$k]->download =  $this->hashid->encode($data_encode);

                if($file->role){
                    foreach (json_decode($file->role) as $r) {
                        $role[] = "$r";
                    }
                }

                $files[$k]->role_string =  $role;

            }
        }

        return view('events.edit')
            ->with('counties', $counties)
            ->with('districts', $districts)
            ->with('countries', $countries)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('files', $files)
            ->with('event', $event);

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

        $module = 'event';

        $rules = [
            'title' => 'required',
            'district_id' => 'required',
            'county_id' => 'required',
            'club_id' => 'required',
            'host_name' => 'required',
            'date_create' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'address_street' => 'required',
            'address_psc' => 'required',
            'address_city' => 'required',
            'address_country' => 'required',
            'url' => 'required|url',
            'lat' => 'required',
            'lng' => 'required',
        ];

        $this->validate($request,$rules);

        $event = Event::find($id);

        $event->title = $request->title;
        $event->district_id = $request->district_id;
        $event->county_id = $request->county_id;
        $event->club_id = $request->club_id;
        $event->host_name = $request->host_name;
        $event->description = $request->description;

        $date_create = Carbon::createFromFormat('d.m.Y', $request->date_create)->format('Y-m-d');

        $event->event_from = Carbon::createFromFormat('Y-m-d H:i', $date_create . ' ' . $request->time_from)->format('Y-m-d H:i:s');
        $event->event_to = Carbon::createFromFormat('Y-m-d H:i', $date_create . ' ' . $request->time_to)->format('Y-m-d H:i:s');
        $event->event_type = $request->event_type;

        $event->address_street = $request->address_street;
        $event->address_psc = $request->address_psc;
        $event->address_city = $request->address_city;
        $event->address_country = $request->address_country;
        $event->address_description = $request->address_description;

        $event->email_image_club = $request->email_image_club;
        $event->email_text_custom = $request->email_text_custom;

        $event->url = $request->url;
        $event->lat = $request->lat;
        $event->lng = $request->lng;
        // ak je neaktivny alebo akivny ale nie uzatvoreny
        if($event->active < 2){
            $event->active = $request->active;
        }
        $event->capacity = $request->capacity;
        $event->price = $request->price;

        $event->save();

        //if update succes and is files
        if ($event && $request->hasFile('files')){

            $image = new UploadImages();
            $image->deleteImage($module, $id);
            $image->procesImage( array($request->files->get('files')) , $module, $event->id, $event->title . '-' . $event->id );
        }


        $module = 'event_attach_files';
        $roles = json_encode(array('all'));

        //if update succes and is files
        if ($request->hasFile('attach_files')){

            $files_to_upload['files'] = $request->attach_files;

            $file_classs = new UploadFiles();

            //$res = $file_classs->procesUploadFiles(collect($files_to_upload), $module, $id, 'event_file_' . $request->title . '_' . $id);
            $res = $file_classs->procesUploadFiles(collect($files_to_upload), $module, $id, '');

            if($res){
                // nastavime prava pre subory
                foreach ($res as $r){
                    DB::table('files')->where('id', $r)->update( ['role' => $roles ]);
                }

            }

        }

        return redirect()->route('events.listing.index')->with('message', 'Záznam úspešne upravený');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::destroy($id);
        return redirect()->route('events.listing.index')->with('message', 'Záznam úspešne vymazaný');
    }


    // zobrazenie nahladu email
    public function invitingPrewiew($id)
    {

        $event_class = new Event();
        $content['preview'] = true;

        $event_detail = Event::find($id);
        $content['club'] = Club::find($event_detail->club_id);

        $content['event'] = $event_detail;
        $content['recipient'] = Auth::user();
        $content['invite_person'] = Auth::user();

        // aktivyty eventu vzdelavaci bod alebo zb
        $activity_list = $event_class->getEventActivity($id);

        if (count($activity_list) > 0 && count($activity_list) > 0 ){
            $content['activity'] = $activity_list;
        } else {
            $content['activity'] = null;
        }

        $content['link_accept'] = null;
        $content['link_apology'] = null;
        $content['link_refused'] = null;
        $content['link_invite_guest'] = null;
        $content['link_info_detail'] = null;

        $content['invitation_type']  = 1;
        $content['signature'] = Auth::user();

        if(strlen($content['club']->image) > 0){
            $content['image_club'] = asset('images/club'). '/' . $content['club']->id .'/'. $content['club']->image;
        } else {
            $content['image_club'] = asset('images/event-type/1/'). '/large/ranny-klub-1-476799231.png';
        }

        if(strlen($content['event']->image) > 0){
            $content['image_event'] = asset('images/event'). '/' . $content['event']->id .'/'. $content['event']->image;
        } else {
            $content['image_event'] = asset('images/event-type/1/'). '/large/ranny-klub-1-476799231.png';
        }


        return view('email.html.invitation')
            ->with('content', $content);


    }



    public function activityStore(Request $request)
    {

        $data['user_id'] = $request->user_id;
        $data['event_id'] = $request->event_id;
        $data['club_id'] = $request->club_id;
        $data['activity_id'] = $request->activity_id;
        $data['user_id_create'] = Auth::user()->id;
        $data['description'] = $request->description;

        DB::table('events_activities')->updateOrInsert(['id' => intval($request->activity_id_hidden) ], $data);

        return redirect()->route('events.listing.show', ['id' =>  $request->event_id] )->with('message', 'Záznam úspešne pridaný');
    }



    public function activityDestroy($id)
    {
        $event_id = DB::table('events_activities')->where('id',$id)->value('event_id');
        DB::table('events_activities')->delete($id);

        return redirect()->route('events.listing.show', ['id' =>  $event_id] )->with('message', 'Záznam úspešne zmazaný');

    }


    public function attendanceDestroy($id)
    {

        $event_id = DB::table('attendance_lists')->where('id',$id)->value('event_id');
        DB::table('attendance_lists')->delete($id);

        return redirect()->route('events.listing.show', ['id' =>  $event_id] )->with('message', 'Záznam úspešne zmazaný');

    }



}
