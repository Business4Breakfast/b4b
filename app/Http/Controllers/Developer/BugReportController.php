<?php

namespace App\Http\Controllers\Developer;

use App\Models\Notification\EmailNotification;
use App\Models\UploadImages;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BugReportController extends Controller
{

    private $module = 'bug-report';

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('developer.' . $this->module . '.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('developer.' . $this->module . '.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Typy udalostí'); //title

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $item = DB::table('bug_reports AS br')
                    ->select('br.*', 'u.name AS name', 'u.surname AS surname')
                    ->where('parent_id', 0)
                    ->join('users AS u', 'u.id', '=', 'br.user_id' )
                    ->orderBy('status')
                    ->orderByDesc('id')

            ->get();


        return view('developer.bug_report.index')
                ->with('items', $item);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('developer.bug_report.add');
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
            'description' => 'required',
            'bug_type' => 'required',
            'bug_report_route' => 'required',
        ];

        $this->validate($request,$rules);

        $data['user_id'] = Auth::user()->id;
        $data['bug_type'] = $request->bug_type;
        $data['url'] = $request->bug_report_route;
        $data['description'] = $request->description;
        $data['parent_id'] = 0;


        $new_bug = DB::table('bug_reports')->insertGetId($data);
        if($new_bug > 0){

            //if update succes and is files
            if ($new_bug && $request->hasFile('files')){
                $image = new UploadImages();
                //$image->deleteImage($this->module, $new_bug);
                $image->procesImage($request->files, $this->module, $new_bug, str_random() . '-' . $new_bug);
            }

        }

        return redirect()->to($request->bug_report_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $users = User::where('admin', 1)->get();


        $issue = DB::table('bug_reports AS br')
            ->select('br.*', 'u.name AS name', 'u.surname AS surname')
            ->join('users AS u', 'u.id', '=', 'br.user_id' )
            ->where('br.id', $id)
            ->first();


        $items = DB::table('bug_reports AS br')
            ->select('br.*', 'u.name AS name', 'u.surname AS surname')
            ->join('users AS u', 'u.id', '=', 'br.user_id' )
            ->where('br.parent_id', $id)
            ->orderByDesc('id')
            ->get();


        return view('developer.bug_report.detail')
            ->with('users', $users)
            ->with('issue', $issue)
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

        $data = null;

        $bug_report = DB::table('bug_reports')->find($id);

        // pridame data polozky k ulohe
        $data['updated_at'] = Carbon::now();
        $data['created_at'] = Carbon::now();
        $data['status'] = $request->status;
        $data['bug_type'] = 0;
        $data['user_id'] = Auth::user()->id;

        $progres = ($data['status'] == 2) ? $request->hidden_slider : 0;
        $data['progres'] = $progres;

        $data['description'] = $request->description;
        $data['parent_id'] = $id;

        $new_bug_item = DB::table('bug_reports')->insertGetId($data);

        //if update succes and is files
        if ($new_bug_item && $request->hasFile('files')){

            $image = new UploadImages();
            $image->procesImage($request->files, $this->module, $new_bug_item, str_random() . '-' . $new_bug_item);

        }

        if($new_bug_item){
            // updatujeme ulohu
            $data = null;

            if($request->status == 1) {
                $data['status'] = 1;

            }elseif($request->status == 2){

                    $data['status'] = 1;
                    $data['progres'] = $progres;

            } else {

                $data['status'] = 10;
                $data['progres'] = 100;
            }

            $data['updated_at'] = Carbon::now();

            DB::table('bug_reports')->where('id', $id)->update($data);


            $email = new EmailNotification();


            //$user_bug_report = User::find($bug_report->user_id);

            //odosleme  kto bug vytvoril a dalsim
            if($request->user){
                $users_id_to_send = collect($request->user);
                $users_id_to_send->push($bug_report->user_id );
            }else{
                $users_id_to_send = [$bug_report->user_id];
            }

            $emails_bug_report = User::whereIn( 'id', $users_id_to_send )->pluck('email')->toArray();

            $content['subject'] = 'Notifikácia o aktivite v BUG REPORT';
            $content['id'] = $bug_report->id;
            $content['url'] = url('developer/bug-report/' . $bug_report->id );


            $text = 'Bola pridaná udalosť k aktivite BUG reports č.' . $bug_report->id . "\n\n";
            $text .= 'Text úlohy.' . $bug_report->description . "\n";

            $content['text'] = $text;

            $res = $email->addNotificationSystemTransaction( $emails_bug_report, $content );

        }

        return redirect()->route('developer.bug-report.index');
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
