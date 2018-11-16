<?php

namespace App\Http\Controllers\External;

use App\Models\Event\Event;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventTicketBuyController extends Controller
{


    public function __construct()
    {
        $this->hashid = new Hashids(env('HASH_SALT'), 10);
    }


    //zobrazenie formulara pre kupu listku k eventu
    public function showFormBuyTicket($event_hash)
    {

        $event_hash_decode = $this->hashid->decode($event_hash);

        if($event_hash_decode){

            $event_detail = Event::find($event_hash_decode[0]);  // [0] id eventu
            $recipient = User::find($event_hash_decode[1]);  // [1] prijemca pozvanky
            $invite_person = User::find($event_hash_decode[2]);  // [2] pozyvajuci clen

            $data['price'] = 0;

            // zistime ci sa da kupit vstupenka online
            $ticket_setting = DB::table('events_ticket_setup')
                ->where('event_id', $event_detail->id)->orderByDesc('date_before_event')->get();

            if ($recipient->admin == 1){

                $ticket_price_member = $ticket_setting->where('member', 1)
                    ->where('date_before_event', '>', Carbon::now())
                    ->sortByDesc('date_before_event')->first();

                if ($ticket_price_member){
                    $data['price'] = $ticket_price_member->price;
                }

            }else{

                $ticket_price_member = $ticket_setting->where('not_member', 1)
                    ->where('date_before_event', '>', Carbon::now())
                    ->sortByDesc('date_before_event')->first();

                if ($ticket_price_member){
                    $data['price'] = $ticket_price_member->price;
                }

            }

            dump($ticket_setting);


            // zistime ci je clen/host

            // ci je mozne kupit  listky pre clena aj hosta


            // zistime ci su rozdielne ceny v ramci terminu



            dump($data);

//            dump($event_detail);
//            dump($recipient);
//            dump($invite_person);


            dump($event_hash_decode);

            return view('external.ticket.event_buy_ticket_step1')
                ->with('event', $event_detail)
                ->with('data', $data)
                ->with('recipient', $recipient)
                ->with('invite_person', $invite_person);

        } else {

            dd('chyba');

        }




    }
    
    
}
