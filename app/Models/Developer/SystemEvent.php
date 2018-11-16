<?php

namespace App\Models\Developer;

use App\Models\Membership;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemEvent extends Model
{


    // kontrolujeme clenstvo ci este neulynulo, ak ano prepneme do stavu neaktivne
    // zneaktívnime uzivatela
    // vygenerujeme nove heslo
    public function setInactiveExpiredMembership()
    {

        $system_log_class = new SystemLog();
        $membership_class = new Membership();

        $expired_membership = Membership::where('active', 1)
                        ->where('valid_to', '<' , Carbon::now())
                        ->get();

        //ak najdeme take
        if ($expired_membership->count() > 0){
            foreach ($expired_membership as $em){

                // nastavime ako neaktivne
                $em->active = 0;
                $em->save();

                // zapiseme do systemoveho logu
                $log_transaction = "Expired membership: " . $em->id;
                $system_log_class->addRecordToSystemLog('membership', $em->id, $log_transaction, $log_transaction);

                // zapiseme do uhrad memberships uplynutie clenstva
                $data['membership_id'] = $em->id;
                $data['date_payment'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['description'] = $log_transaction;
                $data['payment_type'] = 6; //uplynutie clenstva
                $data['user_id'] = 0;
                DB::table('membership_payment')->insert($data);

                //zistime ci nemaju este uzivatelia k clenstvu ine aktívne clenstvo
                // ak nema vypneme mu pístup
                if ($em->user->count() > 0){
                    foreach ($em->user as $emu){
                        //prejdeme jednotlivych uzivatelov clenstva okrem expirovaneho
                        $membersips_user = $membership_class->getAllMembershipForUser($emu->id, 1, $em->id);

                        //TODO odobrat prava ak je clenov vykonneho timu alebo manager

                        // ak sa nema ine clenstvo
                        if ($membersips_user->count() > 0){
                            //dump('ma ine clenstvo');
                        }else{
                            // nema ine clenstvo vypneme
                            $user = User::find($emu->id);
                            // vypneme pristup do adminu a nastavime ako hosta
                            $user->admin = 0;
                            $user->status = 5;
                            $user->password = str_random(12);
                            //$user->save();

                            // zapiseme do systemoveho logu
                            $log_transaction = "Change user to guest and reset password: " . $em->id;
                            $system_log_class->addRecordToSystemLog('user', $emu->id, $log_transaction, $log_transaction);

                        }

                    }
                }

            }

            return true;

        }

        return false;

    }


}