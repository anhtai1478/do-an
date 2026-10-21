@extends('frontend.layouts.master')

@section('content')
    <div class="signup-form">
        <h2>New User Signup!</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" required autocomplete="name">

            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required
                autocomplete="email">

            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone Number" autocomplete="tel">

            <input type="text" name="address" value="{{ old('address') }}" placeholder="Address"
                autocomplete="street-address">

            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">

            <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                autocomplete="new-password">

            <button type="submit" class="btn btn-default">Signup</button>
        </form>
        <p>Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập</a>
        </p>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection