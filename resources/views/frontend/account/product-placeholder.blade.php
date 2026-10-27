@extends('frontend.layouts.master')

@section('title', $title)

@section('menu_left')
    <div class="col-lg-3">
        @include('frontend.layouts.menu-account')
    </div>
@endsection

@section('content')
    <h2>{{ $title }}</h2>
    <p>Chức năng này sẽ được làm sau.</p>
@endsection
