<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'amount_paid',
        'change_amount',
        'receipt_image_path',
        'payment_status',
        'admin_notes',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
