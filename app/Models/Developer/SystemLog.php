<?php

namespace App\Models\Developer;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{

    protected $table = 'system_logs';

    // zapiseme info do systemoveho logu
    public function addRecordToSystemLog($module, $module_id, $transaction="", $description="", $user_id=0  ){

        $system_log = new SystemLog();

        if(strlen($module)>3 && $module_id > 0 && strlen($transaction) > 3){

            $system_log->module = strtolower($module);
            $system_log->module_id = $module_id;
            $system_log->transaction = $transaction;
            $system_log->user_id = $user_id;
            $system_log->description = $description;
            $system_log->log_date = Carbon::now();
            $system_log->day_count = 0;
            $system_log->count = 0;

            $system_log->save();

            return true;

        }else{

            return false;
        }


    }


    // vlozenie logu zgrupnutych info za cely den (napr pocet emailov odoslanych za den)
    public function addDailyCountableEventToSystemLog($module, $id_module, $transaction ){




    }



}
