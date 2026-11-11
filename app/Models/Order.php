<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_ref',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'fulfillment_method',
        'promo_id',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_provider',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_ref)) {
                $order->order_ref = 'ORD-'.strtoupper(Str::random(10));
            }
        });
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->orderItems()->sum('subtotal');
        $this->subtotal_amount = $subtotal;

        $discount = 0;
        if ($this->promo) {
            $discount = $this->promo->calculateDiscount($subtotal);
        }
        $this->discount_amount = $discount;
        $this->total_amount = $subtotal - $discount;
    }
}
