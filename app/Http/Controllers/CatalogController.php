<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // ✅ AMBIL DATA LOGO & SETTINGS DARI DATABASE
        $settings = Setting::firstWhere('key', 'outlet_info');
        $siteData = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
        $logoUrl = $siteData['logo_url'] ?? null;
        $businessName = $siteData['business_name'] ?? 'DAPUR TH SANT';

        // ✅ AMAN: Menggunakan where() agar tidak error jika scope active()/inStock() belum dibuat di Model
        $query = Product::where('is_active', true)
                        ->where('stock', '>', 0)
                        ->with('category');

        // ✅ PERBAIKAN: category_id → id_category (sesuai nama kolom di database)
        if ($request->filled('category')) {
            $query->where('id_category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest(); // Urutkan berdasarkan terbaru
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        return view('catalog.index', compact('products', 'categories', 'logoUrl', 'businessName'));
    }

    public function show(Product $product)
    {
        // Jika produk tidak aktif, tampilkan 404
        if (!$product->is_active) {
            abort(404);
        }

        // ✅ AMBIL DATA LOGO & SETTINGS UNTUK HALAMAN DETAIL PRODUK JUGA
        $settings = Setting::firstWhere('key', 'outlet_info');
        $siteData = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
        $logoUrl = $siteData['logo_url'] ?? null;
        $businessName = $siteData['business_name'] ?? 'DAPUR TH SANT';

        $product->load('category');

        // ✅ PERBAIKAN: category_id → id_category (sesuai nama kolom di database)
        // Related products (produk sejenis yang masih aktif dan ada stoknya)
        $relatedProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->where('id_category', $product->id_category) // ✅ DIPERBAIKI
            ->where('id', '!=', $product->id)
            ->inRandomOrder() // Acak agar variatif
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts', 'logoUrl', 'businessName'));
    }
}
