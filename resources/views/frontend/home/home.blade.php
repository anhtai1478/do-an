@extends('frontend.layouts.master')

@section('title', 'Trang chủ - Shop của tôi')

@section('slide')

    @include('frontend.layouts.slide')
@endsection

@section('content')
    <div class="row">
        @forelse ($product as $item)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            @if ($item->primaryImage())
                                <img src="{{ asset('storage/' . $item->primaryImage()) }}" alt="{{ $item->name }}">
                            @endif

                            <h2>${{ number_format((float) $item->price, 2) }}</h2>
                            <p>{{ $item->name }}</p>
                        </div>

                        <div class="product-overlay">
                            <div class="overlay-content">
                                <h2>${{ number_format((float) $item->price, 2) }}</h2>
                                <p>{{ $item->name }}</p>
                                <button type="button" class="btn btn-default add-to-cart">
                                    <i class="fa fa-shopping-cart"></i> Add to cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>Chưa có sản phẩm nào.</p>
        @endforelse
    </div>
@endsection