<?php

namespace App\Http\Controllers\Events;

use App\Models\Event\Event;
use Carbon\Carbon;
use function GuzzleHttp\Promise\iter_for;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'event_id' => 'required',
            'price' => 'numeric',
            'reference_type' => 'required',
            'user_from' => 'required',
            'user_to' => 'required',
        ];

        $this->validate($request,$rules);

        $data = null;
        $event = Event::find($request->event_id);

        $data['user_from'] = $request->user_from;
        $data['user_to'] = $request->user_to;
        $data['description'] = $request->description;
        $data['price'] = $request->price;
        $data['reference_type'] = $request->reference_type;
        $data['active'] = $request->reference_type;
        $data['club_id'] = $event->club_id;
        $data['event_id'] = $request->event_id;
        $data['reference_id'] = $request->event_id;
        $data['reference_cackon_id'] = 0;
        $data['user_to_cackon_id'] = 0;
        $data['user_from_export'] = 0;
        $data['user_to_export'] = 0;
        $data['user_from_cackon_id'] = 0;
        $data['club_cackon_id'] = 0;
        $data['zb'] = 0;
        $data['vb'] = 0;

        $data['date'] = Carbon::createFromFormat('Y-m-d H:i:s',$event->event_from);

        DB::table('reference_coupons')->insert($data);

        return redirect()->route('events.reference.show', $request->event_id);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $event_class = new Event();

        $event = Event::find($id);

        $attendance = $event_class->getEventUserAttendBalance($id, 1);

        $reference_type = DB::table('references_list')->where('active',1)->get();

        view()->share('backend_title', 'Referenčné ústrižky raňajok'); //title

        $items = DB::table('reference_coupons as rc')
            ->where('rc.reference_id', $id)
            ->select('uf.name as from_name', 'uf.surname as from_surname', 'ut.name as to_name', 'ut.surname as to_surname',
                'rc.id', 'rc.reference_type', 'rc.date as date', 'rc.price', 'rl.name as ref_name',
                'rc.description', 'rc.*' )
            ->join('references_list AS rl','rc.reference_type','=','rl.id')
            ->join('users  as uf', 'rc.user_from', '=', 'uf.id', 'LEFT')
            ->join('users  as ut', 'rc.user_to', '=', 'ut.id', 'LEFT')
            ->orderByDesc('rc.id')
            ->get();

        return view('events.reference_index')
            ->with('attendance', $attendance->sortBy('surname')
//                ->where('status_id', 2)
            )
            ->with('reference_type', $reference_type)
            ->with('event', $event)
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

        $event_class = new Event();

        $coupon = DB::table('reference_coupons')->find( $id);

        if($coupon){

            view()->share('backend_title', 'Referenčné ústrižky raňajok editácia'); //title

            $event = Event::find($coupon->event_id);

            $attendance = $event_class->getEventAttendanceList($event->id);
            $reference_type = DB::table('references_list')->where('active',1)->get();

            return view('events.reference_edit')
                ->with('attendance', $attendance->sortBy('surname')->where('status_id', 2))
                ->with('reference_type', $reference_type)
                ->with('event', $event)
                ->with('coupon', $coupon);

        }

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
            'event_id' => 'required',
            'price' => 'numeric',
            'reference_type' => 'required',
            'user_from' => 'required',
            'user_to' => 'required',
        ];

        $this->validate($request,$rules);

        $data = null;
        $event = Event::find($request->event_id);

        $data['user_from'] = $request->user_from;
        $data['user_to'] = $request->user_to;
        $data['description'] = $request->description;
        $data['price'] = $request->price;
        $data['reference_type'] = $request->reference_type;

        DB::table('reference_coupons')->where('id', $id)->update($data);

        return redirect()->route('events.reference.show', $request->event_id)->with('message', 'Záznam upravený.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
