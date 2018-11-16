<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{

    /**
     * The database table name industry used by the model.
     *
     * @var string
     */
    protected $table = 'industries_list';

    protected $fillable = [
        'name', 'description', 'user_id_create', 'active'
    ];
}
