@extends('frontend.layouts.master')

@section('title', 'Update user')

@section('menu_left')
    <div class="col-lg-3">
        @include('frontend.layouts.menu-account')
    </div>
@endsection

@section('content')
    <div class="blog-post-area">
        <h2 class="title text-center">Update user</h2>

        <div class="signup-form">
            <h2>New User Signup!</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="text" name="name" placeholder="Name" value="{{ old('name', $user->name) }}"
                    autocomplete="name" required />
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input type="email" name="email" placeholder="Email Address"
                    value="{{ old('email', $user->email) }}" autocomplete="email" required />
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input type="password" name="password" placeholder="Password (để trống nếu không đổi)"
                    autocomplete="new-password" />
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input type="text" name="phone" placeholder="Phone" value="{{ old('phone', $user->phone) }}"
                    autocomplete="tel" />
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input type="text" name="address" placeholder="Address" value="{{ old('address', $user->address) }}"
                    autocomplete="street-address" />
                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <select name="id_country" id="id_country" aria-label="Country">
                    <option value="">-- Select country --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}"
                            @selected((string) old('id_country', $user->id_country) === (string) $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('id_country')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                @if ($user->avatar)
                    <div class="form-group">
                        <img src="{{ str_contains($user->avatar, '/') ? asset('storage/' . $user->avatar) : asset('upload/user/avatar/' . $user->avatar) }}"
                            alt="Current avatar" width="80" height="80" style="object-fit: cover">
                    </div>
                @endif
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif" />
                @error('avatar')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <button type="submit" class="btn btn-default">Update</button>
            </form>
        </div>
    </div>
@endsection