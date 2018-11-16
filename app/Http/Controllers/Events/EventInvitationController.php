<?php

namespace App\Http\Controllers\Events;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Invitation\Invitation;
use App\Models\Membership;
use App\Models\Notification\EmailNotification;
use App\Models\Setting\Industry;
use App\Rules\CheckGuestUniqueRule;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use PHPUnit\Util\RegularExpression;

class EventInvitationController extends Controller
{


    private $data;
    private $invitation_class;
    private $hashid;

    public function __construct()
    {
        $this->hashid = new Hashids('salt', 10);
        $this->invitation_class = new Invitation();
    }


    // zakladny formular emaila telefon
    public function userSendInvitationToGuest($id)
    {

        // presmerovanie na novy formular len s email a phone
        return redirect(route('ext.invite.guest.step1', $id));

//        // overime ci existuje clen
//        $user = User::where(['token_id' => $id, 'admin' => 1])->first();
//        $industry = Industry::orderBy('name')->get();
//
//        if ($user){
//
//            $event = Event::where('club_id', $user->club)->get();
//
//            $club_class = new Club();
//            //kluby v ktorych ma clen aktivne clenstva
//            $clubs = $club_class->getClubsFromUsers($user->id);
//
//            $events = DB::table('events AS e')
//                ->select('e.event_from', 'c.title AS club', 'e.title AS event', 'e.id')
//                ->join('clubs AS c', 'e.club_id', '=', 'c.id')
//                ->whereIn('club_id', $clubs->pluck('id'))
//                ->where('e.event_from', '>', Carbon::now())
//                ->where('e.active', 1)
//                ->orderBy('e.event_from')
//                ->get();
//
//            return view('external.member_invitation')
//                ->with('user', $user)
//                ->with( 'events', $events)
//                ->with('industry', $industry);
//
//        } else {
//
//            // ak user neexistuje alebo je adresa poskodena
//            //dd('zaujem o ranajky vseobecny');
//            return redirect()->to('//www.bforb.sk/chcem-prist-na-ranajky');
//
//        }

    }




    // spracovanie zakladneho formulara email a telefon
    public function invitationStore(Request $request)
    {

        $rules = [
            'user_id' => 'required',
            'event' => 'required|integer',
            'gender' => 'required',
            'surname' => 'required',
            'industry' => 'required',
            'phone' => 'required|numeric|phone',
            'internet' => 'required',
            //'email'    => ['required', 'unique:users,email'],
            'company' => 'required',
            //'name' => ['required', new CheckGuestUniqueRule() ],
            'name' => 'required',
        ];

        $messages = [
            'unique' => 'Emailová adresa ' . $request->email . ' už v systéme existuje.',
        ];


        $this->validate($request,$rules, $messages);
        $guests = $this->checkGuestDuplicity($request);

        $user_class = new User();

        // ak existuje jeden alebo viac zaznamov vyhodime chybu a presmerujeme na frormular
        if(count($guests) > 0 ){

            $invite_user = User::where(['id' => $request->user_id ])->first();
            $industry = Industry::orderBy('name')->get();

            if ($invite_user){

                $club_class = new Club();
                //kluby v ktorych ma clen aktivne clenstva
                $clubs = $club_class->getClubsFromUsers($invite_user->id);

                $events = DB::table('events AS e')
                    ->select('e.event_from', 'c.title AS club', 'e.title AS event', 'e.id')
                    ->join('clubs AS c', 'e.club_id', '=', 'c.id')
                    ->whereIn('club_id', $clubs->pluck('id'))
                    ->where('e.event_from', '>', Carbon::now())
                    ->where('e.active', 1)
                    ->orderBy('e.event_from')
                    ->get();

                return view('external.member_duplicity_invitation')
                    ->with('user', $invite_user)
                    ->with('req', $request->only('event'))
                    ->with( 'events', $events)
                    ->with('guests', $guests)
                    ->with('industry', $industry);

            } else {
                // ak user neexistuje alebo je adresa poskodena
                return redirect()->to('//bforb.sk');
            }

        }else{

            $event = Event::find($request->event);
            $new_user_id = $user_class->addNewGuestToDb($request, $event->club_id);

            //pozvanka pre clena klubu = 2, pre hosta 1
            $invitation_type = 1;

            // vlozime do prezencky udalosti
            $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event, $new_user_id, $request->user_id, $request->description, $invitation_type);

            // odosleme email
            $this->invitation_class->addNotificationInvitationToCue($new_user_id, $request->user_id, $request->event, $attendance_id, $invitation_type );

            return redirect()->back()->with('message', 'Hosť úspešne pozvaný');

        }
    }










//
//    //invitation eventdetail
//    public function invitationEventDetail($id)
//    {
//
//
//        $club_class = new Club();
//        $event_class = new Event();
//
//        $event = Event::find($id);
//
//        $industry = Industry::orderBy('name')->get();
//
//
//        $user_statuses = DB::table('user_status')->where('internal', 1)->get();
//
//        // uzivatelia klubu ktory organizuje event
//        $club_members = $club_class->getUsersFromClub($event->club_id);
//
//        // hostia ktory patria do klubu
//
//        //okrem stavov
//        $exclude_statuses = [ 8,17,23];
//        $club_guests = $club_class->getGuestsFromClub($event->club_id,
//            Session::get('events_invitation_filter_status', null), $exclude_statuses);
//
//        //zoznam uz pozvanych k eventu
//        $attendance = $event_class->getEventAttendanceList($event->id);
//
//        //id user club manager
//        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);
//
//        // aktivne cluby
//        $clubs = Club::where('active', 1)->get();
//
//        $clubs_user = null;
//        foreach ($clubs as $c){
//            //ak je vybrany klub zobrazime len tento
//            if(Session::exists('events_invitation_filter_club') && Session::get('events_invitation_filter_club') > 0){
//
//                if ($c->id  ==  Session::get('events_invitation_filter_club')){
//
//                    $clubs_user[$c->id]['users'] = $club_class->getUsersFromClub($c->id);
//                    $clubs_user[$c->id]['club'] = $c->title;
//                    $clubs_user[$c->id]['club_id'] = $c->id;
//                }
//
//            } else {
//                $clubs_user[$c->id]['users'] = $club_class->getUsersFromClub($c->id);
//                $clubs_user[$c->id]['club'] = $c->title;
//                $clubs_user[$c->id]['club_id'] = $c->id;
//            }
//
//        }
//
//
//        //dd($clubs_user);
//
//        return view('events.invitation_event')
//                    ->with('user_statuses', $user_statuses)
//                    ->with('industry', $industry)
//                    ->with('clubs', $clubs)
//                    ->with('clubs_user', $clubs_user)
//                    ->with('club_members', $club_members)
//                    ->with('club_guests', $club_guests)
//                    ->with('attendance', $attendance)
//                    ->with('event', $event);
//    }

//
//    //invitation eventdetail
//    public function invitationEventDetailStore(Request $request)
//    {
//
//        $users = null;
//        $event = Event::find($request->event_id);
//        $club_class = new Club();
//
//
//        $loged_user = Auth::user()->id;
//
//        //id user club manager
//        $club_manager = $club_class->getExecutiveManagerFromClub($event->club_id);
//
//        if(isset($request->clubs_user) && $request->clubs_user ){
//
//            foreach ($request->clubs_user as $k =>$cu){
//                // rozdelime na klub a id uzera
//                $cu_array = explode('-', $cu);
//                $users[$k]['user_id'] = $cu_array[1];
//                $users[$k]['club_id'] = $cu_array[0];
//
//            }
//
//        }
//
//
//        // ak sa prihlasi na ranajky clen danaeho klubu automaticky je potvrdeny
//        $status_invite = 1;
//        //pozvanka pre clena klubu = 2
//        $invitation_type = 2;
//
//        // ranny klub
//        if ($event->event_type == 1 ){
//            $status_invite = 2;
//
//            // mixer
//        }elseif ($event->event_type == 2 ){
//            $status_invite = 1;
//
//            // poobedny klub
//        }elseif ($event->event_type == 4){
//            $status_invite = 2;
//
//        }
//
//
//        //typy formularov vlastny klub
//        if(strcmp($request->form_type, "own_member") == 0){
//            $rules = [
//                'user' => 'required',
//            ];
//
//            $messages = [
//                'user.required' => 'Nie su vybraný žiadny uživatelia na pozvanie',
//            ];
//
//            $this->validate($request,$rules, $messages);
//
//            if ($request->user){
//                foreach ($request->user as $u){
//
//                    // vlozime do prezencky udalosti
//                    $attendance_id = $this->invitation_class->addUserAttendanceList( $event->id, $u, $club_manager, "", $status_invite, $invitation_type);
//                    if($attendance_id > 0 ){
//                        // odosleme email pre clenov s moznostou pozyvania hosti
//                        $this->invitation_class->addNotificationInvitationToCue( $u, $club_manager, $event->id, $attendance_id, $invitation_type );
//                    }
//
//                }
//            }
//        }
//
//
//
//        //typy formularov vlastny klub
//        if(strcmp($request->form_type, "others_member") == 0) {
//
//            $rules = [
//                'user' => 'required',
//            ];
//
//            $messages = [
//                'user.required' => 'Nie su vybraný žiadny uživatelia na pozvanie',
//            ];
//
//            $this->validate($request,$rules, $messages);
//            //ak pozyvame clenov inych klubov musia sa prihlasit ( neaju status prihlaseny)
//            $status_invite = 1;
//
//            // pozvanka bez moznosti pozyvabia hosti
//            $invitation_type = 1;
//
//            if ($request->user) {
//                foreach ($request->user as $u) {
//
//                    // vlozime do prezencky udalosti
//                   $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $u, $club_manager, "", $status_invite, $invitation_type);
//                    if ($attendance_id > 0) {
//                        // odosleme email pre clenov s moznostou pozyvania hosti
//                        $this->invitation_class->addNotificationInvitationToCue($u, $club_manager, $event->id, $attendance_id, $invitation_type);
//                    }
//
//                }
//
//            }
//
//        }
//
//
//
//        //typy formularov vlastny klub
//        if(strcmp($request->form_type, "club_guests") == 0) {
//
//            $rules = [
//                'user' => 'required',
//            ];
//
//            $messages = [
//                'user.required' => 'Nie su vybraný žiadny uživatelia na pozvanie',
//            ];
//
//            $this->validate($request,$rules, $messages);
//
//            //ak pozyvame clenov inych klubov musia sa prihlasit ( neaju status prihlaseny)
//            $status_invite = 1;
//
//            // pozvanka bez moznosti pozyvabia hosti
//            $invitation_type = 1;
//
//            if ($request->user) {
//                foreach ($request->user as $u) {
//                    // vlozime do prezencky udalosti
//                    $attendance_id = $this->invitation_class->addUserAttendanceList($event->id, $u, $club_manager, "", $status_invite, $invitation_type);
////                    if ($attendance_id > 0) {
////                        // odosleme email pre clenov s moznostou pozyvania hosti
////                        $this->addNotificationInvitationToCue($u, $club_manager, $event->id, $attendance_id, $invitation_type);
////                    }
//
//                }
//
//            }
//
//        }
//
//
//
//        //zmena clubu
//        if ($request->club >= 0 && isset($request->form_type) && strcmp('club_select', $request->form_type) == 0) {
//            if ($request->club > 0){
//                Session::put('events_invitation_filter_club', $request->club);
//            }else{
//                Session::forget('events_invitation_filter_club');
//            }
//            return redirect()->route('events.invitation.detail', ['id' => $event->id, 'tab' => 2 ]);
//        }
//
//
//        //zmena stavu
//        if ($request->user_status >= 0 && isset($request->form_type) && strcmp('guest_status', $request->form_type) == 0) {
//            if ($request->user_status > 0){
//                Session::put('events_invitation_filter_status', $request->user_status);
//            }else{
//                Session::forget('events_invitation_filter_status');
//            }
//            return redirect()->route('events.invitation.detail', ['id' => $event->id, 'tab' => 3 ]);
//        }
//
//        return redirect()->route('events.invitation.detail', ['id' => $event->id, 'tab' => 1 ]);
//    }
//




    //znovu preposlanie pozvanky
    public function resendInvitationFromAttendanceStore(Request $request)
    {

        $attendance = DB::table('attendance_lists')->find($request->attendance_id);
        if($attendance){
            // odosleme email
            $this->invitation_class->addNotificationInvitationToCue( $attendance->user_id, $attendance->user_invite_id,
                                    $attendance->event_id, $attendance->id, $attendance->invitation_type );
        }

        return redirect()->route('events.listing.show', ['id' => $attendance->event_id]);

    }




    public function invitationResponse($attend)
    {

        //        1 Bez reakcie
        //        2 Potvrdená účas
        //        3 Ospravedlnená účasť
        //        4 Účasť náhradného zástupcu
        //        9 odhalsit z mailing listu


        $data = $this->hashid->decode($attend);

        //overime ci su data v poriadku
        if (count($data) == 3 ){

            $email = new EmailNotification();

            $attendance_id = $data[0];
            $user_id = $data[1];
            $status = $data[2];

            $user = User::find($user_id);

            $attendance = DB::table('attendance_lists')->find($attendance_id);

            $title = "";
            $bg_class = "";

            $content = [];

            // kto pozýval hosta
            $attended_member = User::find($attendance->user_invite_id );
            // pozvaný hosť
            $attend_guest = User::find($attendance->user_id );

            // udalost
            $event = Event::find($attendance->event_id );


            if($status == 2){
                //potvrdena ucast
                $title = "Potvrdenie účasti";
                $bg_class = "success";

                //aktualizujeme stav
                DB::table('attendance_lists')->where('id', $attendance_id )->update(['status' => $status]);


            }elseif ($status == 3){
                // ospravedlnenie
                $title = "Ospravedlnenie účasti";
                $bg_class = "warning";

                $attendance = DB::table('attendance_lists')->where('id', $attendance_id )->update(['status' => $status]);

            }else{
                //odhlasit
                $title = "Odhlásenie zo zasielania";
                $bg_class = "danger";
                $user->status = 8;
                $user->save();
            }

            // odosleme notifikaciu pozyvatelovi
            $content['subject'] = $title . ' pozvaného hosťa ' . $attend_guest->full_name . ' na udalosť ' . $event->title . ' - ' .
                Carbon::createFromFormat('Y-m-d H:i:s' , $event->event_from)->format('d.m.Y');

            $content['id'] = $event->id;
            $content['url'] = null;
            $content['text'] =  $content['subject'] . "\n\n";

            // odosleme
            $email->addNotificationSystemTransaction( $attended_member->email, $content );

        } else {

            return view('external.error');

        }

        $text['title'] = $title;
        $text['bg_jumbotron_'] = $bg_class;

        return view('external.guest_invitation_response')
            ->with('text', $text)
            ->with('user', $user);

    }





    public function invitationExistStore(Request $request)
    {

        $user = User::find($request->invitation_to);

        //pozvanka pre clena klubu = 2, pre hosta 1
        $invitation_type = 1;

        // vlozime do prezencky udalosti
        $attendance_id = $this->invitation_class->addUserAttendanceList( $request->event, $request->invitation_to, $request->user_id, $request->description, $invitation_type);

        if($attendance_id > 0){
            // odosleme email
            $this->invitation_class->addNotificationInvitationToCue($request->invitation_to, $request->user_id, $request->event, $attendance_id, $invitation_type );

            $text['title'] = "Hosť úspešne pozvaný";
            $text['bg_jumbotron_'] = "success";

        }else{

            $text['title'] = "Duplicitné pozývanie nie je možné";
            $text['bg_jumbotron_'] = "success";

        }

        return view('external.guest_invitation_response')
            ->with('text', $text)
            ->with('user', $user);

    }





    public function checkGuestDuplicity($request)
    {

        $this->data['name'] = strtolower(trim(toAscii($request->name)));
        $this->data['surname'] = strtolower(trim(toAscii($request->surname)));
        $this->data['email'] = strtolower(trim(toAscii($request->email)));
        $this->data['phone'] = trim(toAscii($request->phone));
        $this->data['company'] = strtolower(trim(toAscii($request->company)));


        $email =         $guest = DB::table('users AS u')->select( 'u.name','u.surname', 'u.id', 'u.email', 'u.phone', 'u.status', 'us.status')
            ->join('user_status AS us', 'us.id', '=', 'u.status')
            ->where('email', '=', $this->data['email'])
            ->get();

        if ($email) {
            return $guest;
        }else{

            $guest = DB::table('users AS u')->select( 'u.name','u.surname', 'u.id', 'u.email', 'u.phone', 'u.status', 'us.status')
                ->join('user_status AS us', 'us.id', '=', 'u.status')
                ->whereRaw('LOWER(`surname`) = ? ',[  $this->data['surname'] ])
                ->whereRaw('LOWER(`name`) = ? ',[  $this->data['name'] ])
                ->where('email', '=', $this->data['email'])
                ->orWhere('phone', '=', $this->data['phone'])
                ->get();

            //ak mame rovnake meno skontrolujeme emailovu adresu
            return $guest;
        }

    }




}
