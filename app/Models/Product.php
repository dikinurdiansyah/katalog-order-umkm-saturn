<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'id_category',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'image_2',
        'image_3',
        'image_4',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // ✅ Helper: Cek apakah produk punya lebih dari 1 foto
    public function hasMultipleImages()
    {
        return !empty($this->image_2) || !empty($this->image_3) || !empty($this->image_4);
    }

    // ✅ Helper: Ambil semua foto yang TIDAK NULL dan TIDAK KOSONG
    public function getAllImages()
    {
        return array_values(array_filter([
            $this->image_url,
            $this->image_2,
            $this->image_3,
            $this->image_4,
        ], function ($value) {
            return !is_null($value) && trim($value) !== '';
        }));
    }

    public function isLowStock()
    {
        return $this->stock <= 10 && $this->stock > 0;
    }

    public function isOutOfStock()
    {
        return $this->stock == 0;
    }
}
