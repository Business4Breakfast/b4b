<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = [
        'company_name',
        'title',
        'contact_person',
        'address_street',
        'address_psc',
        'address_city',
        'address_country',
        'ico', 'dic', 'ic_dph',
        'phone', 'email', 'url',
        'registration',
        'description',
    ];

    public function getImageThumbAttribute() {

        if(strlen($this->image) > 0){
            $path = asset('images/company'). '/' . $this->id .'/small/'.$this->image;
        } else {
            $path = asset('images/default'). '/sq/default_alpha.png';
        }

        return $path;
    }

}

