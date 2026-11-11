<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Dapur TH Sant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F9F7F3] min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="max-w-md w-full bg-white border border-[#E8E5DF] shadow-sm p-8 text-center">
        <div class="w-16 h-16 bg-teal-50 border border-teal-200 text-teal-700 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>

        <h1 class="text-2xl font-black text-slate-900 mb-2">Pesanan Diterima!</h1>
        <p class="text-xs text-slate-500 font-mono mb-6">Terima kasih, pesanan Anda sedang kami tinjau.</p>

        <div class="bg-[#F6F4ED] border border-[#E8E5DF] p-4 mb-6">
            <p class="text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-1">KODE REFERENSI PESANAN</p>
            <p class="text-xl font-bold font-mono text-[#4A3728]">{{ $order->order_ref }}</p>
        </div>

        <a href="{{ route('catalog.index') }}" class="block w-full border border-slate-300 text-slate-700 text-xs font-mono font-bold py-3 hover:bg-slate-50 transition">
            [ KEMBALI KE KATALOG ]
        </a>
    </div>

</body>
</html>
