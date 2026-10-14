@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
  <h4>Reset password</h4>
  <h6 class="font-weight-light">Masukkan password baru Anda.</h6>

  <form class="pt-3" method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
      <input type="email" name="email" value="{{ old('email', $email) }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="nama@dty.local" required>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password baru" required autofocus>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Ulangi password baru" required>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100">SIMPAN PASSWORD</button>
    </div>
  </form>
@endsection
