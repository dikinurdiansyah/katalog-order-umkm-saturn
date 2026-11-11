<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dapur TH Sant')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 20px); }
    </style>
</head>
<body class="text-[#4A3320] pb-28 md:pb-10 min-h-screen relative" x-data="cartSystem()">

    <!-- HEADER GLOBAL (Desktop & Mobile) -->
    <header class="sticky top-0 z-40 bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EBE3D5] shadow-sm">
        <div class="max-w-7xl mx-auto px-5 md:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-white border border-[#EBE3D5] rounded-full flex items-center justify-center shadow-sm">
                    <img src="https://ui-avatars.com/api/?name=TH&background=4A3320&color=fff" alt="Logo" class="w-8 h-8 md:w-9 md:h-9 rounded-full">
                </div>
                <div class="font-extrabold text-[15px] md:text-[18px] tracking-[0.1em] uppercase text-[#312215]">Dapur TH Sant</div>
            </div>

            <div class="flex items-center gap-3">
                <button @click="isCartOpen = true" class="hidden md:flex items-center gap-2 bg-white border border-[#EBE3D5] px-4 py-2 rounded-full font-bold text-[#4A3320] shadow-sm hover:bg-[#F4EFE7] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Keranjang</span>
                    <span x-show="totalItems() > 0" x-text="totalItems()" class="bg-[#D16942] text-white px-2 py-0.5 rounded-full text-[10px] ml-1" style="display: none;"></span>
                </button>

                <a href="{{ route('login') }}" class="w-9 h-9 md:w-10 md:h-10 bg-[#4A3320] text-white rounded-full flex items-center justify-center shadow-sm hover:bg-[#342519] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- AREA KONTEN DINAMIS -->
    @yield('content')

    <!-- FLOATING CART BAWAH (Hanya muncul di HP saat ada item) -->
    <div x-show="totalItems() > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-[85px] md:bottom-8 left-0 md:left-auto md:right-8 w-full md:w-[400px] flex justify-center px-4 md:px-0 z-40" style="display: none;">
        <button @click="isCartOpen = true" class="w-full max-w-md md:max-w-full bg-[#4A3320] text-white rounded-[18px] md:rounded-[24px] p-3 md:p-4 flex justify-between items-center shadow-[0_10px_25px_rgba(74,51,32,0.3)] hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-3 pl-1">
                <div class="relative">
                    <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span x-text="totalItems()" class="absolute -top-1.5 -right-2 md:-right-3 bg-[#D16942] text-white text-[10px] md:text-[12px] w-5 h-5 md:w-6 md:h-6 flex items-center justify-center rounded-full font-bold border-2 border-[#4A3320]"></span>
                </div>
                <div class="text-left ml-2">
                    <span class="block text-[10px] md:text-[11px] font-medium text-white/80 mb-0.5" x-text="totalItems() + ' MENU DIPILIH'"></span>
                    <span class="font-extrabold text-[15px] md:text-[18px]" x-text="'Rp ' + subtotal().toLocaleString('id-ID')"></span>
                </div>
            </div>
            <div class="bg-white text-[#4A3320] text-[11px] md:text-[13px] font-extrabold px-4 py-2.5 md:py-3 rounded-full flex items-center gap-1">
                Lihat <span class="hidden md:inline">Keranjang</span> <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>
    </div>

    <!-- BOTTOM NAVIGATION (Hanya di HP) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white flex justify-center z-30 shadow-[0_-5px_15px_rgba(0,0,0,0.03)] border-t border-[#F2EBE1]">
        <div class="w-full max-w-md flex justify-around py-3 pb-safe">
            <a href="{{ route('catalog.index') }}" class="flex flex-col items-center {{ request()->routeIs('catalog.index') ? 'text-[#4A3320]' : 'text-[#A5998D] hover:text-[#4A3320]' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">KATALOG</span>
            </a>
            <button @click="isCartOpen = true" class="flex flex-col items-center relative text-[#A5998D] hover:text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">KERANJANG</span>
                <span x-show="totalItems() > 0" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-[#D16942] rounded-full border border-white" style="display: none;"></span>
            </button>
            <a href="#" class="flex flex-col items-center text-[#A5998D] hover:text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">STATUS</span>
            </a>
        </div>
    </nav>

    <!-- DRAWER KERANJANG UTAMA (Desain Sesuai p3.png) -->
    <div x-show="isCartOpen" style="display: none;" class="relative z-50">
        <div x-show="isCartOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="isCartOpen = false"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-md w-full md:w-[450px]">
            <div x-show="isCartOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-full bg-[#FDFBF7] h-full flex flex-col shadow-2xl">

                <!-- Header Keranjang -->
                <div class="flex justify-between items-center p-5 bg-white border-b border-[#EBE3D5] shadow-sm z-10">
                    <h2 class="font-black text-[15px] text-[#312215] tracking-widest uppercase flex items-center gap-3">
                        <div class="w-8 h-8 bg-[#F4EFE7] rounded-md flex items-center justify-center font-extrabold text-[#A5998D] text-[11px]">WF</div>
                        KERANJANG
                    </h2>
                    <button @click="isCartOpen = false" class="w-9 h-9 flex items-center justify-center rounded-full bg-[#F4EFE7] text-[#A5998D] hover:text-[#4A3320] hover:bg-[#EBE3D5] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Area Scroll Isi Keranjang -->
                <div class="flex-1 overflow-y-auto pb-6">

                    <!-- Kondisi Saat Keranjang Kosong -->
                    <template x-if="cart.length === 0">
                        <div class="p-8 flex flex-col items-center justify-center text-center h-full mt-10">
                            <div class="w-20 h-20 bg-[#F4EFE7] rounded-full flex items-center justify-center mb-5">
                                <svg class="w-10 h-10 text-[#DBCAB7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <p class="text-[14px] font-black text-[#312215] uppercase tracking-widest mb-1">Keranjang Kosong</p>
                            <p class="text-[12px] font-medium text-[#A5998D] mb-6">Belum ada menu yang dipilih untuk dipesan.</p>
                            <button @click="isCartOpen = false" class="bg-[#4A3320] text-white px-6 py-3.5 rounded-xl text-[11px] font-extrabold uppercase tracking-widest shadow-sm hover:bg-[#342519] transition">Mulai Belanja</button>
                        </div>
                    </template>

                    <!-- Kondisi Saat Ada Item -->
                    <template x-if="cart.length > 0">
                        <div class="p-4 md:p-5">

                            <!-- Progress Ongkir -->
                            <div class="bg-white border border-[#EBE3D5] rounded-[16px] p-4 mb-5 shadow-sm">
                                <div class="flex justify-between items-center text-[10px] font-extrabold uppercase tracking-widest mb-2.5">
                                    <span class="text-[#312215] flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-[#A5998D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        GRATIS ONGKIR: TAMBAH RP 26.000 LAGI
                                    </span>
                                    <span class="text-[#A5998D]">67%</span>
                                </div>
                                <div class="w-full bg-[#F4EFE7] rounded-full h-2.5 mb-2.5 overflow-hidden border border-[#EBE3D5]">
                                    <div class="bg-[#4A3320] h-full rounded-full" style="width: 67%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold text-[#7A6C60]">
                                    <span x-text="'Rp ' + subtotal().toLocaleString('id-ID')"></span>
                                    <span>Target: Rp 80.000</span>
                                </div>
                            </div>

                            <!-- List Item -->
                            <div class="flex justify-between items-center mb-4 px-1">
                                <label class="flex items-center gap-2.5 text-[12px] font-extrabold text-[#312215] cursor-pointer">
                                    <div class="w-5 h-5 bg-[#4A3320] rounded-[6px] flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span x-text="'Pilih Semua (' + totalItems() + ' Item)'"></span>
                                </label>
                            </div>

                            <div class="space-y-3.5 mb-6">
                                <template x-for="item in cart" :key="item.id">
                                    <div class="bg-white border border-[#EBE3D5] rounded-[18px] p-4 shadow-sm flex gap-3.5 relative">
                                        <div class="pt-1.5">
                                            <div class="w-5 h-5 bg-[#4A3320] rounded-[6px] flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>

                                        <div class="w-[72px] h-[72px] bg-[#F4EFE7] border border-[#EBE3D5] rounded-xl flex items-center justify-center text-[11px] font-bold text-[#A5998D] flex-shrink-0 relative overflow-hidden">
                                            <span class="relative z-10 tracking-widest">[ X ]</span>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <h4 class="text-[13px] font-extrabold text-[#312215] leading-snug pr-8 line-clamp-1" x-text="item.name"></h4>
                                                <button @click="removeFromCart(item.id)" class="absolute top-4 right-4 text-[#A5998D] hover:text-[#D16942] transition">
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            <div class="inline-block bg-[#F4E9D8] text-[#C27E4B] text-[9px] px-2 py-0.5 rounded mt-1.5 font-bold">Menu Favorit</div>
                                            <p class="text-[10px] text-[#7A6C60] font-medium mt-1.5" x-text="'@ Rp ' + item.price.toLocaleString('id-ID') + ' / porsi'"></p>

                                            <div class="flex justify-between items-end mt-3 border-t border-[#F2EBE1] pt-3">
                                                <div>
                                                    <span class="block text-[8px] text-[#A5998D] font-extrabold uppercase tracking-widest mb-0.5">Subtotal</span>
                                                    <span class="text-[14px] font-black text-[#312215]" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="flex items-center border border-[#EBE3D5] rounded-[10px] bg-white shadow-[0_2px_4px_rgba(0,0,0,0.02)] overflow-hidden">
                                                    <button @click="minQty(item.id)" class="w-8 h-8 flex items-center justify-center text-[#7A6C60] hover:bg-[#F4EFE7] hover:text-[#4A3320] font-bold transition">-</button>
                                                    <span class="w-8 h-8 flex items-center justify-center text-[12px] font-black text-[#312215] border-x border-[#EBE3D5]" x-text="item.qty"></span>
                                                    <button @click="addQty(item.id)" class="w-8 h-8 flex items-center justify-center text-[#7A6C60] hover:bg-[#F4EFE7] hover:text-[#4A3320] font-bold transition">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Voucher -->
                            <div class="bg-white border border-[#EBE3D5] rounded-[16px] p-4 flex items-center justify-between shadow-sm mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-[#F4EFE7] rounded-[10px] flex items-center justify-center text-[#312215]">
                                        <svg class="w-5 h-5 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[12px] font-extrabold text-[#312215]">Voucher Diskon UMKM</h4>
                                        <p class="text-[10px] font-medium text-[#A5998D]">Hemat hingga Rp 10.000</p>
                                    </div>
                                </div>
                                <div class="bg-[#F4E9D8] text-[#C27E4B] text-[10px] font-extrabold uppercase tracking-wide px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                    1 Promo Siap Pakai <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>

                            <!-- Ringkasan -->
                            <div class="bg-white border border-[#EBE3D5] rounded-[16px] p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4 border-b border-[#EBE3D5] pb-3">
                                    <h3 class="font-extrabold text-[13px] text-[#312215]">Ringkasan Pembayaran</h3>
                                    <span class="text-[9px] font-bold text-[#A5998D] uppercase tracking-widest">DRAFT PO-0924</span>
                                </div>
                                <div class="space-y-2.5 text-[12px] font-medium text-[#7A6C60] mb-4">
                                    <div class="flex justify-between">
                                        <span>Subtotal Produk</span>
                                        <span class="font-bold text-[#312215]" x-text="'Rp ' + subtotal().toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Biaya Kemasan Higienis</span>
                                        <span class="font-bold text-[#312215]">Rp 2.000</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Diskon Promo</span>
                                        <span class="font-bold text-[#312215]">-Rp 0</span>
                                    </div>
                                </div>
                                <div class="bg-[#F4EFE7] rounded-xl p-3.5 flex justify-between items-center">
                                    <div>
                                        <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#4A3320]">TOTAL SEMENTARA</span>
                                        <span class="text-[10px] font-medium text-[#7A6C60]">Termasuk Pajak Resto</span>
                                    </div>
                                    <span class="font-black text-[16px] text-[#312215]" x-text="'Rp ' + (subtotal() + 2000).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Tombol Konfirmasi -->
                <div x-show="cart.length > 0" class="bg-[#FDFBF7] border-t border-[#EBE3D5] p-5 shadow-[0_-10px_20px_rgba(0,0,0,0.03)] pb-safe z-10">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <span class="block text-[9px] font-extrabold uppercase tracking-widest text-[#A5998D] mb-0.5">Total Pembayaran</span>
                            <div class="flex items-baseline gap-1">
                                <span class="font-black text-[18px] text-[#312215]" x-text="'Rp ' + (subtotal() + 2000).toLocaleString('id-ID')"></span>
                                <span class="text-[10px] font-medium text-[#7A6C60]" x-text="'(' + totalItems() + ' Porsi)'"></span>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="bg-[#4A3320] text-white px-6 py-4 rounded-[14px] text-[13px] font-extrabold flex items-center gap-2.5 hover:bg-[#342519] transition-colors shadow-sm whitespace-nowrap">
                            Lanjut ke Checkout
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- LOGIKA LOCAL STORAGE ALPINE -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartSystem', () => ({
                isCartOpen: false,
                cart: JSON.parse(localStorage.getItem('thsant_cart') || '[]'),
                init() {
                    this.$watch('cart', (val) => {
                        localStorage.setItem('thsant_cart', JSON.stringify(val));
                    });
                },
                addToCart(id, name, price) {
                    let item = this.cart.find(i => i.id === id);
                    if (item) item.qty++;
                    else this.cart.push({ id, name, price, qty: 1 });
                    if(navigator.vibrate) navigator.vibrate(50);
                },
                removeFromCart(id) {
                    this.cart = this.cart.filter(i => i.id !== id);
                },
                addQty(id) {
                    let item = this.cart.find(i => i.id === id);
                    if (item) item.qty++;
                },
                minQty(id) {
                    let item = this.cart.find(i => i.id === id);
                    if (item && item.qty > 1) item.qty--;
                },
                totalItems() { return this.cart.reduce((total, item) => total + item.qty, 0); },
                subtotal() { return this.cart.reduce((total, item) => total + (item.price * item.qty), 0); }
            }))
        })
    </script>
</body>
</html>
