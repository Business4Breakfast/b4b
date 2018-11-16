<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class InvoiceText extends Model
{
    /**
     * The database table name industry used by the model.
     *
     * @var string
     */
    protected $table = 'invoice_text';

    protected $fillable = [
        'name', 'description', 'user_id_create', 'active'
    ];


}
