<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = [
        'payment_id',
        'payment_type',
        'status',
        'total',
        'products',
        'user_email',
        'user_name',
        'user_document',
        'user_phone',
        'user_birth',
        'address_zip',
        'address_street',
        'address_number',
        'address_district',
        'address_city',
        'address_state',
        'card_last_for_digits',
        'card_brand',
        'installments',
        'installment_value',
    ];
    protected $hidden   = ['payment_id'];
    protected $casts    = [
        'payment_id'        => 'integer',
        'total'             => 'decimal:2',
        'products'          => 'array',
        'user_birth'        => 'date',
        'installments'      => 'integer',
        'installment_value' => 'decimal:2',
    ];
}
