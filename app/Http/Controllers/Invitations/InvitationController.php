<?php

namespace App\Http\Controllers\Invitations;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Invitation\Invitation;
use App\Models\Setting\Industry;
use App\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{

    private $data;
    private $invitation_class;
    private $hashid;
    private $debug_send_email_switch;

    public function __construct()
    {

        //debug na neodosielanie emailov
        $this->debug_send_email_switch = true;

        $this->hashid = new Hashids('salt', 10);
        $this->invitation_class = new Invitation();

    }



    // nacitame novy formular emai a telefon
    public function invitationEventGuestNew($event_id)
    {

        $res = null;

        $club_class = new Club();
        $event_class = new Event();

        $event = Event::find($event_id);

        $industry = Industry::orderBy('name')->get();


        $clubs = $club_class->orderByDesc('active')->orderBy('short_title')->get();

        // uzivatelia klubu ktory organizuje event
        $club_members = $club_class->getUsersFromClub($event->club_id);

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        return view('invitation.event_guest_new')
            ->with('res', $res)
            ->with('clubs', $clubs)
            ->with('industry', $industry)
            ->with('attendance', $attendance)
            ->with('event', $event);

    }






    //invitation new guest
    public function invitationEventGuestNewStore(Request $request)
    {

        $users = null;
        $send = 0;
        $res = null;
        $users_exist = null;

        $club_class = new Club();
        $user_class = new User();
        $event_class = new  Event();

        $event = Event::find($request->event_id);

        $industry = Industry::orderBy('name')->get();
        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        //id user club manager
        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        // status nepotrvdeny
        $status_invite = 1;
        // pozvanka pre hosta
        $invitation_type = 1;

        //typ zistime ci existuje email a phone
        if(strcmp($request->form_type, "guest_new_step_1") == 0){
            $rules = [
                'phone' => 'required',
                'email' => 'required|email'
            ];

            $this->validate($request,$rules);
            $res = collect( ['email' => $request->email, 'phone' => $request->phone ]);

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

            $users_exist = $users_exist->unique('id');
            $users_exist->values()->all();

                return  view('invitation.event_guest_new_2')
                    ->with('res', $res)
                    ->with('attendance', $attendance)
                    ->with('users_exist', $users_exist)
                    ->with( 'event', $event)
                    ->with('industry', $industry);

            // zobrazime doplnujuci formulkar
            }else{

                return  view('invitation.event_guest_new_3')
                    ->with('res', $res)
                    ->with('users_exist', $users_exist)
                    ->with( 'event', $event)
                    ->with('industry', $industry);

            }

        }

    }






    //invitation new guest
    public function invitationEventGuestNewStore2(Request $request)
    {

        $users = null;
        $send = 0;
        $res = null;
        $users_exist = null;


        $club_class = new Club();
        $user_class = new User();

        $event = Event::find($request->event_id);

        $industry = Industry::orderBy('name')->get();

        //id user club manager
        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        //ak pozyvamehosti status nepotvrdeny
        $status_invite = 1;
        // ak je po termine
        if( Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) < Carbon::createFromFormat('Y-m-d H:i:s', now()) ){
            // ak prihlasujeme do prezencky bez pozvanky nastavime status potvrdeny
            $status_invite = 2;
        }

        //pozvanka pre hosta 1
        $invitation_type = 1;


        if(strcmp($request->form_type, "guest_new_step_2") == 0) {

            $rules = [
                'phone' => 'required',
                'email' => 'required|email',
                'guest' => 'required'
            ];

            $this->validate($request, $rules);

            //v mene koho pozyvame
            if(strcmp($request->invite_person, 'personely') == 0) {
                $club_manager = Auth::user()->id;
            }

            //ak pozyvamehosti status nepotvrdeny
            $status_invite = 1;
            // ak je po termine
            if( Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) < Carbon::createFromFormat('Y-m-d H:i:s', now()) ){
                // ak prihlasujeme do prezencky bez pozvanky nastavime status potvrdeny
                $status_invite = 2;
            }

            // pozvanka bez moznosti pozyvabia hosti
            $invitation_type = 1;

            $user_to = null;
            if ($request->guest) {
                $user_to = $request->guest;

                // vlozime do prezencky udalosti
                $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $user_to, $club_manager, "", $status_invite, $invitation_type);
                if ($attendance_id > 0) {
                    $send = $send + 1;
                    // odosleme email pre hosti bez moznosti pozyvania
                    if($this->debug_send_email_switch){
                        $this->invitation_class->addNotificationInvitationToCue($user_to, $club_manager, $event->id, $attendance_id, $invitation_type);
                    }
                }
            }

            $redirect = redirect()->route('invitations.event.guest-new', ['id' => $event->id ]);

            if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bola  odoslaná pozvánka pre existujuceho hosťa. ');

            return $redirect;

        }

    }






    //invitation new guest
    public function invitationEventGuestNewStore3(Request $request)
    {

        $users = null;
        $send = 0;
        $res = null;
        $users_exist = null;


        $club_class = new Club();
        $user_class = new User();

        $event = Event::find($request->event_id);

        $industry = Industry::orderBy('name')->get();

        //id user club manager
        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        //v mene koho pozyvame
        if(strcmp($request->invite_person, 'personely') == 0) {
            $club_manager = Auth::user()->id;
        }

        //ak pozyvamehosti status nepotvrdeny
        $status_invite = 1;
        // ak je po termine
        if( Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) < Carbon::createFromFormat('Y-m-d H:i:s', now()) ){
            // ak prihlasujeme do prezencky bez pozvanky nastavime status potvrdeny
            $status_invite = 2;
        }

        //pozvanka hosta = 1
        $invitation_type = 1;

        //po vyplneni overime ci neexistuje niekto stakym menom
        if(strcmp($request->form_type, "guest_new_step_3") == 0) {

            //overime meno a priezvisko
            $name = $user_class->getBasicUserDataFromName($request->name, $request->surname);

            // ak ano otvorime formular kde je mozne vybrat existujuceho
            if (count($name)>0){

                // posleme info do formulara
                $res = $request->all();
                $name_duplicity  = $name;

                return  view('invitation.event_guest_new_4')
                    ->with('name_duplicity', $name_duplicity )
                    ->with('res', $res)
                    ->with('users_exist', $users_exist)
                    ->with( 'event', $event)
                    ->with('industry', $industry);
            } else {

                //vytvorime noveho a pozveme ho
                $rules = [
                    'phone' => 'required',
                    'email' => 'required|email',
//                    'internet' => 'required',
                    'company' => 'required',
                    'industry' => 'required',
                    'name' => 'required',
                    'surname' => 'required',
                ];

                $this->validate($request, $rules);

                $user_to  = $user_class->addNewGuestToDb($request, $event->club_id);

                if($user_to > 0){

                    // vlozime do prezencky udalosti
                    $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $user_to, $club_manager, "", $status_invite, $invitation_type);
                    if ($attendance_id > 0) {
                        $send = $send + 1;
                        // odosleme email pre hosti bez moznosti pozyvania
                        if($this->debug_send_email_switch) {
                            $this->invitation_class->addNotificationInvitationToCue($user_to, $club_manager, $event->id, $attendance_id, $invitation_type);
                        }
                    }

                }

                $redirect = redirect()->route('invitations.event.guest-new', ['id' => $event->id ]);
                if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bola  odoslaná pozvánka novému hosťovi '
                    . $request->name . ' ' . $request->surname);
                return $redirect;

            }

        }

        //po odoslani zistime ci pozyvatel vybral exiszujuceho hosta s rovnakym menom aleb vytvori noveho hosta
        if(strcmp($request->form_type, "guest_new_step_4") == 0) {

            //ak je vybrana existujuca osoba
            $user_to = null;
            if ($request->guest) {
                $user_to = $request->guest;

                // vlozime do prezencky udalosti
                $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $user_to, $club_manager, "", $status_invite, $invitation_type);
                if ($attendance_id > 0) {
                    $send = $send + 1;
                    // odosleme email pre hosti bez moznosti pozyvania
                    if($this->debug_send_email_switch) {
                        $this->invitation_class->addNotificationInvitationToCue($user_to, $club_manager, $event->id, $attendance_id, $invitation_type);
                    }
                }

                $redirect = redirect()->route('invitations.event.guest-new', ['id' => $event->id ]);
                if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bola  odoslaná pozvánka existujúcemu hosťovi ');
                return $redirect;


            // ak nevyberieme podobne meno ale chceme vytvorit noveho hosta
            } else {

                $rules = [
                    'phone' => 'required',
                    'email' => 'required|email',
//                    'internet' => 'required',
                    'company' => 'required',
                    'industry' => 'required',
                    'name' => 'required',
                    'surname' => 'required',
                ];

                $this->validate($request, $rules);

                $user_to  = $user_class->addNewGuestToDb($request, $event->club_id);

                if($user_to > 0){

                    // vlozime do prezencky udalosti
                    $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $user_to, $club_manager, "", $status_invite, $invitation_type);
                    if ($attendance_id > 0) {
                        $send = $send + 1;
                        // odosleme email pre hosti bez moznosti pozyvania
                        if($this->debug_send_email_switch) {
                            $this->invitation_class->addNotificationInvitationToCue($user_to, $club_manager, $event->id, $attendance_id, $invitation_type);
                        }
                    }

                }

                $redirect = redirect()->route('invitations.event.guest-new', ['id' => $event->id ]);
                if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bola  odoslaná pozvánka novému hosťovi '
                                        . $request->name . ' ' . $request->surname);
                return $redirect;

            }

        }

    }



    //pre vlastných členov
    public function invitationEventGuestNew3($event_id)
    {

        $res = null;

        $club_class = new Club();
        $event_class = new Event();

        $event = Event::find($event_id);

        $industry = Industry::orderBy('name')->get();

        $name_duplicity = Input::get('name_duplicity');
        $res = old();

        $clubs = $club_class->orderByDesc('active')->orderBy('short_title')->get();

        // uzivatelia klubu ktory organizuje event
        $club_members = $club_class->getUsersFromClub($event->club_id);

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        return view('invitation.event_guest_new_4')
            ->with('name_duplicity', $name_duplicity )
            ->with('res', $res)
            ->with('clubs', $clubs)
            ->with('industry', $industry)
            ->with('attendance', $attendance)
            ->with('event', $event);
    }




    public function invitationEventGuest($event_id)
    {

        $club_class = new Club();
        $event_class = new Event();

        $event = Event::find($event_id);

        $clubs = $club_class->orderByDesc('active')->orderBy('short_title')->get();
        $user_members =         // uzivatelia klubu ktory organizuje event
        $club_members = $club_class->getUsersFromClub($event->club_id);

        // nastavime do ssession klub podla eventu
        if(!Session::get('invitation_guest_filter_club') > 0){

            Session::put('invitation_guest_filter_club', $event->club_id);
        }


        $user_statuses = DB::table('user_status')->where('internal', 1)->get();

        if(Input::get('reset') == true){
            //vymazeme meno so session
            Session::forget('invitation_guest_filter_user');
            Session::forget('invitation_guest_filter_status');
            Session::forget('invitation_guest_filter_member');
            Session::forget('invitation_guest_filter_user_invite');
        }

        // hostia ktory patria do klubu
        //okrem stavov
        $exclude_statuses = [5];
        $club_guests = $club_class->getGuestsFromClub(
            Session::get('invitation_guest_filter_club', $event->club_id),
            Session::get('invitation_guest_filter_status', null), $exclude_statuses,
            Session::get('invitation_guest_filter_user', null),
            Session::get('invitation_guest_filter_member', null),
            Session::get('invitation_guest_filter_user_invite', null)
        );

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        return view('invitation.event_guest')
            ->with('user_statuses', $user_statuses)
            ->with('user_members', $user_members)
            ->with('clubs', $clubs)
            ->with('club_guests', $club_guests)
            ->with('attendance', $attendance)
            ->with('event', $event);
    }



    //pre vlastných členov
    public function invitationEventMember($event_id)
    {

        $club_class = new Club();
        $event_class = new Event();

        $event = Event::find($event_id);

        $clubs = $club_class->orderByDesc('active')->orderBy('short_title')->get();

        // uzivatelia klubu ktory organizuje event
        $club_members = $club_class->getUsersFromClub($event->club_id);

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);

        return view('invitation.event_member')
            //->with('user_statuses', $user_statuses)
            ->with('clubs', $clubs)
            ->with('club_members', $club_members)
            ->with('attendance', $attendance)
            ->with('event', $event);
    }



    //pozvanka pre ostatných členov
    public function invitationEventMemberAll($event_id)
    {

        $club_class = new Club();
        $event_class = new Event();

        $clubs_user = null;

        $event = Event::find($event_id);

        $clubs = $club_class->orderByDesc('active')->orderBy('short_title')->where('active', 1)->get();

        //zoznam uz pozvanych k eventu
        $attendance = $event_class->getEventAttendanceList($event->id);



        // ak je povolene pozyva t clenov inych klubov
        if ($event->type->invite_other_clubs){

            foreach ($clubs as $c){
                //ak je vybrany klub zobrazime len tento
                if(Session::exists('invitation_member_all_filter_club') && Session::get('invitation_member_all_filter_club') > 0){

                    if ($c->id  ==  Session::get('invitation_member_all_filter_club')){

                        $clubs_user[$c->id]['users'] = $club_class->getUsersFromClub($c->id);
                        $clubs_user[$c->id]['club'] = $c->title;
                        $clubs_user[$c->id]['club_id'] = $c->id;
                    }

                } else {

                    $clubs_user[$c->id]['users'] = $club_class->getUsersFromClub($c->id);
                    $clubs_user[$c->id]['club'] = $c->title;
                    $clubs_user[$c->id]['club_id'] = $c->id;
                }

            }


        } else {

            // ak je povolene pozyvat clenov vt
            if($event->type->invite_other_executives){

                foreach ($clubs as $c){
                    //ak je vybrany klub zobrazime len tento
                    if(Session::exists('invitation_member_all_filter_club') && Session::get('invitation_member_all_filter_club') > 0){

                        if ($c->id  ==  Session::get('invitation_member_all_filter_club')){

                            $clubs_user[$c->id]['users'] = $club_class->getUserManagersTeamFromClubs($c->id);
                            $clubs_user[$c->id]['club'] = $c->title;
                            $clubs_user[$c->id]['club_id'] = $c->id;
                        }

                    } else {

                        $clubs_user[$c->id]['users'] = $club_class->getUserManagersTeamFromClubs($c->id);
                        $clubs_user[$c->id]['club'] = $c->title;
                        $clubs_user[$c->id]['club_id'] = $c->id;
                    }

                }

            }

        }



        return view('invitation.event_member_all')
            //->with('user_statuses', $user_statuses)
            ->with('clubs', $clubs)
            ->with('clubs_user', $clubs_user)
            ->with('attendance', $attendance)
            ->with('event', $event);
    }



    //invitation even own member
    public function invitationEventMemberStore(Request $request)
    {

        $users = null;
        $send = 0;

        $event = Event::find($request->event_id);
        $club_class = new Club();

        //id user club manager
        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        // ak sa prihlasi na ranajky clen danaeho klubu automaticky je potvrdeny
        $status_invite = 1;

        //pozvanka pre clena klubu = 2
        $invitation_type = 2;

        // ranny klub
        if ($event->event_type == 1 ){
            $status_invite = 2;

            // mixer
        }elseif ($event->event_type == 2 ){
            $status_invite = 1;

            // poobedny klub
        }elseif ($event->event_type == 4){
            $status_invite = 2;

        }

        //typy formularov vlastny klub
        if(strcmp($request->form_type, "own_member") == 0){
            $rules = [
                'user' => 'required',
            ];

            $messages = [
                'user.required' => 'Nie su vybraní žiadny uživatelia na pozvanie',
            ];

            $this->validate($request,$rules, $messages);

            if ($request->user){
                foreach ($request->user as $u){
                    // vlozime do prezencky udalosti
                    $attendance_id = $this->invitation_class->addUserAttendanceList( $event->id, $u, $club_manager, "", $status_invite, $invitation_type);
                    if($attendance_id > 0 ){
                        $send = $send + 1;
                        if($this->debug_send_email_switch) {
                            //odosleme email pre clenov s moznostou pozyvania hosti
                            $this->invitation_class->addNotificationInvitationToCue($u, $club_manager, $event->id, $attendance_id, $invitation_type);
                        }
                    }

                }
            }
        }


        $redirect = redirect()->route('invitations.event.member', ['id' => $event->id ]);

        if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bolo  odoslané ' . $send . ' pozvánok.');

        return $redirect;
    }



    //invitation even own member
    public function invitationEventMemberAllStore(Request $request)
    {

        $users = null;
        $send = 0;

        $event = Event::find($request->event_id);
        $club_class = new Club();

        //id user club manager
        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        // ak sa prihlasi na ranajky clen danaeho klubu automaticky je potvrdeny
        $status_invite = 1;

        //pozvanka pre clena klubu = 2
        $invitation_type = 2;

        // ranny klub
        if ($event->event_type == 1 ){
            $status_invite = 2;

            // mixer
        }elseif ($event->event_type == 2 ){
            $status_invite = 1;

            // poobedny klub
        }elseif ($event->event_type == 4){
            $status_invite = 2;

        }


        //typy clenovia ostatnych klubov
        if(strcmp($request->form_type, "others_member") == 0) {

            $rules = [
                'user' => 'required',
            ];

            $messages = [
                'user.required' => 'Nie su vybraní žiadny uživatelia na pozvanie',
            ];

            $this->validate($request,$rules, $messages);
            //ak pozyvame clenov inych klubov musia sa prihlasit ( neaju status prihlaseny)
            $status_invite = 1;

            // pozvanka bez moznosti pozyvabia hosti
            $invitation_type = 1;

            if ($request->user) {
                foreach ($request->user as $u) {

                    // vlozime do prezencky udalosti
                    $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $u, $club_manager, "", $status_invite, $invitation_type);
                    if ($attendance_id > 0) {
                        $send = $send + 1;

                        // odosleme email pre clenov s moznostou pozyvania hosti
                        if($this->debug_send_email_switch) {
                            $this->invitation_class->addNotificationInvitationToCue($u, $club_manager, $event->id, $attendance_id, $invitation_type);
                        }
                    }

                }

            }

        }


        //zmena clubu
        if ($request->club >= 0 && isset($request->form_type) && strcmp('club_select', $request->form_type) == 0) {

            if ($request->club > 0){
                Session::put('invitation_member_all_filter_club', $request->club);
            }else{
                Session::forget('invitation_member_all_filter_club');
            }
        }


        $redirect = redirect()->route('invitations.event.member-all', ['id' => $event->id ]);

        if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bolo  odoslané ' . $send . ' pozvánok.');

        return $redirect;
    }





    //invitation guest store
    public function invitationEventGuestStore(Request $request)
    {

        $send = 0;
        $users = null;
        $event = Event::find($request->event_id);
        $club_class = new Club();

        //id user club manager
        //$club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);

        //id pozyvajuceho usera
        $club_manager = Auth::user()->id;

        //ak pozyvamehosti status nepotvrdeny
        $status_invite = 1;

        //pozvanka pre clena klubu = 2
        $invitation_type = 2;

        // ranny klub
        if ($event->event_type == 1 ){
            $status_invite = 2;

            // mixer
        }elseif ($event->event_type == 2 ){
            $status_invite = 1;

            // poobedny klub
        }elseif ($event->event_type == 4){
            $status_invite = 2;

        }

        // ak je po termine
        if( Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) < Carbon::createFromFormat('Y-m-d H:i:s', now()) ){
            // ak prihlasujeme do prezencky bez pozvanky nastavime status potvrdeny
            $status_invite = 2;
        }


        //typy formularov vlastny klub
        if(strcmp($request->form_type, "club_guests") == 0) {

            $rules = [
                'user' => 'required',
            ];

            $messages = [
                'user.required' => 'Nie su vybraní žiadny uživatelia na pozvanie',
            ];

            $this->validate($request,$rules, $messages);

            //ak pozyvame clenov inych klubov musia sa prihlasit ( neaju status prihlaseny)
            $status_invite = 1;

            // ak je po termine
            if( Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) < Carbon::createFromFormat('Y-m-d H:i:s', now()) ){
                // ak prihlasujeme do prezencky bez pozvanky nastavime status potvrdeny
                $status_invite = 2;
            }

            // pozvanka bez moznosti pozyvabia hosti
            $invitation_type = 1;

            if ($request->user) {
                foreach ($request->user as $u) {
                    // vlozime do prezencky udalosti
                    $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $u, $club_manager, "", $status_invite, $invitation_type);
                    if ($attendance_id > 0) {
                        $send = $send + 1;
                        // odosleme email pre clenov s moznostou pozyvania hosti
                        if($this->debug_send_email_switch) {
                            $this->invitation_class->addNotificationInvitationToCue($u, $club_manager, $event->id, $attendance_id, $invitation_type);
                        }

                    }

                }

            }

        }

        //zmena stavu
        if ($request->user_status >= 0 && isset($request->form_type) && strcmp('guest_status', $request->form_type) == 0) {

            if ($request->user_status > 0){
                Session::put('invitation_guest_filter_status', $request->user_status);
            }else{
                Session::forget('invitation_guest_filter_status');
            }

            if ($request->user_invite > 0){
                Session::put('invitation_guest_filter_user_invite', $request->user_invite);
            }else{
                Session::forget('invitation_guest_filter_user_invite');
            }

            if (strlen($request->search_user) > 2){
                Session::put('invitation_guest_filter_user', $request->search_user);
            }else{
                Session::forget('invitation_guest_filter_user');
            }

            if (intval($request->club) > 0){
                Session::put('invitation_guest_filter_club', $request->club);
            }

        }


        $redirect = redirect()->route('invitations.event.guest', ['id' => $event->id ]);

        if($send > 0) $redirect->with('message', 'K udalosti ' . $event->title . ' bolo  odoslané ' . $send . ' pozvánok.');

        return $redirect;

    }





}
