<?php

namespace App\Http\Controllers\Developer;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceImapEmail;
use App\Models\Finance\InvoicePdf;
use App\Models\Notification\EmailNotification;
use App\Models\Notification\InvoiceOverdueNotification;
use App\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;

class TestControler extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        //prejdeme emaily
        //$imap = new InvoiceImapEmail();
        //Log:info($imap->readImapMail());

       // $this->doplnenieKtoPozvalHostaDoTabulkyUser();

//        $email = new EmailNotification();
//        $res_email = $email->procesEmailNotification();

       //  Log::info($res_email);


    }



    private function doplnenieKtoPozvalHostaDoTabulkyUser(){

        // ziskame vsetkych uzivatelov sk
        $users_sk = DB::table('users')
            ->select('id', 'user_cackon_id', 'name', 'surname' )
            ->where('user_cackon_id', '>', 0)
            ->where('verified', 0)
            ->orderByDesc('user_cackon_id')
            ->take(1000)->get();


        //prejdeme vsetkych a najdeme kde existuje info o tom kto ho pozval
        foreach ($users_sk as $key => $value){

            //zistime ci tento uzivatel existuje a ma zaznam kto ho pozval
            $user_cackon = DB::connection('mysql2')->table('lide')
                ->where('id', $value->user_cackon_id)
                ->first();

            // ak je zaznam kto ho poznval
            if ($user_cackon->pozval > 0){

                //overime ci mame tohto usera v tabulke users
                $user_invite_in_table = DB::table('users')
                    ->where('user_cackon_id', $user_cackon->pozval )
                    ->first();

                dump($user_cackon->jmeno . ">" . $user_cackon->pozval . " --  " . $value->surname . " >>> " . $user_invite_in_table);


                if ($user_invite_in_table){


//                    dump($user_cackon->pozval);
//
//                    dump($user_invite_in_table->surname);
//                    dump($user_invite_in_table->id);

//                    DB::table('users')
//                        ->where('id', $value->id)
//                        ->update(['verified' => 1, 'created_user' => $user_invite_in_table->id ]);

                }



            }


        }



        $res = $users_sk;



        //$res = $this->getLideFromCackon();

        return $res;

    }






    private function doplnenieKtoPozvalHostaDoTabulkyUserAttendanceList(){

        $start_user_id = 4300;
        $start_attendance_id = 409163;

        $users = DB::table('users')
                    ->where('id', '>', $start_user_id)
                    ->whereNull('created_user')
//                    ->take(20)
                    ->get();

        $attendance = DB::table('attendance_lists AS al')
                    ->select('al.id', 'al.user_invite_id', 'al.user_id', 'e.event_type')
                    ->join('events AS e', 'e.id', '=', 'al.event_id')
                    ->where('al.id', '>', $start_attendance_id)
                    ->where('e.event_type', 1)
                    //->orderByDesc('al.id')
                    ->get();


        foreach ($users as $u){

            foreach ($attendance as $a){

                // ak sa tento user nachadza v prezencke
                if($u->id == $a->user_id){

//                    dump($created);
                    //dump($u-id);

                    //zistime ci user uz nema zapisaneho uzivatela v create

                    $created = DB::table('users')->where('id', $u->id)->value('created_user');

                    // ak je O zapiseme do user created_user
                    if ($created == 0 && $a->user_invite_id != 23){

                        dump($created);
                        dump($a->user_invite_id);
                        dump($u->id);

                        //DB::table('users')->where('id', $u->id)->update( ['created_user' => $a->user_invite_id ] );

                    }

                }

            }




        }


        dd($attendance);

    }





    private function getClubUser($user_id){


        $query = DB::table('attendance_lists AS al')
                    ->select('ce.id AS club_id')
                    ->join('events AS e', 'e.id', '=', 'al.event_id', 'LEFT')
                    ->join('users AS usi', 'usi.id', '=', 'al.user_id', 'LEFT')
                    ->join('clubs AS ce', 'ce.id', '=', 'e.club_id', 'LEFT')
                    ;

        $query->where('usi.id', '>', 0);
        $query->where('usi.id', '=', $user_id);
        //$query->where('usi.admin', '=', 0);


        $query->orderBy('ce.id', 'desc');


        $query->groupBy('ce.id');


        $query->take(1000);
        return  $query->pluck('club_id')->toArray();

    }


    public function cron(){


        Log::info('cron od willmann:'. Carbon::now()->toDateTimeString());

        $email = new EmailNotification();
        dump($email->procesEmailNotification());

        // prejdeme emaily kvoli uhradam
       // $imap = new InvoiceImapEmail();
       // dump($imap->readImapMail());

    }

    public function sanitaze_prezencky(){


        $reference = DB::table('attendance_lists')->where('user_id', 0)->limit(10000)->get();
        foreach ($reference as $r){

            $ludia_sk = DB::connection('mysql2')->table('lide')
                ->where('id', $r->ucastnik)
                ->first();


            if($ludia_sk && strlen($ludia_sk->email) > 3 && strlen($ludia_sk->jmeno) > 5 ){

                //dump($ludia_sk);

                $users_raw = $this->sanitazeUserDatabazeCackon($ludia_sk);

                if(strlen($users_raw['email']) > 5 && strlen($users_raw['name']) > 2  && strlen($users_raw['surname']) > 2 ){

                    //dump($users_raw);

                    $search = $users_raw['name'] . ' ' . $users_raw['surname'];
                    $guest = DB::table('guests as g')
                        //->where("name" , 'LIKE',  $users_raw['name'] )
                        //->where("surname" , 'LIKE',  $users_raw['surname'] )
                        //->where('email', '=', $users_raw['email'])
                        ->orWhere(DB::raw('CONCAT(name," ",surname)'), 'LIKE', "%{$search}%")
                        ->first();

                    //ak existuje priradime do alternativnych
                    if($guest){

                        $data=null;
                        $data['id_admin_sk'] = $guest->id;
                        $data['email'] =  $users_raw['email'];
                        $data['id_cackon_unique'] =  $guest->user_cackon_id;
                        $data['id_cackon_alt'] = intval($users_raw['id']);

                        //nacitame  alt idcka usera
                        $user_alt = DB::table('guest_conversation_alt')->get();
                        $key = array_search(intval($users_raw['id']), array_column( $user_alt->toArray() , 'id_cackon_alt'));

                        if (!$key > 0  &&  $guest->user_cackon_id !=  intval($users_raw['id']) ){

                            dump('zapiseme alt id usera ' . intval($users_raw['id'] ) );

                            // ak najde zhodu v emaily zapiseme do tab guest_conversation_alt
                            DB::table('guest_conversation_alt')->insert($data);

                        }


                        // updatujeme attendance list
                        DB::table('attendance_lists')->where('id', $r->id)->update(['user_id' => $guest->id]);

//                        dump($r);
//                        dump($data);
//                        dump($guest);

                    }else{
                        //ak neexistuje v guests
                        if(!DB::table('guests')->where('email', $users_raw['email'])){

                            $data=null;
                            $data['gender'] = $users_raw['gender'];
                            $data['name'] = $users_raw['name'];
                            $data['surname'] = $users_raw['surname'];
                            $data['title'] = $users_raw['title'];
                            $data['email'] = $users_raw['email'];
                            $data['phone'] = $users_raw['phone'];
                            $data['status'] = 0;
                            $data['description'] = "";

                            $data['club_cackon_id'] = 0;
                            $data['user_cackon_id'] = $users_raw['id'];
                            $data['internet'] = strtolower($users_raw['web']);
                            $data['created_at'] = Carbon::now();
                            $data['updated_at'] = Carbon::now();

                            //dd($users_raw);

                            $last_id = DB::table('guests')->insertGetId($data);

                            if($last_id > 0){
                                // updatujeme attendance list
                                DB::table('attendance_lists')->where('id', $r->id)->update(['user_id' => $last_id]);
                            }

                            //dump($data);

                            dump('last id' . $last_id);
                            //dump($guest);
                        }


                    }


                }

            }


//            foreach ($user_alt as $ua){
//                if($r->ucastnik == $ua->id_cackon_unique){
//
//                    DB::table('attendance_lists')->where('id', $r->id)->update(['user_id' => $ua->id_admin_sk]);
//                    dump($ua);
//                }
//            }

        }

    }

    public function duplicate_address()
    {
        $res=[];
        $data = [];

        $users = DB::connection('mysql2')->table('lide_123')
            ->where('export', 0)
            //->where('jmeno', 'LIKE', '%bai%')
            //->offset(1000)
            ->limit(400)
            ->orderBy('razeni')
            ->get();

        if($users){
            foreach ($users as $k => $u){

                // output sanitize
                $us = $this->sanitazeUserDatabazeCackon($u);

                dump($us);

                // ak nepreslo zapiseme do db export 1
                if(!$us){
                    $r = DB::connection('mysql2')->table('lide_123')
                        ->where('id', $u->id)
                        ->update(['export' => 1]);

                    dump($u->id . ' ID oznaceneho zaznamu: ' . $r);

                }else{
                    // porovnavame zhodu

                    // ak je zadany  email
                    if (isset($us['email'])){

                        //databaza hosti
                        $guest = DB::table('guests')->select(['id','email', 'user_cackon_id'])->where('email', $us['email'])->first();

                        //ak uz zaznamy existuju a nezhoduju sa id (rovnaky zaznam)
                        if(count($guest) > 0) {

                            if(intval($us['id']) ==  intval($guest->user_cackon_id)){

                                $r = DB::connection('mysql2')->table('lide_123')
                                    ->where('id', $u->id)
                                    ->update(['export' => 2]);

                                dump($u->id . ' ID oznaceneho duplicitneho zaznamu: ' . $r);

                            } else {

                                $data['id_admin_sk'] = $guest->id;
                                $data['email'] =  $guest->email;
                                $data['id_cackon_unique'] =  intval($us['id']);
                                $data['id_cackon_alt'] = $guest->user_cackon_id;
                                // ak najde zhodu v emaily zapiseme do tab guest_conversation_alt
                                DB::table('guest_conversation_alt')->insert($data);
                                // oznacime export do db
                                DB::connection('mysql2')->table('lide_123')->where('id', $us['id'])->update(['export' => 1]);

                                //ak sa nenasla zhoda v emaile skusime telefon

                            }

                        } else {


                            $r = DB::connection('mysql2')->table('lide_123')
                                ->where('id', $u->id)
                                ->update(['export' => 1]);

                            dump($u->id . ' ID nenasiel email: ' . $r);

                        }

                        dump($guest);

                    }


                }


            }
        }

    }


    // vratime upravene data hosta
    public function sanitazeUserDatabazeCackon($guest){

        $res = null;
        //tituly akceptovane
        $title = ['ing', 'mgr','judr','mudr','ng', 'bc', 'mvdr', 'mga', 'dr'];

        $l = $guest;

        // existuje email a je spravny tvar
        if (  strlen($l->email) > 3 && filter_var(trim($l->email), FILTER_VALIDATE_EMAIL)) {


            //dump($l);


            // email bez medzier na zaciatku a konci
            $res['id'] = trim($l->id);

            $res['email'] = trim($l->email);
            $res['web'] = trim($l->web);

            $male = [0,1,3,5,7,9,11,16];
            $gender = (in_array( intval($l->osloveni1), $male ) ) ? 'M' : 'F';
            $res['gender'] = $gender;

            //1-nový, 2-posílat pozvánky,  3-jednou přišel, 4-dvakrát přišel, 5-člen, 11-bývalí člen, 7-fanoušek BforB, 8-nezasílat


            // telefon ===========================================================================

            // telefon escapujeme nespravne znaky
            $phone = trim(str_replace([' ', '-', '/', ',', '(', ')'], '', $l->mobil));

            // ak je menej ako 8 znakov nespravne cislo
            if (strlen($phone) <= 8) {

                $res['phone'] = '';

                // ak zacina nulou zoberieme 0 a pridame  +421
            } elseif (strlen($phone) > 8 && strcmp($phone[0], '0') == 0) {

                $res['phone'] = '+421' . substr($phone, 1);

                // ak zacina 2mi nulami
            }elseif (strlen($phone) > 8 &&  strcmp(substr($phone, 0, 2), '00') == 0 ){

                $res['phone'] = '+421' . substr($phone, 2);

                // ak zacina 9 pridame +421
            } elseif (strlen($phone) > 8 && strcmp($phone[0], '9') == 0) {

                $res['phone'] = '+421' . $phone;

                // ak zacina 6 a 7 pridame +420
            } elseif (strlen($phone) > 8 && in_array($phone[0], [6,7]) == 1) {

                $res['phone'] = '+420' . $phone;

            }else {

                $res['phone'] =  $phone;

            }



            // meno ===========================================================================

            //rozdelime meno na medzery
            $arr_meno = preg_split('/\s+/', trim($l->jmeno));

            $res['title'] = '';
            $res['name'] = '';
            $res['surname'] = '';
            $res['surname_asci'] = '';
            $res['name_asci'] = '';

            if(count($arr_meno) > 2 && count($arr_meno) < 4 ){

                $title_name = str_replace( '.', '', strtolower($arr_meno[0]) );
                if (in_array($title_name, $title)) {

                    $res['title'] = $arr_meno[0];;
                    $res['name'] = $arr_meno[1];
                    $res['surname'] = $arr_meno[2];
                    $res['surname_asci'] = strtolower(toAscii($arr_meno[2]));
                    $res['name_asci'] = strtolower(toAscii($arr_meno[1]));

                }else{

                    $res['title'] = "";
                    $res['name'] = $arr_meno[0];
                    $res['surname'] = $arr_meno[1] . ' - '. $arr_meno[2];
                    $res['surname_asci'] = strtolower(toAscii($arr_meno[1] . ' - '. $arr_meno[2]));
                    $res['name_asci'] = strtolower(toAscii($arr_meno[2]));

                }

            }elseif (count($arr_meno) == 2 ){

                $res['title'] = '';
                $res['name'] = $arr_meno[0];
                $res['surname'] = $arr_meno[1];
                $res['surname_asci'] = strtolower(toAscii($arr_meno[1]));
                $res['name_asci'] = strtolower(toAscii($arr_meno[0]));

            } elseif ((count($arr_meno) > 1 )) {

                $res['title'] = '';
                $res['name'] = $arr_meno[0];
                $res['surname'] = $arr_meno[1];
                $res['surname_asci'] = strtolower(toAscii($arr_meno[1]));
                $res['name_asci'] = strtolower(toAscii($arr_meno[0]));

            }

            return $res;

        }

        return null;

    }



    // ziskame ludi ktory este neboli exportovany do druhej db
    public function getKlubyFromCackon()
    {

        $res = [];

        // vsetky kluby kde master je 123 cize slovenske
        $kluby = DB::connection('mysql2')->table('kluby')->where('master', 123)->get();

        foreach ($kluby as $u){

            $data['title'] = $u->popis;
            $data['short_title'] = $u->oznaceni;
            $data['address_country'] = 'Slovensko';
            $data['address_street'] = $u->adresa;
            $data['address_city'] = $u->mesto;
            $data['host_url'] = strtolower($u->web);
            $data['repeat_interval'] = 7;
            $data['repeat_day'] = 2;

            $data['description'] = strtolower($u->poznamky);

            $data['created_at'] = Carbon::createFromFormat('Y-m-d', $u->prvniSnidane);
            $data['updated_at'] = Carbon::now();
            $data['club_cackon_id'] = $u->id;

            DB::table('clubs')->insert([$data]);

            $res[] = $data;

        }

        return $res;
    }


    public function prepareDataToImportGuestToDb($users){

        $except_id = [];
        $prve_id = null;
        $user_others_data = [];
        $raw = null;
        $res = null;

        if($users){
            foreach ($users as $u){


                if(!in_array($u['id'], $except_id)) {

                    $prve_id = $u['id'];

                    foreach ($users as $k_u => $u_n) {

//                        if(isset($u_n['name_asci'])){
//                            dump( 'start' . $u_n['name_asci']);
//                        }else{
//                            dd( 'stop' . $u_n['name_asci']);
//                        }

                            // najdeme kde je zhoda mena a priezviska
                            if ($u['name_asci'] == $u_n['name_asci']

                                && $u['surname_asci'] == $u_n['surname_asci']

                                && $u['id'] != $u_n['id'] ) {

                                $user_others_data[] = $u_n;

                                // okrem vlastnych id
                                $except_id[] = $u_n['id'];

                            }


                    }

                    //doplnenie tel ak existuje v inom zazname
                    if ($user_others_data){
                        foreach ($user_others_data as $uod){
                            if (strlen($u['phone']) == 0  &&  strlen($uod['phone']) > 0 ) {
                                $u['phone'] = $uod['phone'];
                            }
                        }
                    }

                    $data['gender'] = $u['gender'];
                    $data['name'] = $u['name'];
                    $data['surname'] = $u['surname'];
                    $data['title'] = $u['title'];
                    $data['email'] = $u['email'];
                    $data['phone'] = $u['phone'];
                    $data['status'] = $u['status'];
                    $data['club_cackon_id'] = $u['club'];
                    $data['user_cackon_id'] = $u['id'];
                    $data['internet'] = strtolower($u['web']);
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();
                    $data['others_id'] = $except_id;
                    $data['prve_id'] = $prve_id;

                    $res[] = $data;


                }



                // vynulujeme data k inym zaznamom
                $user_others_data = null;

                // vynulujeme except id
                $except_id = [];
            }

        }

        return $res;

    }





    public function importGuestToDb($guest)
    {

        $data = collect($guest)->except('prve_id', 'others_id')->toArray();

        $prvy_user = DB::connection('mysql2')->table('lide')
            ->where('export', 0)
            ->where('id', $guest['prve_id'])
            ->count();

        // zsistieme ci este nebol importovany kvoli duplicite
        if($prvy_user == 1){
            //iportujeme data
            DB::table('guests')->insert([$data]);
            DB::connection('mysql2')->table('lide')->where('id', $guest['prve_id'])->update(['export' => 1]);

        }

        // oznacime duplicitne zaznamy
        if (is_array($guest['others_id'])){
            // oznacime duplicity
            DB::connection('mysql2')->table('lide')
                ->whereIn('id', $guest['others_id'])
                ->update(['export' => 1]);
        }

    }



    // ziskame ludi ktory este neboli exportovany do druhej db
    public function getLideFromCackon()
    {
        // vsetky kluby kde master je 123 cize slovenske
        $kluby = DB::connection('mysql2')->table('kluby')->where('master', 123)->get();

        // id slovenských klubov
        $kluby_id = $kluby->pluck('id');

        $kluby_implode = $kluby_id->implode(',');

        $ludia_sk = DB::connection('mysql2')->table('lide')
            ->whereIn('klub', $kluby_id)
            ->where('export', 0)
            //->where('jmeno', 'LIKE', '%bai%')
            ->offset(1000)

            ->limit(500)
            ->orderBy('razeni')
            ->get();

        return $ludia_sk;
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
     *i
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
