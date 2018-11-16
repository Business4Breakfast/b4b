<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Franchisor extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }


    
    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id','id');
    }

}
