<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TAG Lucky Spin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,800;1,800;1,900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/tag.css') }}?v={{ filemtime(public_path('assets/css/tag.css')) }}">
</head>
<body>
<header>
  <div class="slash" aria-hidden="true"></div>
  <div>
    <h1>TAG Roulette</h1>
    <p>PT Tunas Auto Graha</p>
  </div>
</header>
<div class="hline"></div>

<main id="mainLayout">
  <section class="stage" aria-label="Roda hadiah">
    <div class="wheel-wrap">
      <svg class="needle" id="needle" viewBox="0 0 40 96" aria-hidden="true">
        <defs><linearGradient id="ng" x1="0" x2="1"><stop offset="0" stop-color="#b80818"/><stop offset=".5" stop-color="#ff3b4c"/><stop offset="1" stop-color="#b80818"/></linearGradient></defs>
        <path d="M20 96 12 14h16z" fill="url(#ng)"/>
        <circle cx="20" cy="13" r="11" fill="#CBD5E1" stroke="#64748b" stroke-width="2"/><circle cx="20" cy="13" r="5" fill="#EB0A1E"/>
      </svg>
      <canvas id="wheel" aria-label="Roda dengan pilihan hadiah"></canvas>
      <button class="start" id="spinBtn" aria-label="Putar roda">
        <svg viewBox="0 0 24 24" fill="none" stroke="#EB0A1E" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 3v8"/><path d="M6.3 6.6a8 8 0 1 0 11.4 0"/></svg>
        <span>SPIN</span>
      </button>
    </div>
    <button class="btn red list-toggle" id="toggleList" type="button" aria-controls="side" aria-expanded="true">Daftar</button>
    <div class="sr" id="live" aria-live="polite"></div>
  </section>

  <aside class="side" id="side">
    <section class="panel" aria-label="Daftar hadiah">
      <h2>Daftar hadiah <span class="count" id="count"></span></h2>
      <form class="auth-form signed-in" method="POST" action="{{ route('logout') }}">
        @csrf
        <span>Masuk sebagai {{ auth()->user()->username }}</span>
        <button class="btn small" type="submit">Keluar</button>
      </form>
      <form class="add" onsubmit="return false">
        <input type="text" id="newItem" maxlength="40" placeholder="Tulis hadiah, tekan Enter" autocomplete="off" aria-label="Nama hadiah baru">
        <label class="stock-field"><span>Stok</span><input type="number" id="newStock" min="1" max="9999" step="1" value="1" required aria-label="Stok hadiah" title="Stok hadiah"></label>
        <button class="btn red" id="addBtn" type="button">Tambah</button>
      </form>
      <ul id="items"></ul>
      <p class="hint">Stok berkurang 1 setiap menang dan hadiah keluar dari roda saat stok habis. Tarik pegangan ⠿ untuk mengubah urutan.</p>
      <div class="row">
        <button class="btn small" id="clear" type="button">Kosongkan</button>
      </div>
    </section>

    <section class="panel" aria-label="Riwayat pemenang">
      <h2>Riwayat pemenang <button class="btn small" id="clearHist" type="button">Hapus</button></h2>
      <ul id="history"></ul>
      <p class="empty" id="histEmpty">Belum ada putaran. Tekan tombol di tengah roda untuk mulai.</p>
    </section>
  </aside>
</main>

<div class="overlay" id="overlay" role="dialog" aria-modal="true" aria-labelledby="winName">
  <div class="modal">
    <small>Selamat! Hadiah Anda</small>
    <div class="badge" id="badge" hidden>HADIAH UTAMA</div>
    <div class="win" id="winName"></div>
    <div class="row">
      <button class="btn red" id="again">Putar lagi</button>
      <button class="btn" id="close">Tutup</button>
    </div>
  </div>
</div>
<canvas id="fx" aria-hidden="true"></canvas>
<div class="toast" id="toast" role="status"></div>

{{-- Data awal dari database. Js::from() aman dari XSS (nama hadiah bebas diketik user). --}}
<script>window.TAG_BOOT = {{ \Illuminate\Support\Js::from($boot) }};</script>
<script src="{{ asset('assets/js/tag.js') }}?v={{ filemtime(public_path('assets/js/tag.js')) }}"></script>
</body>
</html>
