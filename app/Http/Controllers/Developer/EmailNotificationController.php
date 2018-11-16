<?php

namespace App\Http\Controllers\Developer;

use App\Models\Notification\EmailNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailNotificationController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
//                ['name' => 'Pridať nový záznam', 'link' => route(''), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('developer.email-notifications.index'), 'icon' => 'list', 'class' => 'btn-success']
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
        $query = DB::table('email_notifications')->orderByDesc('id');

        if (strlen($request->search_email) > 3){

             $req['search_email'] = $request->search_email;

             $query->where('recipients', 'like', '%"' . $request->search_email . '"%');
        } else {
            $req['search_email'] = "";

        }

        $items = $query->paginate(20);

        $links = $items->links();

        $items->each(function ($item, $key) {

            if (is_array(json_decode($item->recipients))) {
                $item->recipients_string = implode("\n", json_decode($item->recipients));
            }else{
                $item->recipients_string = json_decode($item->recipients);
            }
        });

        return view('developer.email_notification.index')
            ->with('links', $links)
            ->with('req', $req)
            ->with('items',$items);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = DB::table('email_notifications')->find($id);

        return view('developer.email_notification.edit')
                    ->with('item', $item);

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
        $data = $request->only(['recipients','data','subject','date_send',
                                    'updated_at', 'created_at', 'status',
                                    'html', 'text', 'module_id', 'module' ]);


        DB::table('email_notifications')->where('id', $id)->update($data);

        if($request->send_imediately == 1){

            $this->sendEmail();

        }

        return redirect()->route('developer.email-notifications.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('email_notifications')->delete($id);

        return redirect()->route('developer.email-notifications.index');
    }


    public function sendEmail()
    {

        $email = new EmailNotification();
        $res_email = $email->procesEmailNotification();

        Log::info($res_email);

        return redirect()->route('developer.email-notifications.index')
                    ->with('message', print_r($res_email));


    }



}
