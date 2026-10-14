@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
  <h4>Lupa password?</h4>
  <h6 class="font-weight-light">Masukkan email Anda, kami akan mengirimkan link untuk reset password.</h6>

  @if (session('status'))
    <div class="alert alert-success mt-3">{{ session('status') }}</div>
  @endif

  <form class="pt-3" method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
      <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="nama@dty.local" required autofocus>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100">KIRIM LINK RESET</button>
    </div>

    <div class="text-center mt-4 font-weight-light">
      <a href="{{ route('login') }}" class="text-primary">Kembali ke login</a>
    </div>
  </form>
@endsection
