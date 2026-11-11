<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ✅ PERBAIKAN: Ganti 'category_id' menjadi 'id_category' sesuai kolom di tabel products
    public function products()
    {
        return $this->hasMany(Product::class, 'id_category', 'id');
    }
}
