<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Dapur TH Sant</title>
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

    <!-- HEADER (Responsif: Di HP ringkas, Di Laptop lengkap dengan tombol keranjang) -->
    <header class="sticky top-0 z-40 bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EBE3D5] shadow-sm">
        <div class="max-w-7xl mx-auto px-5 md:px-8 py-4 flex justify-between items-center">
            <!-- Logo & Title -->
            <div class="flex items-center gap-3 md:gap-4">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-white border border-[#EBE3D5] rounded-full flex items-center justify-center shadow-sm">
                    <img src="https://ui-avatars.com/api/?name=TH&background=4A3320&color=fff" alt="Logo" class="w-8 h-8 md:w-9 md:h-9 rounded-full">
                </div>
                <div class="font-extrabold text-[15px] md:text-[18px] tracking-[0.1em] uppercase text-[#312215]">Dapur TH Sant</div>
            </div>

            <!-- Aksi Header (Keranjang & User) -->
            <div class="flex items-center gap-3">
                <!-- Tombol Keranjang (Hanya Desktop, di HP pakai Bottom Nav) -->
                <button @click="isCartOpen = true" class="hidden md:flex items-center gap-2 bg-white border border-[#EBE3D5] px-4 py-2 rounded-full font-bold text-[#4A3320] shadow-sm hover:bg-[#F4EFE7] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Keranjang</span>
                    <span x-show="totalItems() > 0" x-text="totalItems()" class="bg-[#D16942] text-white px-2 py-0.5 rounded-full text-[10px] ml-1"></span>
                </button>

                <!-- Tombol Login/User -->
                <a href="{{ route('login') }}" class="w-9 h-9 md:w-10 md:h-10 bg-[#4A3320] text-white rounded-full flex items-center justify-center shadow-sm hover:bg-[#342519] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA (Max-width 7xl agar rapi di Laptop) -->
    <main class="max-w-7xl mx-auto px-5 md:px-8 py-5 md:py-8">

        <!-- PROMO BANNER (Responsif: Melebar rapi di Desktop) -->
        <div class="bg-gradient-to-br from-[#F4E9D8] to-[#EBE0CD] rounded-[20px] md:rounded-[24px] p-5 md:p-8 mb-6 md:mb-8 relative overflow-hidden shadow-sm flex flex-col justify-center min-h-[160px] md:min-h-[220px]">
            <div class="relative z-10 md:max-w-lg">
                <span class="inline-block px-3 py-1 bg-white text-[#C27E4B] font-extrabold text-[9px] md:text-[11px] uppercase rounded-full mb-3 md:mb-4 tracking-wider shadow-sm">PROMO HARIAN</span>
                <h2 class="text-[17px] md:text-[28px] font-black mb-1.5 md:mb-3 leading-snug pr-12 md:pr-0 text-[#312215]">Dimsum Fresh Kukus Siap Kirim</h2>
                <p class="text-[11px] md:text-[14px] text-[#7A6C60] mb-4 md:mb-6 max-w-[200px] md:max-w-md leading-relaxed font-medium">Dibuat hangat tiap hari dari daging ayam pilihan tanpa pengawet.</p>
                <button class="bg-[#4A3320] text-white px-5 md:px-6 py-2.5 md:py-3 rounded-xl md:rounded-2xl text-[11px] md:text-[13px] font-bold flex items-center gap-1.5 hover:bg-[#342519] transition w-max">
                    Pesan Sekarang <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
            <!-- Dekorasi Bulat -->
            <div class="absolute -right-12 -bottom-12 md:right-10 md:-bottom-20 w-44 h-44 md:w-80 md:h-80 border-[4px] border-[#DBCAB7] rounded-full opacity-30"></div>
            <div class="absolute -right-4 -bottom-4 md:right-28 md:bottom-10 w-28 h-28 md:w-48 md:h-48 border-[4px] border-[#DBCAB7] rounded-full opacity-30"></div>
        </div>

        <!-- SEARCH BAR & FILTER (Responsif: Sejajar di Desktop, Atas-Bawah di HP) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
            <!-- Search Bar -->
            <div class="relative w-full md:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" class="w-full pl-10 md:pl-12 pr-4 py-3.5 md:py-3 bg-white border border-[#EBE3D5] rounded-2xl md:rounded-full text-[12px] md:text-[14px] font-medium focus:outline-none focus:border-[#4A3320] placeholder-slate-400 shadow-sm" placeholder="Cari menu...">
            </div>

            <!-- Filter Kategori -->
            <div class="flex gap-2.5 overflow-x-auto no-scrollbar w-full md:w-auto pb-1 md:pb-0">
                <button class="flex-shrink-0 bg-[#4A3320] text-white px-5 py-2.5 md:py-2.5 rounded-full text-[12px] md:text-[13px] font-extrabold shadow-sm">Semua <span class="font-normal text-white/70 ml-0.5">(12)</span></button>
                <button class="flex-shrink-0 bg-white border border-[#EBE3D5] text-[#4A3320] px-5 py-2.5 md:py-2.5 rounded-full text-[12px] md:text-[13px] font-bold shadow-sm hover:bg-[#F4EFE7]">Siomay Kukus <span class="font-normal text-slate-400 ml-0.5">(5)</span></button>
                <button class="flex-shrink-0 bg-white border border-[#EBE3D5] text-[#4A3320] px-5 py-2.5 md:py-2.5 rounded-full text-[12px] md:text-[13px] font-bold shadow-sm hover:bg-[#F4EFE7]">Dimsum Goreng <span class="font-normal text-slate-400 ml-0.5">(4)</span></button>
            </div>
        </div>

        <!-- HEADER SEKSI PRODUK -->
        <div class="flex justify-between items-end mb-4 px-1 border-b border-[#EBE3D5] pb-2">
            <h3 class="font-extrabold text-[12px] md:text-[14px] uppercase tracking-widest text-[#312215]">KATALOG PRODUK</h3>
            <span class="text-[10px] md:text-[12px] text-[#7A6C60] font-medium">Tersedia: 4 Item Aktif</span>
        </div>

        <!-- GRID PRODUK (Responsif: 2 Kolom HP, 3 Kolom Tablet, 4 Kolom Laptop) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 md:gap-6">

            @foreach($categories as $category)
                @foreach($category->products as $product)
                <div class="bg-white border border-[#EBE3D5] rounded-[18px] md:rounded-[24px] p-3 md:p-4 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow group">

                    <!-- Gambar Placeholder -->
                    <div class="bg-[#F4EFE7] aspect-[4/5] md:aspect-square rounded-xl md:rounded-2xl mb-3 md:mb-4 flex flex-col items-center justify-center relative overflow-hidden group-hover:bg-[#EBE0CD] transition-colors">
                        <div class="w-10 h-10 md:w-14 md:h-14 bg-white rounded-full flex items-center justify-center shadow-sm mb-2 md:mb-3">
                            <span class="text-[14px] md:text-[20px]">🍣</span>
                        </div>
                        <div class="text-[9px] md:text-[11px] font-bold text-[#A5998D] uppercase tracking-wider text-center">FOTO PRODUK<br><span class="font-medium text-[8px] md:text-[10px] opacity-70">({{ strtoupper(Str::limit($product->slug, 8, '')) }})</span></div>
                    </div>

                    <!-- Badge Status & Rating -->
                    <div class="flex justify-between items-center mb-2.5">
                        <span class="bg-[#E7F3EB] text-[#287A4F] px-1.5 py-0.5 rounded text-[8px] md:text-[10px] font-extrabold uppercase tracking-widest">READY STOCK</span>
                        <span class="flex items-center text-[10px] md:text-[12px] font-extrabold text-[#4A3320]"><span class="text-[#ECA538] mr-1 text-[11px] md:text-[14px]">★</span> 4.9</span>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <div class="flex-grow">
                        <h4 class="font-extrabold text-[12px] md:text-[15px] leading-snug mb-1 md:mb-2 text-[#312215] line-clamp-2 group-hover:text-[#C27E4B] transition-colors">{{ $product->name }}</h4>
                        <p class="text-[10px] md:text-[12px] text-[#8F8377] mb-3 md:mb-4 line-clamp-2 leading-relaxed font-medium">{{ $product->description }}</p>
                    </div>

                    <!-- Harga & Tombol Add -->
                    <div class="flex justify-between items-end mt-1 border-t border-[#F2EBE1] pt-3">
                        <div>
                            <span class="block text-[8px] md:text-[10px] text-[#8F8377] uppercase tracking-widest mb-0.5 font-bold">HARGA</span>
                            <span class="font-extrabold text-[13px] md:text-[16px] text-[#312215]">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <!-- Tombol Kotak (Sesuai p1.jpg) -->
                        <button @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                                class="w-8 h-8 md:w-10 md:h-10 bg-[#4A3320] text-white rounded-lg md:rounded-xl flex items-center justify-center hover:bg-[#342519] transition active:scale-90 shadow-sm">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            @endforeach

        </div>
    </main>

    <!-- FLOATING CART SUMMARY (Responsif) -->
    <!-- Di HP: Melayang di tengah bawah. Di Desktop: Melayang di pojok kanan bawah -->
    <div x-show="totalItems() > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-[85px] md:bottom-8 left-0 md:left-auto md:right-8 w-full md:w-[400px] flex justify-center px-4 md:px-0 z-40" style="display: none;">

        <button @click="isCartOpen = true" class="w-full max-w-md md:max-w-full bg-[#4A3320] text-white rounded-[18px] md:rounded-[24px] p-3 md:p-4 flex justify-between items-center shadow-[0_10px_25px_rgba(74,51,32,0.3)] hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-3 pl-1">
                <div class="relative">
                    <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <!-- Badge -->
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

    <!-- BOTTOM NAVIGATION BAR (Hanya Tampil di HP/Mobile) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white flex justify-center z-30 shadow-[0_-5px_15px_rgba(0,0,0,0.03)] border-t border-[#F2EBE1]">
        <div class="w-full max-w-md flex justify-around py-3 pb-safe">
            <a href="#" class="flex flex-col items-center text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">KATALOG</span>
            </a>
            <button @click="isCartOpen = true" class="flex flex-col items-center text-[#A5998D] hover:text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">KERANJANG</span>
            </button>
            <a href="#" class="flex flex-col items-center text-[#A5998D] hover:text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">STATUS</span>
            </a>
            <a href="#" class="flex flex-col items-center text-[#A5998D] hover:text-[#4A3320]">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                <span class="text-[9px] font-extrabold uppercase tracking-widest">KONTAK</span>
            </a>
        </div>
    </nav>

    <!-- DRAWER KERANJANG SEMENTARA (Akan kita desain sesuai p3.png nanti) -->
    <div x-show="isCartOpen" style="display: none;" class="relative z-50">
        <div x-show="isCartOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="isCartOpen = false"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-md w-full md:w-[450px]">
            <div class="w-full bg-[#FDFBF7] h-full flex flex-col shadow-2xl">
                <div class="flex justify-between items-center p-6 bg-white border-b border-[#F2EBE1]">
                    <h2 class="font-extrabold text-[16px] text-[#4A3320] tracking-widest">KERANJANG</h2>
                    <button @click="isCartOpen = false" class="text-3xl font-bold text-[#A5998D] hover:text-[#4A3320] transition">&times;</button>
                </div>
                <div class="p-6 flex-1 overflow-auto flex flex-col justify-center items-center text-center">
                    <svg class="w-16 h-16 text-[#EBE3D5] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-sm font-bold text-[#A5998D]">Silakan lanjut ke sesi berikutnya<br>untuk mendesain tampilan Checkout (p3 & p4).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGIKA ALPINE.JS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartSystem', () => ({
                isCartOpen: false,
                cart: JSON.parse(localStorage.getItem('thsant_cart') || '[]'),
                init() {
                    this.$watch('cart', (val) => { localStorage.setItem('thsant_cart', JSON.stringify(val)); });
                },
                addToCart(id, name, price) {
                    let item = this.cart.find(i => i.id === id);
                    if (item) item.qty++;
                    else this.cart.push({ id, name, price, qty: 1 });
                },
                totalItems() { return this.cart.reduce((total, item) => total + item.qty, 0); },
                subtotal() { return this.cart.reduce((total, item) => total + (item.price * item.qty), 0); }
            }))
        })
    </script>
</body>
</html>
