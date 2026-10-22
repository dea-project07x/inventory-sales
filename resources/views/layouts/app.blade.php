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

        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body>

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
                        <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                        <a class="side-link {{ request()->routeIs('products.*') && ! request()->routeIs('products.low-stock') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam"></i> Produk
                        </a>
                        <a class="side-link {{ request()->routeIs('products.low-stock') ? 'active' : '' }}" href="{{ route('products.low-stock') }}">
                            <i class="bi bi-exclamation-diamond"></i> Stok rendah
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-group-title">Transaksi</div>
                        <a class="side-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                            <i class="bi bi-box-arrow-in-down"></i> Pembelian
                        </a>
                        <a class="side-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <i class="bi bi-receipt"></i> Penjualan
                        </a>
                    </div>

                    @can('manage-master')
                    <div class="nav-group">
                        <div class="nav-group-title">Data master</div>
                        <a class="side-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> Kategori
                        </a>
                        <a class="side-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                            <i class="bi bi-truck"></i> Pemasok
                        </a>
                    </div>
                    @endcan

                    @can('manage-users')
                    <div class="nav-group">
                        <div class="nav-group-title">Pengaturan</div>
                        <a class="side-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
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
@stack('scripts')
</body>
</html>
