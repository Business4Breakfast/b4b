<?php

namespace App\Models\Invitation;

use App\Models\Event\Event;
use App\Models\Notification\EmailNotification;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Invitation extends Model
{


    // odosleme pozvanku
    public function addNotificationInvitationToCue($user_to, $user_from, $event_id, $attendance_id=0, $invitation_type=0,  $account="default")
    {

        //invitation type
        // 1. pozvanka hostovy
        // 2. pozvanka clenovi

        $module = 'invitation';
        $subject = "";
        $files = null;

        $user_to_send = User::find($user_to);
        $user_from_send = User::find($user_from);
        $event = Event::find($event_id);
        $invitation_type = intval($invitation_type);

        // pozvanky posielame len ked je to v buducnosti
        if(Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) > Carbon::createFromFormat('Y-m-d H:i:s', now() )){

            $subject = "Pozvánka na " . strtolower($event->title);

            // spravy
            $template = 'invitation';

            // sprava pre kazdeho clena
            $em = new EmailNotification();
            $em->subject = $subject;
            $em->html = null;
            $em->text = null;
            $em->account = $account;
            $em->module = $module;
            $em->date_send = Carbon::createFromFormat('Y-m-d H:i:s', now()->addMinute(1))->format('Y-m-d') . ' 08:30:00';
            $em->files = $files;
            $em->module_id = $event_id;
            $em->data = json_encode(['template' => $template,
                'user_from' => $user_from_send->only('id'),
                'user_to' => $user_to_send->only('id'),
                'event' => $event,
                'club' => $event->club_id,
                'attendance' => $attendance_id,
                'invitation_type' => $invitation_type,
            ]);

            $em->recipients = json_encode([$user_to_send->email]);
            $em->save();

        }

        return true;
    }



    public function addUserAttendanceList($event, $user, $invite_user, $description, $status=1, $invitation_type=1)
    {

        $data['user_id'] = $user;
        $data['user_invite_id'] = $invite_user;
        $data['event_id'] = $event;
        $data['description'] = $description;
        $data['status'] = intval($status);
        $data['user_status_id'] = User::find($user)->status;
        $data['pay'] = 0;
        $data['invitation_type'] = intval($invitation_type);

        // aby sa nedal pozvat 2x ten isty
        $duplicity = DB::table('attendance_lists')
            ->where('event_id', $event )
            ->where('user_id', $user)
            ->count();

        if ($duplicity == 0 )  {
            return DB::table('attendance_lists')->insertGetId($data);
        } else {
            return null;
        }
    }



}
