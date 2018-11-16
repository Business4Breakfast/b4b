<?php

namespace App\Http\Controllers\Events;

use App\Models\Event\Event;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventTicketSettingController extends Controller
{


    public function __construct()
    {

        $this->hashid = new Hashids('salt', 10);

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
            'price_dph' => 'numeric',
            'price_w_dph' => 'numeric',
            'date_pay_to' => 'required',
            'member' => 'required_without_all:not_member',
            'not_member' => 'required_without_all:member',
        ];

        $message = [
            'member' => 'Aspoň jeden typ lístku musí bť zaškrtnutý.',
            'not_member' => 'Aspoň jeden typ lístku musí bť zaškrtnutý.',
        ];

        $this->validate($request,$rules, $message);

        $data = null;

        $event = Event::find($request->event_id);

        $data['event_id'] = intval($event->id);
        $data['member'] = intval($request->member);
        $data['not_member'] = intval($request->not_member);
        $data['price'] = $request->price;
        $data['price_dph'] = $request->price_dph;
        $data['price_w_dph'] = $request->price_w_dph;
        $data['description'] = $request->description;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['date_before_event'] = Carbon::createFromFormat('d.m.Y',$request->date_pay_to)->format('Y-m-d 23:59:59');
//
        DB::table('events_ticket_setup')->insert($data);

        return redirect()->route('events.ticket-setting.show', $event->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);


        $event_hash = $this->hashid->encode( $id ); // id eventu );

        dump($event_hash);
        dump($this->hashid->decode($event_hash));

        if ($event){

            $ticket_setting = DB::table('events_ticket_setup')
                ->where('event_id', $event->id)
                ->orderByDesc('date_before_event')
                ->get();

        }

        return view('events.ticket.index')
            ->with('items', $ticket_setting)
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

        $ticket_setup = DB::table('events_ticket_setup')->where('id', $id)->first();

        if ($ticket_setup){
            $event = Event::find($ticket_setup->event_id);
        }

        return view('events.ticket.edit')
            ->with('ticket_setup', $ticket_setup)
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

        $rules = [
            'event_id' => 'required',
            'price' => 'numeric',
            'price_dph' => 'numeric',
            'price_w_dph' => 'numeric',
            'date_pay_to' => 'required',
            'member' => 'required_without_all:not_member',
            'not_member' => 'required_without_all:member',
        ];


        $this->validate($request,$rules);

        $data = null;

        $event = Event::find($request->event_id);

        $data['event_id'] = intval($event->id);
        $data['member'] = intval($request->member);
        $data['not_member'] = intval($request->not_member);
        $data['price'] = $request->price;
        $data['price_dph'] = $request->price_dph;
        $data['price_w_dph'] = $request->price_w_dph;
        $data['description'] = $request->description;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $data['date_before_event'] = Carbon::createFromFormat('d.m.Y',$request->date_pay_to)->format('Y-m-d 23:59:59');
//
        DB::table('events_ticket_setup')->where('id', $id)->update($data);

        return redirect()->route('events.ticket-setting.show', $data['event_id']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $ticket_setup = DB::table('events_ticket_setup')->where('id', $id)->first();


        if ($ticket_setup){

            $event = Event::find($ticket_setup->event_id);

            DB::table('events_ticket_setup')->delete($id);

        }

        return redirect()->route('events.ticket-setting.show', $event->id);

    }
}
