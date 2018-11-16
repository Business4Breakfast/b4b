<?php

namespace App\Http\Controllers\Events;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Event\EventTypes;
use App\Models\Notification\EmailNotification;
use App\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventBalanceController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $hashids = new Hashids(env('HASH_SALT'), 10);

        $user_class = new User();
        $event_class = new Event();
        $email_class = new EmailNotification();

        if ($request->event_id){

            $event = Event::find( $request->event_id);

            if ($request->user){

                $users = User::whereIn('id', $request->user)->get();
                $statuses = DB::table('user_status')->get()->toArray();

                $attendance = $event_class->getEventAttendanceList($request->event_id);

                $status_change = 0;

                // ak event meni status hosta
                if($event->type->change_status_guest == 1){

                    // ak je novy = 1 meni sa na 2 prisiel 1x,
                    // ak je 2 mení sa na 3 2xprisiel
                    // ak je 3 ostava 3
                    foreach ($users as $u){
                        $status_change = 0;
                        //povodny staus
                        $user_old_status = $u->status;

                        if($u->status == 1) {
                            $u->status = 2;
                            $status_change = $res = $u->save();

                        }elseif ($u->status == 2) {
                            $u->status = 3;
                            $status_change = $u->save();
                        }

                        //ak sa menil status
                        if( $status_change == 1){

                            $desc = $statuses[array_search( $user_old_status, array_column($statuses, 'id'))]->status;
                            $desc .= ' -> '. $statuses[array_search( $u->status, array_column($statuses, 'id'))]->status;

                            $user_class->addUserUpdateStatus($u->id, $user_old_status, $u->status, 'event_balance', $request->event_id,
                                'Zmena stavu pri uzavierke udalosti' . ',  ' . $desc);
                        }

                    }

                }



                // zaktualizujeme kto prisiel do tbl attendance_list
                if ($attendance){
                    foreach ($attendance as $a){

                        //$user_attend = null;
                        foreach ($users as $u){
                            if($u->id == $a->user_id){

                                //odosleme notifikaciu
                                $email_balance = [$u->email];

                                // hash zakodovane id eventu a id prijemca a kto pozval
                                $hash_ticket = $hashids->encode(
                                    $a->user_id,  // id usera komu je pozvanka
                                    $a->user_invite_id ); //kto pozval

                                $url = route('ext.application.form.member', [ $hash_ticket ] );

                                $content['subject'] = 'Poďakovanie k udalosti '. $a->event_title;
                                $content['url'] = $url;
                                $content['event_id'] =  $event->id;
                                $content['attendance_id'] = $a->attend_id;
                                $content['id'] = $a->attend_id;
                                $content['user_status_id'] = $a->user_status_id;
                                $content['invitation_type'] = $a->invitation_type;

                                $user_gender = ($a->gender == 'M') ? __('email.invoice_user_gender_man') : __('email.invoice_user_gender_female');
                                $content['user_to_send'] = $user_gender . ' ' . $a->name . ' ' .  $a->surname;

                                $text = 'Poďakovanie k udalosti ' . $a->event_title . "\n\n";
                                $content['text'] = $text;

                                $email_class->addNotificationThankYou($email_balance , $content);

                                //aktualizujeme v db
                                $user_attend[] = $a->id;
                                DB::table('attendance_lists')
                                    ->where('id', $a->attend_id)
                                    ->update(['user_attend' => 1]);
                            }
                        }
                    }
                }


            }

            // vykoname uzavierku a nastavime status
            $event->active = 2;
            $res = $event->save();
            if ($res){
                return redirect()->back()->with('message', 'Uzávierka prebehla v úspešne');
            }

        }

        return redirect()->route('events.listing.show', $event->id)->with('message', 'Uzávierka nebola úspešná');

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        view()->share('backend_title', 'Uzávierka prezenčnej listiny'); //title

        $event_class = new Event();

        $event = Event::find($id);

        $clubs = Club::orderBy('active','DESC')->orderBy('short_title')->get();

        $event_types = EventTypes::all();

        //stavy v prezencnej listinde
        $events_guest_status = DB::table('events_guest_status')->get();

        $attendance = $event_class->getAttendanceGroupUserType($id);

        return view('events.balance_index')
            ->with('events_guest_status' , $events_guest_status)
            ->with('attendance', $attendance)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('event', $event);

    }


}
