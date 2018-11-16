<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{

     /**
     * The database table name industry used by the model.
     *
     * @var string
     */
    protected $table = 'interests_list';

    protected $fillable = [
        'name', 'description', 'user_id_create', 'active'
    ];

}

