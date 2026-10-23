<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Inventory &amp; Sales</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --mist:   #f6f1ff;   /* latar halaman */
            --paper:  #ffffff;
            --plum:   #2b2140;   /* teks utama */
            --muted:  #7b7194;
            --line:   #ece4fb;
            --lilac:  #e8deff;
            --violet: #7a5cf0;   /* aksen utama */
            --violet-d: #6446d8;
            --petal:  #ffe4f2;
            --bubble: #ff7fbd;   /* aksen peringatan */
            --bubble-d: #d9407f;
            --sage:   #dff5ea;
            --sage-d: #1f7a52;

            --bs-body-font-family: 'DM Sans', system-ui, sans-serif;
            --bs-body-color: var(--plum);
            --bs-body-bg: var(--mist);
            --bs-primary: var(--violet);
            --bs-primary-rgb: 122, 92, 240;
            --bs-link-color: var(--violet-d);
            --bs-link-hover-color: var(--plum);
            --bs-border-color: var(--line);
        }

        body {
            background:
                radial-gradient(900px 500px at 105% -10%, #ffdff0 0%, transparent 60%),
                radial-gradient(700px 480px at -10% 110%, #e3d7ff 0%, transparent 60%),
                var(--mist);
            background-attachment: fixed;
            color: var(--plum);
        }

        h1, .h1, h2, .h2, h3, .h3, .brand-name {
            font-family: 'Bricolage Grotesque', 'DM Sans', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        h1.h3 { font-size: 1.9rem; }

        /* ---------- Shell ---------- */
        .shell { display: flex; min-height: 100vh; }
        .content { flex: 1; min-width: 0; }
        .content-inner { max-width: 1180px; margin: 0 auto; padding: 2rem 1.5rem 3rem; }

        /* ---------- Sidebar ---------- */
        .sidebar-inner {
            display: flex; flex-direction: column;
            width: 100%; height: 100%;
            padding: 1.4rem 1rem 1rem;
            background: var(--paper);
            overflow-y: auto;
        }
        .brand { display: flex; align-items: center; gap: .75rem; padding: 0 .5rem 1.3rem; text-decoration: none; color: var(--plum); }
        .brand-mark {
            width: 42px; height: 42px; flex: none;
            display: grid; place-items: center;
            font-size: 1.25rem; color: #fff;
            background: linear-gradient(135deg, var(--violet) 0%, var(--bubble) 100%);
            border-radius: 14px 14px 14px 4px;
        }
        .brand-name { font-size: 1.1rem; line-height: 1.1; display: block; }
        .brand-sub { font-size: .78rem; color: var(--muted); font-weight: 400; font-family: 'DM Sans', sans-serif; }

        .nav-group { margin-top: 1.1rem; }
        .nav-group-title { font-size: .8rem; color: var(--muted); font-weight: 700; padding: 0 .75rem .35rem; }
        .side-link {
            display: flex; align-items: center; gap: .7rem;
            padding: .6rem .75rem; margin-bottom: 2px;
            color: var(--plum); text-decoration: none; font-weight: 500;
            border-radius: 12px;
            transition: background-color .15s ease, color .15s ease;
        }
        .side-link i { font-size: 1.05rem; color: var(--muted); transition: color .15s ease; }
        .side-link:hover { background: var(--mist); color: var(--plum); }
        .side-link.active { background: var(--lilac); color: var(--violet-d); font-weight: 700; }
        .side-link.active i { color: var(--violet-d); }

        .user-card {
            margin-top: auto; padding: .8rem;
            background: var(--mist); border-radius: 16px;
            display: flex; align-items: center; gap: .7rem;
        }
        .avatar {
            width: 38px; height: 38px; flex: none; border-radius: 50%;
            display: grid; place-items: center; font-weight: 700; color: var(--violet-d);
            background: var(--petal);
        }
        .user-name { font-weight: 700; font-size: .92rem; line-height: 1.2; }
        .role-pill {
            display: inline-block; font-size: .72rem; font-weight: 700;
            padding: .08rem .55rem; border-radius: 999px;
            background: var(--lilac); color: var(--violet-d);
        }
        .logout-btn {
            margin-left: auto; border: 0; background: transparent; color: var(--muted);
            font-size: 1.15rem; padding: .3rem .45rem; border-radius: 10px;
        }
        .logout-btn:hover { background: var(--petal); color: var(--bubble-d); }

        @media (min-width: 992px) {
            .sidebar-col { width: 276px; flex: none; position: sticky; top: 0; height: 100vh; border-right: 1px solid var(--line); }
        }
        @media (max-width: 991.98px) {
            .sidebar-col .offcanvas { --bs-offcanvas-width: 290px; }
            .sidebar-col .offcanvas-body { padding: 0; }
            .content-inner { padding: 1.25rem 1rem 2.5rem; }
        }

        .topbar {
            display: flex; align-items: center; gap: .75rem;
            padding: .7rem 1rem; background: var(--paper); border-bottom: 1px solid var(--line);
            position: sticky; top: 0; z-index: 1020;
        }
        .topbar .brand-mark { width: 34px; height: 34px; font-size: 1rem; border-radius: 11px 11px 11px 3px; }
        .topbar-toggle { border: 0; background: var(--lilac); color: var(--violet-d); border-radius: 12px; padding: .35rem .65rem; font-size: 1.25rem; }

        /* ---------- Komponen Bootstrap yang disesuaikan ---------- */
        .card {
            border: 1px solid var(--line); border-radius: 22px;
            background: var(--paper);
            box-shadow: 0 10px 30px -18px rgba(100, 70, 216, .35);
        }
        .card-header { background: transparent; border-bottom: 1px solid var(--line); font-weight: 700; padding: 1rem 1.25rem; border-radius: 22px 22px 0 0 !important; }
        .card-body { padding: 1.35rem; }
        .card .fs-3 { font-family: 'Bricolage Grotesque', sans-serif; letter-spacing: -0.02em; }
        .card.border-danger { border: 1.5px solid var(--bubble) !important; background: #fff8fc; }
        .text-danger { color: var(--bubble-d) !important; }
        .text-muted, .text-secondary { color: var(--muted) !important; }

        .btn { border-radius: 999px; font-weight: 700; padding: .5rem 1.15rem; }
        .btn-sm { padding: .3rem .8rem; }
        .btn-primary { --bs-btn-bg: var(--violet); --bs-btn-border-color: var(--violet); --bs-btn-hover-bg: var(--violet-d); --bs-btn-hover-border-color: var(--violet-d); --bs-btn-active-bg: var(--violet-d); --bs-btn-active-border-color: var(--violet-d); --bs-btn-focus-shadow-rgb: 122, 92, 240; }
        .btn-outline-primary { --bs-btn-color: var(--violet-d); --bs-btn-border-color: #cdbdfb; --bs-btn-hover-bg: var(--violet); --bs-btn-hover-border-color: var(--violet); --bs-btn-active-bg: var(--violet); --bs-btn-active-border-color: var(--violet); --bs-btn-focus-shadow-rgb: 122, 92, 240; }
        .btn-outline-secondary { --bs-btn-color: var(--muted); --bs-btn-border-color: var(--line); --bs-btn-hover-bg: var(--lilac); --bs-btn-hover-color: var(--violet-d); --bs-btn-hover-border-color: var(--lilac); --bs-btn-active-bg: var(--lilac); --bs-btn-active-color: var(--violet-d); --bs-btn-active-border-color: var(--lilac); }
        .btn-outline-danger { --bs-btn-color: var(--bubble-d); --bs-btn-border-color: #ffc2de; --bs-btn-hover-bg: var(--bubble); --bs-btn-hover-border-color: var(--bubble); --bs-btn-hover-color: #fff; --bs-btn-active-bg: var(--bubble-d); --bs-btn-active-border-color: var(--bubble-d); --bs-btn-focus-shadow-rgb: 255, 127, 189; }

        .form-control, .form-select { border-radius: 12px; border-color: var(--line); padding: .6rem .85rem; background-color: #fff; }
        .form-control:focus, .form-select:focus { border-color: var(--violet); box-shadow: 0 0 0 .22rem rgba(122, 92, 240, .18); }
        .form-label { font-weight: 700; font-size: .9rem; margin-bottom: .3rem; }
        .form-check-input:checked { background-color: var(--violet); border-color: var(--violet); }

        .table { --bs-table-bg: transparent; --bs-table-hover-bg: #faf6ff; --bs-table-border-color: var(--line); }
        .table > thead.table-light > tr > th, .table-light { --bs-table-bg: #f3ecff; --bs-table-color: var(--plum); color: var(--plum); font-weight: 700; border-bottom: 0; }
        .table > :not(caption) > * > * { padding: .85rem 1rem; }
        .table-responsive { border-radius: 22px; }

        .badge { font-weight: 700; border-radius: 999px; padding: .38em .75em; }
        .text-bg-danger   { background-color: var(--petal) !important; color: var(--bubble-d) !important; }
        .text-bg-success  { background-color: var(--sage) !important;  color: var(--sage-d) !important; }
        .text-bg-secondary{ background-color: #eceaf2 !important;      color: var(--muted) !important; }
        .text-bg-primary  { background-color: var(--lilac) !important; color: var(--violet-d) !important; }

        .alert { border: 0; border-radius: 16px; padding: .95rem 1.15rem; }
        .alert-success { background: var(--sage); color: #14543a; }
        .alert-danger  { background: var(--petal); color: #8f1f52; }
        .is-invalid { border-color: var(--bubble) !important; }
        .invalid-feedback { color: var(--bubble-d); }

        .list-group-item { border-color: var(--line); padding: .85rem 1.25rem; background: transparent; }
        .page-link { color: var(--violet-d); border-color: var(--line); border-radius: 10px !important; margin: 0 2px; }
        .page-item.active .page-link { background: var(--violet); border-color: var(--violet); color: #fff; }

        dt { color: var(--muted); font-weight: 500; }
        dd { font-weight: 500; }
        a:focus-visible, button:focus-visible, .side-link:focus-visible { outline: 3px solid rgba(122, 92, 240, .45); outline-offset: 2px; }

        /* ---------- Halaman login (tamu) ---------- */
        .guest-wrap { min-height: 100vh; display: grid; place-items: center; padding: 1.5rem; }
        .guest-inner { width: 100%; max-width: 440px; }
        .guest-inner .card { padding: .5rem; }
        /* view login punya pembungkus .row/.col-* sendiri: paksa selebar wadah */
        .guest-inner .row { margin: 0; --bs-gutter-x: 0; }
        .guest-inner .row > [class*='col-'] { flex: 0 0 100%; width: 100%; max-width: 100%; padding: 0; }
        .guest-brand { display: flex; align-items: center; gap: .75rem; justify-content: center; margin-bottom: 1.25rem; }
        .guest-brand .brand-mark { width: 48px; height: 48px; font-size: 1.4rem; border-radius: 16px 16px 16px 5px; }


        /* =========================================================
           Tema ceria: warna per menu, animasi, sparkle
           ========================================================= */
        .side-link i { color: var(--c, var(--muted)); font-size: 1.12rem; }
        .side-link:hover { background: var(--c-soft, var(--mist)); }
        .side-link:hover i { animation: wiggle .55s ease; }
        .side-link.active { background: var(--c-soft, var(--lilac)); color: var(--plum); box-shadow: inset 4px 0 0 var(--c, var(--violet)); }
        .side-link.active i { color: var(--c, var(--violet-d)); }

        .brand-mark { animation: wobble 6s ease-in-out infinite; }
        h1.h3::after { content: '\2726'; display: inline-block; margin-left: .45rem; font-size: .7em; color: var(--bubble); animation: twinkle 2.4s ease-in-out infinite; }

        /* tombol */
        .btn { transition: transform .15s ease, filter .15s ease, box-shadow .15s ease; }
        .btn:hover { transform: translateY(-2px) scale(1.03); }
        .btn:active { transform: scale(.93); }
        .btn-primary {
            background-image: linear-gradient(135deg, #7a5cf0 0%, #c04bd8 55%, #ff7fbd 100%);
            border-color: transparent; box-shadow: 0 8px 18px -8px rgba(122, 92, 240, .7);
        }
        .btn-primary:hover { filter: brightness(1.07); box-shadow: 0 12px 22px -8px rgba(255, 127, 189, .7); }

        /* kartu ringkasan: garis warna beragam + efek angkat */
        .card.h-100 { transition: transform .2s ease, box-shadow .2s ease; border-top: 5px solid var(--cc, var(--lilac)); }
        .card.h-100:hover { transform: translateY(-5px) rotate(-.6deg); box-shadow: 0 18px 34px -16px rgba(122, 92, 240, .55); }
        .row > [class*='col-']:nth-child(6n+1) > .card.h-100 { --cc: #7a5cf0; }
        .row > [class*='col-']:nth-child(6n+2) > .card.h-100 { --cc: #ff6fae; }
        .row > [class*='col-']:nth-child(6n+3) > .card.h-100 { --cc: #3aa0ff; }
        .row > [class*='col-']:nth-child(6n+4) > .card.h-100 { --cc: #19b37a; }
        .row > [class*='col-']:nth-child(6n+5) > .card.h-100 { --cc: #f2b705; }
        .row > [class*='col-']:nth-child(6n+6) > .card.h-100 { --cc: #ff9a4d; }
        .card.border-danger { border-top: 5px solid var(--bubble) !important; }

        /* animasi masuk halaman */
        .content-inner > * { animation: rise .55s cubic-bezier(.2, .8, .3, 1) backwards; }
        .content-inner > *:nth-child(2) { animation-delay: .05s; }
        .content-inner > *:nth-child(3) { animation-delay: .1s; }
        .content-inner > *:nth-child(4) { animation-delay: .15s; }
        .content-inner > *:nth-child(5) { animation-delay: .2s; }
        .row.g-3 > [class*='col-'] > .card.h-100 { animation: pop .6s cubic-bezier(.2, .8, .3, 1) backwards; }
        .row.g-3 > [class*='col-']:nth-child(2) > .card.h-100 { animation-delay: .07s; }
        .row.g-3 > [class*='col-']:nth-child(3) > .card.h-100 { animation-delay: .14s; }
        .row.g-3 > [class*='col-']:nth-child(4) > .card.h-100 { animation-delay: .21s; }
        .row.g-3 > [class*='col-']:nth-child(5) > .card.h-100 { animation-delay: .28s; }
        .row.g-3 > [class*='col-']:nth-child(6) > .card.h-100 { animation-delay: .35s; }
        tbody tr { animation: rowin .4s ease backwards; }
        tbody tr:nth-child(2) { animation-delay: .04s; } tbody tr:nth-child(3) { animation-delay: .08s; }
        tbody tr:nth-child(4) { animation-delay: .12s; } tbody tr:nth-child(5) { animation-delay: .16s; }
        tbody tr:nth-child(6) { animation-delay: .2s; }  tbody tr:nth-child(7) { animation-delay: .24s; }
        tbody tr:nth-child(8) { animation-delay: .28s; } tbody tr:nth-child(n+9) { animation-delay: .32s; }
        .alert { animation: pop .5s cubic-bezier(.2, .8, .3, 1) backwards; }

        @keyframes rise   { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: none; } }
        @keyframes pop    { 0% { opacity: 0; transform: scale(.82) translateY(12px); } 65% { opacity: 1; transform: scale(1.04); } 100% { transform: none; } }
        @keyframes rowin  { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: none; } }
        @keyframes wiggle { 0%, 100% { transform: rotate(0); } 25% { transform: rotate(-14deg) scale(1.15); } 75% { transform: rotate(14deg) scale(1.15); } }
        @keyframes wobble { 0%, 88%, 100% { transform: rotate(0); } 91% { transform: rotate(-9deg) scale(1.06); } 95% { transform: rotate(9deg) scale(1.06); } }
        @keyframes twinkle { 0%, 100% { transform: scale(.8) rotate(0); opacity: .6; } 50% { transform: scale(1.25) rotate(25deg); opacity: 1; } }
        @keyframes floaty { 0%, 100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-26px) rotate(18deg); } }

        /* sparkle (dibuat lewat JavaScript) */
        .spark {
            position: fixed; z-index: -1; pointer-events: none;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        }
        .spark.dot { clip-path: none; border-radius: 50%; }

        /* bintang melayang di latar */
        .floaties { position: fixed; inset: 0; z-index: -1; pointer-events: none; overflow: hidden; }
        .floaties span {
            position: absolute; width: var(--s, 18px); height: var(--s, 18px); opacity: .45;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            background: var(--bg, #ff7fbd); animation: floaty var(--d, 9s) ease-in-out infinite; animation-delay: var(--dl, 0s);
        }


        /* ---------- Sparkle berkedip (lebih banyak) ---------- */
        .floaties span {
            opacity: .55;
            animation: floaty var(--d, 9s) ease-in-out infinite, blink var(--b, 2s) ease-in-out infinite;
            animation-delay: var(--dl, 0s), var(--dl2, 0s);
        }
        @keyframes blink {
            0%, 100% { opacity: .15; scale: .55; }
            50%      { opacity: 1;   scale: 1.3; }
        }
        @keyframes blink2 {
            0%, 100% { opacity: .25; transform: scale(.6) rotate(0deg); }
            50%      { opacity: 1;   transform: scale(1.25) rotate(35deg); }
        }
        /* judul berkilau dua bintang */
        h1.h3::before {
            content: '\2727'; display: inline-block; margin-right: .4rem; font-size: .6em;
            color: #6ec6ff; vertical-align: .25em; animation: blink2 2.6s ease-in-out infinite .5s;
        }


        /* ---------- Sparkle v3: lebih banyak bentuk & lapisan glitter ---------- */
        .spark.four, .floaties span.four, .glitter span.four {
            clip-path: polygon(50% 0%, 61% 39%, 100% 50%, 61% 61%, 50% 100%, 39% 61%, 0% 50%, 39% 39%);
        }
        .spark.heart {
            clip-path: none; background: none !important; width: auto !important; height: auto !important;
            line-height: 1; font-family: Arial, sans-serif;
        }
        .floaties span.heart { clip-path: none; background: none; width: auto; height: auto; font-size: var(--s, 18px); line-height: 1; color: var(--bg); }
        .floaties span.dot { clip-path: none; border-radius: 50%; }
        @keyframes blink {
            0%, 100% { opacity: .2; scale: .5; }
            50%      { opacity: 1;  scale: 1.35; }
        }
        .glitter { position: fixed; inset: 0; z-index: -1; pointer-events: none; overflow: hidden; }
        .glitter span {
            position: absolute; width: var(--g, 4px); height: var(--g, 4px);
            background: var(--bg, #fff); border-radius: 50%; box-shadow: 0 0 7px 1px var(--bg, #fff);
            opacity: 0; animation: glit var(--b, 1.6s) ease-in-out infinite; animation-delay: var(--dl, 0s);
        }
        .glitter span.four { border-radius: 0; box-shadow: none; filter: drop-shadow(0 0 3px var(--bg, #fff)); }
        @keyframes glit {
            0%, 100% { opacity: 0;   scale: .2; }
            50%      { opacity: .95; scale: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .floaties, .glitter { display: none; }
        }
    </style>
</head>
<body>
<div class="floaties" id="floaties" aria-hidden="true"></div>
<div class="glitter" id="glitter" aria-hidden="true"></div>


@auth
<div class="shell">
    <div class="sidebar-col">
        <aside class="offcanvas-lg offcanvas-start h-100" tabindex="-1" id="sidebar" aria-label="Menu utama">
            <div class="offcanvas-header d-lg-none">
                <span class="brand-name">Menu</span>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body sidebar-inner">
                <a href="{{ route('dashboard') }}" class="brand">
                    <span class="brand-mark"><i class="bi bi-boxes"></i></span>
                    <span>
                        <span class="brand-name">Inventory &amp; Sales</span>
                        <span class="brand-sub">Stok dan penjualan</span>
                    </span>
                </a>

                <nav>
                    <div class="nav-group">
                        <div class="nav-group-title">Ringkasan</div>
                        <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="--c:#7a5cf0;--c-soft:#e8deff;" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                        <a class="side-link {{ request()->routeIs('products.*') && ! request()->routeIs('products.low-stock') ? 'active' : '' }}" style="--c:#ff6fae;--c-soft:#ffe0ef;" href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam"></i> Produk
                        </a>
                        <a class="side-link {{ request()->routeIs('products.low-stock') ? 'active' : '' }}" style="--c:#ff9a4d;--c-soft:#ffe9d6;" href="{{ route('products.low-stock') }}">
                            <i class="bi bi-exclamation-diamond"></i> Stok rendah
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-group-title">Transaksi</div>
                        <a class="side-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" style="--c:#19b37a;--c-soft:#d9f5e8;" href="{{ route('purchases.index') }}">
                            <i class="bi bi-box-arrow-in-down"></i> Pembelian
                        </a>
                        <a class="side-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" style="--c:#3aa0ff;--c-soft:#dcefff;" href="{{ route('sales.index') }}">
                            <i class="bi bi-receipt"></i> Penjualan
                        </a>
                    </div>

                    @can('manage-master')
                    <div class="nav-group">
                        <div class="nav-group-title">Laporan</div>
                        <a class="side-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" style="--c:#f2b705;--c-soft:#fff3c4;" href="{{ route('reports.index') }}">
                            <i class="bi bi-clock-history"></i> Riwayat &amp; laporan
                        </a>
                    </div>
                    @endcan

                    @can('manage-master')
                    <div class="nav-group">
                        <div class="nav-group-title">Data master</div>
                        <a class="side-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" style="--c:#b06cff;--c-soft:#f0e3ff;" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> Kategori
                        </a>
                        <a class="side-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" style="--c:#ff6b6b;--c-soft:#ffe1e1;" href="{{ route('suppliers.index') }}">
                            <i class="bi bi-truck"></i> Pemasok
                        </a>
                    </div>
                    @endcan

                    @can('manage-users')
                    <div class="nav-group">
                        <div class="nav-group-title">Pengaturan</div>
                        <a class="side-link {{ request()->routeIs('users.*') ? 'active' : '' }}" style="--c:#18b5b0;--c-soft:#d6f6f4;" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i> Pengguna &amp; role
                        </a>
                    </div>
                    @endcan
                </nav>

                <div class="user-card mt-4">
                    <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <div>
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <span class="role-pill">{{ auth()->user()->role }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                        @csrf
                        <button class="logout-btn" title="Keluar" aria-label="Keluar"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </aside>
    </div>

    <div class="content">
        <div class="topbar d-lg-none">
            <button class="topbar-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
            <span class="brand-mark"><i class="bi bi-boxes"></i></span>
            <span class="brand-name">Inventory &amp; Sales</span>
        </div>
        <main class="content-inner">
@endauth
@guest
<div class="guest-wrap">
    <div class="guest-inner">
        <div class="guest-brand">
            <span class="brand-mark"><i class="bi bi-boxes"></i></span>
            <span class="brand-name fs-4">Inventory &amp; Sales</span>
        </div>
@endguest

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if ($errors->any() && ! request()->routeIs('login'))
                <div class="alert alert-danger" role="alert">
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

@auth
        </main>
    </div>
</div>
@endauth
@guest
    </div>
</div>
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    /* ====== Pengaturan jumlah sparkle (ubah angka ini untuk menambah / mengurangi) ====== */
    var FLOATIES = window.innerWidth < 768 ? 40 : 80;        // bintang berkedip di latar
    var GLITTER  = window.innerWidth < 768 ? 22 : 46;        // titik kilau kecil
    var EMPTY_STARS   = window.innerWidth < 768 ? 24 : 60;   // bintang tambahan di area kosong
    var EMPTY_GLITTER = window.innerWidth < 768 ? 16 : 40;   // glitter tambahan di area kosong
    var CLICK_PARTY = 3;      // jumlah ledakan di area kosong setiap klik
    var CLICK_BURST = 14;     // isi tiap ledakan
    var AMBIENT_MS  = 1500;   // jeda 'kembang api' otomatis (ms)
    /* Semua sparkle berada DI BELAKANG kartu, tabel, dan menu: hanya terlihat di area kosong. */
    /* =================================================================================== */

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var canHover = window.matchMedia('(hover: hover)').matches;
    var colors = ['#ff7fbd', '#7a5cf0', '#ffd166', '#5ee0b5', '#6ec6ff', '#ff9f6e', '#c59bff', '#ff5fa2'];
    var live = 0, MAX_LIVE = 160;
    function rand(a, b) { return a + Math.random() * (b - a); }
    function color() { return colors[Math.floor(Math.random() * colors.length)]; }
    function shape() {
        var r = Math.random();
        if (r < 0.15) return 'heart';
        if (r < 0.40) return 'four';
        if (r < 0.60) return 'dot';
        return '';
    }

    // titik acak di area kosong (separuh bawah, di sebelah kanan sidebar)
    function emptySpot() {
        var W = window.innerWidth, H = window.innerHeight;
        var minX = W >= 992 ? W * 0.3 : W * 0.05;
        return { x: rand(minX, W * 0.97), y: rand(H * 0.5, H * 0.96) };
    }

    function makeSpark(x, y, size) {
        var s = document.createElement('span');
        var sh = shape();
        s.className = 'spark' + (sh ? ' ' + sh : '');
        s.style.left = x + 'px';
        s.style.top = y + 'px';
        var c = color();
        if (sh === 'heart') {
            s.textContent = '\u2665';
            s.style.color = c;
            s.style.fontSize = (size * 1.5) + 'px';
        } else {
            s.style.width = size + 'px';
            s.style.height = size + 'px';
            s.style.background = c;
        }
        document.body.appendChild(s);
        live++;
        return s;
    }

    // ledakan sparkle
    function burst(x, y, n) {
        if (reduce || !document.body.animate || live > MAX_LIVE) return;
        n = n || 14;
        for (var i = 0; i < n; i++) {
            var s = makeSpark(x, y, rand(8, 22));
            var ang = Math.random() * Math.PI * 2;
            var dist = rand(50, 140);
            var dx = Math.cos(ang) * dist;
            var dy = Math.sin(ang) * dist - 26;
            var a = s.animate([
                { transform: 'translate(-50%, -50%) scale(0) rotate(0deg)', opacity: 1 },
                { transform: 'translate(calc(-50% + ' + dx * 0.6 + 'px), calc(-50% + ' + dy * 0.6 + 'px)) scale(1.2) rotate(150deg)', opacity: 1, offset: 0.4 },
                { transform: 'translate(calc(-50% + ' + dx + 'px), calc(-50% + ' + (dy + 38) + 'px)) scale(0) rotate(320deg)', opacity: 0 }
            ], { duration: rand(750, 1250), easing: 'cubic-bezier(.2,.8,.3,1)' });
            a.onfinish = (function (el) { return function () { el.remove(); live--; }; })(s);
        }
    }

    // beberapa ledakan berurutan di area kosong
    function party(times, n) {
        for (var i = 0; i < times; i++) {
            (function (delay) {
                setTimeout(function () { var p = emptySpot(); burst(p.x, p.y, n); }, delay);
            })(i * 140);
        }
    }

    // jejak kilau kecil yang mengikuti kursor (tampil hanya di area kosong)
    function trail(x, y) {
        if (reduce || !document.body.animate || live > MAX_LIVE) return;
        for (var k = 0; k < 2; k++) {
            var s = makeSpark(x + rand(-6, 6), y + rand(-6, 6), rand(6, 13));
            var a = s.animate([
                { transform: 'translate(-50%, -50%) scale(1) rotate(0deg)', opacity: 1 },
                { transform: 'translate(calc(-50% + ' + rand(-14, 14) + 'px), calc(-50% + ' + rand(16, 34) + 'px)) scale(0) rotate(220deg)', opacity: 0 }
            ], { duration: rand(550, 900), easing: 'ease-out' });
            a.onfinish = (function (el) { return function () { el.remove(); live--; }; })(s);
        }
    }

    // bintang berkedip di latar
    var fl = document.getElementById('floaties');
    if (fl && !reduce) {
        for (var i = 0; i < FLOATIES; i++) {
            var st = document.createElement('span');
            var sh = shape();
            if (sh) st.className = sh;
            if (sh === 'heart') st.textContent = '\u2665';
            st.style.cssText = 'left:' + rand(0, 100) + '%;top:' + rand(0, 100) + '%;' +
                '--s:' + rand(9, 28) + 'px;--bg:' + color() + ';' +
                '--d:' + rand(7, 14) + 's;--b:' + rand(1.2, 3.2) + 's;' +
                '--dl:' + (-rand(0, 8)) + 's;--dl2:' + (-rand(0, 3)) + 's';
            fl.appendChild(st);
        }
        for (var q = 0; q < EMPTY_STARS; q++) {
            var es = document.createElement('span');
            var esh = shape();
            if (esh) es.className = esh;
            if (esh === 'heart') es.textContent = '\u2665';
            es.style.cssText = 'left:' + rand(0, 100) + '%;top:' + rand(50, 98) + '%;' +
                '--s:' + rand(14, 36) + 'px;--bg:' + color() + ';' +
                '--d:' + rand(6, 12) + 's;--b:' + rand(1, 2.6) + 's;' +
                '--dl:' + (-rand(0, 8)) + 's;--dl2:' + (-rand(0, 3)) + 's';
            fl.appendChild(es);
        }
    }

    // glitter kecil yang berkedip di latar
    var gl = document.getElementById('glitter');
    if (gl && !reduce) {
        for (var j = 0; j < GLITTER; j++) {
            var g = document.createElement('span');
            if (Math.random() < 0.4) g.className = 'four';
            g.style.cssText = 'left:' + rand(0, 100) + '%;top:' + rand(0, 100) + '%;' +
                '--g:' + rand(3, 7) + 'px;--bg:' + color() + ';' +
                '--b:' + rand(1.1, 2.6) + 's;--dl:' + (-rand(0, 3)) + 's';
            gl.appendChild(g);
        }
        for (var w = 0; w < EMPTY_GLITTER; w++) {
            var eg = document.createElement('span');
            if (Math.random() < 0.45) eg.className = 'four';
            eg.style.cssText = 'left:' + rand(0, 100) + '%;top:' + rand(50, 99) + '%;' +
                '--g:' + rand(4, 9) + 'px;--bg:' + color() + ';' +
                '--b:' + rand(0.9, 2.2) + 's;--dl:' + (-rand(0, 3)) + 's';
            gl.appendChild(eg);
        }
    }

    // 'kembang api' otomatis di area kosong
    if (!reduce) {
        setInterval(function () {
            if (document.hidden) return;
            var p = emptySpot();
            burst(p.x, p.y, Math.round(rand(7, 13)));
        }, AMBIENT_MS);
    }

    // setiap menekan tombol / menu / tautan: ledakan di area kosong
    document.addEventListener('click', function (e) {
        var t = e.target.closest('a, button, .btn, .side-link');
        if (!t) return;
        party(CLICK_PARTY, CLICK_BURST);
        try { sessionStorage.setItem('spark', String(Date.now())); } catch (err) {}
    });

    // lanjutan di halaman berikutnya
    try {
        var p0 = sessionStorage.getItem('spark');
        if (p0) {
            sessionStorage.removeItem('spark');
            if (Date.now() - parseInt(p0, 10) < 4000) setTimeout(function () { party(2, 12); }, 200);
        }
    } catch (err) {}

    // pesta saat berhasil menyimpan
    if (document.querySelector('.alert-success')) {
        setTimeout(function () { party(5, 18); }, 250);
    }

    // jejak kursor (hanya perangkat dengan mouse)
    if (canHover && !reduce) {
        var last = 0, lx = 0, ly = 0;
        document.addEventListener('mousemove', function (e) {
            var now = performance.now();
            if (now - last < 45) return;
            if (Math.abs(e.clientX - lx) + Math.abs(e.clientY - ly) < 10) return;
            last = now; lx = e.clientX; ly = e.clientY;
            trail(e.clientX, e.clientY);
        });
    }
})();
</script>
{{-- ===== Ledakan sparkle imut tepat di menu yang diklik (tambahan v6) ===== --}}
<script>
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!document.body.animate) return;

    var MENU_SELECTOR = '.sidebar-col a, .side-link';   // menu sidebar
    var COUNT = 20;                                      // jumlah partikel per ledakan
    var COLORS = ['#ff7fbd', '#7a5cf0', '#ffd166', '#5ee0b5', '#6ec6ff', '#ff9f6e', '#c59bff', '#ff5fa2'];
    var SHAPES = [
        '<path d="M12 0C13 8 16 11 24 12C16 13 13 16 12 24C11 16 8 13 0 12C8 11 11 8 12 0Z"/>',
        '<path d="M12 21s-8-5.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.5-8 11-8 11z"/>',
        '<circle cx="12" cy="12" r="9"/>'
    ];

    // lapisan khusus di depan semua elemen, tidak menghalangi klik
    var layer = document.createElement('div');
    layer.setAttribute('aria-hidden', 'true');
    layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99999;overflow:hidden;';
    document.body.appendChild(layer);

    function rand(a, b) { return a + Math.random() * (b - a); }

    function menuBurst(x, y) {
        for (var i = 0; i < COUNT; i++) {
            var size = rand(10, 24);
            var color = COLORS[Math.floor(Math.random() * COLORS.length)];
            var shape = SHAPES[Math.floor(Math.random() * SHAPES.length)];

            var p = document.createElement('span');
            p.style.cssText = 'position:absolute;left:' + (x - size / 2) + 'px;top:' + (y - size / 2) + 'px;' +
                'width:' + size + 'px;height:' + size + 'px;will-change:transform,opacity;';
            p.innerHTML = '<svg viewBox="0 0 24 24" width="100%" height="100%" fill="' + color + '">' + shape + '</svg>';
            layer.appendChild(p);

            var ang = (Math.PI * 2 * i) / COUNT + Math.random() * 0.5;
            var dist = rand(45, 120);
            var dx = Math.cos(ang) * dist;
            var dy = Math.sin(ang) * dist + 22;
            var rot = rand(-180, 180);

            var a = p.animate([
                { transform: 'translate(0,0) scale(0) rotate(0deg)', opacity: 1 },
                { transform: 'translate(' + dx * 0.6 + 'px,' + dy * 0.6 + 'px) scale(1.2) rotate(' + rot / 2 + 'deg)', opacity: 1, offset: 0.4 },
                { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(0) rotate(' + rot + 'deg)', opacity: 0 }
            ], { duration: rand(650, 1100), easing: 'cubic-bezier(.2,.8,.3,1)' });
            a.onfinish = (function (el) { return function () { el.remove(); }; })(p);
        }
    }

    document.addEventListener('click', function (e) {
        var item = e.target.closest(MENU_SELECTOR);
        if (!item) return;
        var x = e.clientX, y = e.clientY;
        if (!x && !y) {   // klik lewat keyboard
            var r = item.getBoundingClientRect();
            x = r.left + r.width / 2;
            y = r.top + r.height / 2;
        }
        menuBurst(x, y);
        // halaman akan berpindah, jadi ledakan dilanjutkan di halaman berikutnya
        try { sessionStorage.setItem('menuBurst', JSON.stringify({ x: x, y: y, t: Date.now() })); } catch (err) {}
    }, true);

    try {
        var raw = sessionStorage.getItem('menuBurst');
        if (raw) {
            sessionStorage.removeItem('menuBurst');
            var d = JSON.parse(raw);
            if (Date.now() - d.t < 4000) setTimeout(function () { menuBurst(d.x, d.y); }, 120);
        }
    } catch (err) {}
})();
</script>
@stack('scripts')
</body>
</html>
