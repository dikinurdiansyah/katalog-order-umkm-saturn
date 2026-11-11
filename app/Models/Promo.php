<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_amount',
        'min_order_amount',
        'is_active',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'valid_until' => 'date',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'promo_id', 'id');
    }

    public function isValid(): bool
    {
        return $this->is_active && $this->valid_until >= Carbon::now()->startOfDay();
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->isValid() || $subtotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->discount_type === 'PERCENT') {
            return ($subtotal * $this->discount_amount) / 100;
        }

        return $this->discount_amount;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_until', '>=', Carbon::now()->startOfDay());
    }
}
