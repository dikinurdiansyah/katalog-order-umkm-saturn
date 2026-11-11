<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $item['product'] = $product;

                // ✅ SELF-HEALING: Pastikan field wajib ada
                $item['price'] = $item['price'] ?? $product->price;
                $item['notes'] = $item['notes'] ?? ''; // ✅ Fix: Default notes kosong jika tidak ada

                $item['subtotal'] = $item['price'] * $item['quantity'];
                $total += $item['subtotal'];
                $cartItems[] = $item;
            }
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_active || $product->stock < $validated['quantity']) {
            return back()->with('error', 'Produk tidak tersedia atau stok tidak mencukupi.');
        }

        $cart = session()->get('cart', []);

        // ✅ AMAN: Ambil notes dari request, default string kosong jika tidak ada
        $notes = trim($request->input('notes', ''));

        if (isset($cart[$product->id])) {
            // ✅ SELF-HEALING: Perbaiki data lama yang mungkin korup / kurang field
            if (!isset($cart[$product->id]['price'])) {
                $cart[$product->id]['price'] = $product->price;
            }
            if (!isset($cart[$product->id]['name'])) {
                $cart[$product->id]['name'] = $product->name;
            }
            if (!isset($cart[$product->id]['image'])) {
                $cart[$product->id]['image'] = $product->image_url;
            }
            if (!isset($cart[$product->id]['notes'])) {
                $cart[$product->id]['notes'] = '';
            }

            $newQty = $cart[$product->id]['quantity'] + $validated['quantity'];
            if ($product->stock < $newQty) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            $cart[$product->id]['quantity'] = $newQty;

            // ✅ Update notes HANYA jika user mengisinya saat menambah quantity
            if (!empty($notes)) {
                $cart[$product->id]['notes'] = $notes;
            }
        } else {
            // ✅ SIMPAN DATA LENGKAP DI SESSION UNTUK ITEM BARU
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'notes' => $notes, // ✅ Selalu ada, minimal string kosong
                'image' => $product->image_url,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($id);
            if ($product && $product->stock >= $validated['quantity']) {
                $cart[$id]['quantity'] = $validated['quantity'];

                // ✅ Pastikan notes tetap ada
                if (!isset($cart[$id]['notes'])) {
                    $cart[$id]['notes'] = '';
                }

                session()->put('cart', $cart);
                return back()->with('success', 'Keranjang diperbarui.');
            }

            return back()->with('error', 'Stok tidak mencukupi.');
        }

        return back()->with('error', 'Item tidak ditemukan.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            return back()->with('success', 'Item dihapus dari keranjang.');
        }

        return back()->with('error', 'Item tidak ditemukan.');
    }

    public function clear()
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += intval($item['quantity'] ?? 0);
        }

        return response()->json(['count' => $count]);
    }
}
