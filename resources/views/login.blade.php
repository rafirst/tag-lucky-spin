<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Buatan gue nih Mohammad Rafi Abdillah. IG : @rafirst_ -->
  <title>Login - TAG Roulette</title>
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

<main class="login-shell">
  <section class="panel login-panel" aria-label="Login pengelola">
    <h2>Masuk pengelola</h2>
    <p class="login-description">Silakan masuk untuk membuka roda dan mengelola hadiah.</p>
    <form class="auth-form login-form" method="POST" action="{{ route('login.authenticate') }}">
      @csrf
      <input type="text" name="username" value="{{ old('username') }}" placeholder="Username" autocomplete="username" required autofocus aria-label="Username">
      <input type="password" name="password" placeholder="Password" autocomplete="current-password" required aria-label="Password">
      @if($errors->any())
        <p class="auth-error">{{ $errors->first() }}</p>
      @endif
      <button class="btn red" type="submit">Masuk</button>
    </form>
  </section>
</main>
</body>
</html>
