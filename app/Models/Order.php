<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {

    use SoftDeletes;

    protected $fillable = [
        'payment_id',
        'payment_type',
        'status',
        'total',
        'total_paid',
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
        'txid',
        'qrcode',
        'imagemQrcode',
        'card_last_for_digits',
        'card_brand',
        'card_installments',
        'card_installment_value',
        'card_charge_total',
        'billet_barcode',
        'billet_link',
        'billet_pdf',
        'paid_at',
        'expire_at',
    ];
    protected $hidden   = ['payment_id'];
    protected $casts    = [
        'payment_id'             => 'integer',
        'total'                  => 'decimal:2',
        'total_paid'             => 'decimal:2',
        'products'               => 'array',
        'user_birth'             => 'date',
        'card_installments'      => 'integer',
        'card_installment_value' => 'decimal:2',
        'card_charge_total'      => 'decimal:2',
        'paid_at'                => 'datetime',
        'expire_at'              => 'date',
    ];
}
