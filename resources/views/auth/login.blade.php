@extends('layouts.auth')

@section('title', 'Login')

@section('content')
  <h4>Selamat datang!</h4>
  <h6 class="font-weight-light">Silakan login untuk melanjutkan.</h6>

  @if (session('status'))
    <div class="alert alert-success mt-3">{{ session('status') }}</div>
  @endif

  <form class="pt-3" method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
      <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="nama@dty.local" required autofocus>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100">LOGIN</button>
    </div>

    <div class="my-2 d-flex justify-content-between align-items-center">
      <div class="form-check">
        <label class="form-check-label text-muted">
          <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
          Ingat saya
        </label>
      </div>
      <a href="{{ route('password.request') }}" class="auth-link text-black">Lupa password?</a>
    </div>
  </form>
@endsection
