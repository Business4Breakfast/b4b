<?php

namespace App\Http\Controllers\External;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Invitation\Invitation;
use App\Models\Setting\Industry;
use App\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvitationGuestController extends Controller
{

    private $data;
    private $invitation_class;
    private $hashid;

    public function __construct()
    {
        $this->hashid = new Hashids('salt', 10);
        $this->invitation_class = new Invitation();
    }

    // zobrazenie formularu email a phones
    public function inviteGuestStep1($id)
    {

       // overime ci existuje clen
       $user = User::where(['token_id' => $id, 'admin' => 1])->first();
       $industry = Industry::orderBy('name')->get();

       if ($user){

           $event = Event::where('club_id', $user->club)->get();

           $club_class = new Club();
           //kluby v ktorych ma clen aktivne clenstva
           $clubs = $club_class->getClubsFromUsers($user->id);

           $events = DB::table('events AS e')
               ->select('e.event_from', 'c.title AS club', 'e.title AS event', 'e.id')
               ->join('clubs AS c', 'e.club_id', '=', 'c.id')
               ->whereIn('club_id', $clubs->pluck('id'))
               ->where('e.event_from', '>', Carbon::now())
               ->where('e.active', 1)
               ->orderBy('e.event_from')
               ->get();

           return view('external.member_invitation_step1')
               ->with('user', $user)
               ->with( 'events', $events)
               ->with('industry', $industry);

       } else {

           // ak user neexistuje alebo je adresa poskodena
           //dd('zaujem o ranajky vseobecny');
           return redirect()->to('//www.bforb.sk/chcem-prist-na-ranajky');

       }

   }



    //spracovanie zakladneho formulara step1
    public function inviteGuestStoreStep1(Request $request)
    {

        $users = null;
        $send = 0;
        $res = null;
        $users_exist = null;

        $club_class = new Club();
        $user_class = new User();
        $event_class = new  Event();


        $rules = [
            'phone' => 'required',
            'email' => 'required|email',
            'event' => 'required',
            'user_id' => 'required',
            'invite_person' => 'required',
        ];

        $this->validate($request,$rules);

        $event = Event::find($request->event);
        $user = User::find($request->user_id);

        $industry = Industry::orderBy('name')->get();


        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        //typ zistime ci existuje email a phone
        if(strcmp($request->form_type, "guest_new_step_1") == 0){

            $res = collect( [   'email' => $request->email,
                                'phone' => $request->phone,
                                'order_token' =>  $request->order_token ]);

            //overime email ci existuje
            $email = $user_class->getBasicUserDataFromEmail($request->email);

            if (count($email) > 0){
                $users_exist  = $email;
            }

            //overime ci je zhoda v telephone
            $phone = $user_class->getBasicUserDataFromPhone($request->phone);

            if (count($phone) > 0){
                if ($users_exist) {
                    $users_exist = $users_exist->merge($phone);
                } else {
                    $users_exist = $phone;
                }
            }

            //ak existuju duplicity zobrazime form
            if (!is_null($users_exist)){

                // vyfiltrujeme aby sa neopakovalo id
                $users_exist = $users_exist->unique('id');
                $users_exist->values()->all();

                $raw_users_exist = null;
                if(count($users_exist) > 0){
                    foreach ($users_exist as $k =>$ue){

                        $raw_users_exist[$k] = $ue;
                        $raw_users_exist[$k]->attend = null;
                        if ($attendance) {
                            foreach ($attendance as $a) {
                                if ($a->user_id == $ue->id) {
                                    $raw_users_exist[$k]->attend = $a->user_id;
                                }
                            }
                        }

                    }
                }

                return  view('external.member_invitation_step2')
                    ->with('res', $res)
                    ->with('user', $user)
                    ->with('attendance', $attendance)
                    ->with('users_exist', $raw_users_exist)
                    ->with( 'event', $event);


            }else{

            // zobrazime doplnujuci formulkar
                return  view('external.member_invitation_step22')
                    ->with('res', $res)
                    ->with('user', $user)
                    ->with( 'event', $event)
                    ->with('industry', $industry);

            }

        }

    }



    //spracovanie formulara existujuceho hosta (usera)
    public function inviteGuestStoreStep2(Request $request)
    {

        $user = User::find($request->invitation_to);

        if(isset($request->invitation_to) && $request->invitation_to){

            //pozvanka pre clena klubu = 2, pre hosta 1
            $invitation_type = 1;
            $invitation_status = 1;

            // ak ma host status 6 (nema zaujem) zmenime na statusd novy 1
            if ($user->status  == 6)  {
                $user->status = 1;
                $user->save();
            }

            // vlozime do prezencky udalosti
            $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event_id, $request->invitation_to, $request->user_id, $request->description, $invitation_status, $invitation_type);

            if($attendance_id > 0){
                // odosleme email
                $this->invitation_class->addNotificationInvitationToCue($request->invitation_to, $request->user_id, $request->event_id, $attendance_id, $invitation_type );

                $text['title'] = "Hosť úspešne pozvaný";
                $text['bg_jumbotron_'] = "success";

            }else{

                $text['title'] = "Duplicitné pozývanie nie je možné";
                $text['bg_jumbotron_'] = "success";

            }

            return view('external.guest_invitation_response')
                ->with('text', $text)
                ->with('user', $user);


        }else{

            return redirect()->back();

        }

    }



    //spracovanie formulara noveho hosta (usera)
    public function inviteGuestStoreStep22(Request $request)
    {

        $users = null;
        $send = 0;
        $res = null;
        $text = null;

        //pozvanka pre clena klubu = 2, pre hosta 1
        $invitation_type = 1;
        $invitation_status = 1;

        $club_class = new Club();
        $user_class = new User();
        $event_class = new  Event();

        $event = Event::find($request->event_id);
        $user = User::find($request->user_id);

        $industry = Industry::orderBy('name')->get();

        $rules = [
            'phone' => 'required',
            'email' => 'required|email',
            'event_id' => 'required',
            'user_id' => 'required',
            'invite_person' => 'required',
            'surname' => 'required',
            'name' => 'required',
            'internet' => 'required',
            'company' => 'required',
        ];

        $this->validate($request,$rules);

        //overime meno a priezvisko
        $name = $user_class->getBasicUserDataFromName($request->name, $request->surname);

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        // ak ano otvorime formular kde je mozne vybrat existujuceho
        if (count($name)>0){

            // posleme info do formulara
            $res = $request->all();
            $users_exist  = $name;

            // vyfiltrujeme aby sa neopakovalo id
            $users_exist = $users_exist->unique('id');
            $users_exist->values()->all();

            $raw_users_exist = null;
            if(count($users_exist) > 0){
                foreach ($users_exist as $k =>$ue){
                    $raw_users_exist[$k] = $ue;
                    $raw_users_exist[$k]->attend = null;
                    if($attendance){
                        foreach ($attendance as $a){
                            if($a->user_id == $ue->id){
                                $raw_users_exist[$k]->attend = $a->user_id;
                            }
                        }
                    }

                }
            }

            return  view('external.member_invitation_step3')
                ->with('res', $res)
                ->with('users_exist', $raw_users_exist)
                ->with('user', $user)
                ->with( 'event', $event)
                ->with('industry', $industry);

        } else {

            //vytvorime noveho
            $new_user_id = $user_class->addNewGuestToDb($request, $event->club_id);

            if($new_user_id > 0){

                // vlozime do prezencky udalosti
                $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event_id, $new_user_id, $request->user_id, $request->description, $invitation_status, $invitation_type);

                if($attendance_id > 0){
                    // odosleme email
                    $this->invitation_class->addNotificationInvitationToCue($new_user_id, $request->user_id, $request->event_id, $attendance_id, $invitation_type );

                    $text['title'] = "Hosť úspešne pozvaný";
                    $text['bg_jumbotron_'] = "success";

                }

                return view('external.guest_invitation_response')
                    ->with('text', $text)
                    ->with('user', $user);

            }

        }

    }




    //spracovanie formulara noveho hosta ak existuje zhoda v mene
    public function inviteGuestStoreStep3(Request $request)
    {

        $user = null;
        $send = 0;
        $res = null;
        $text = null;

        $club_class = new Club();
        $user_class = new User();
        $event_class = new  Event();

        //pozvanka pre clena klubu = 2, pre hosta 1
        $invitation_type = 1;
        $invitation_status = 1;

        $event = Event::find($request->event_id);
        $user = User::find($request->user_id);

        $industry = Industry::orderBy('name')->get();


        // ak je vybrany exist uzivatel
        if(isset($request->invitation_to) && $request->invitation_to > 0){

            $user = User::find($request->invitation_to);

            // vlozime do prezencky udalosti
            $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event_id, $request->invitation_to, $request->user_id, $request->description, $invitation_status, $invitation_type);

            if($attendance_id > 0){
                // odosleme email
                $this->invitation_class->addNotificationInvitationToCue($request->invitation_to, $request->user_id, $request->event_id, $attendance_id, $invitation_type );

                $text['title'] = "Hosť úspešne pozvaný";
                $text['bg_jumbotron_'] = "success";

            }

            return view('external.guest_invitation_response')
                ->with('text', $text)
                ->with('user', $user);

        } else {

            //vytvorime noveho
            $new_user_id = $user_class->addNewGuestToDb($request, $event->club_id);

            if($new_user_id > 0){

                // vlozime do prezencky udalosti
                $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event_id, $new_user_id, $request->user_id, $request->description, $invitation_status,  $invitation_type);

                if($attendance_id > 0){
                    // odosleme email
                    $this->invitation_class->addNotificationInvitationToCue($new_user_id, $request->user_id, $request->event_id, $attendance_id, $invitation_type );

                    $text['title'] = "Hosť úspešne pozvaný";
                    $text['bg_jumbotron_'] = "success";

                }

                return view('external.guest_invitation_response')
                    ->with('text', $text)
                    ->with('user', $user);

            }

        }

    }


}
