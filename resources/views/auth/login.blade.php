@extends('frontend.layouts.master')
@section('content')
    <div class="login-form">
        <h2>Login to your account</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required
                autocomplete="email">

            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">

            <span>
                <input type="checkbox" name="remember" class="checkbox">
                Keep me signed in
            </span>

            <button type="submit" class="btn btn-default">Login</button>

            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </form>
        <p>Chưa có tài khoản?
            <a href="{{ route('register') }}">Đăng ký</a>
        </p>
    </div>
@endsection