@extends('frontend.layouts.master')

@section('title', $product->name)

@section('content')
    @php
        $images = array_slice($product->imagePaths(), 0, 5);
    @endphp

    @if ($images !== [])
        <div class="product-details">
            <div class="col-sm-5">
                <div class="view-product">
                    <a id="product-main-image-link" href="{{ asset('storage/' . $images[0]) }}"
                        aria-label="Phóng to ảnh sản phẩm">
                        <img id="product-main-image" src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}">
                        <h3>ZOOM</h3>
                    </a>
                </div>

                <div class="product-image-slider">
                    @if (count($images) > 1)
                        <button type="button" class="product-image-arrow" data-thumbnail-prev aria-label="Ảnh trước">
                            <i class="fa fa-angle-left"></i>
                        </button>
                    @endif
                    <div id="product-thumbnails-viewport">
                        <div id="product-thumbnails">
                            @foreach ($images as $image)
                                <button type="button" class="product-thumbnail {{ $loop->first ? 'active' : '' }}"
                                    data-index="{{ $loop->index }}"
                                    data-image="{{ asset('storage/' . $image) }}"
                                    aria-label="Xem ảnh {{ $loop->iteration }} của sản phẩm"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @if (count($images) > 1)
                        <button type="button" class="product-image-arrow" data-thumbnail-next aria-label="Ảnh tiếp theo">
                            <i class="fa fa-angle-right"></i>
                        </button>
                    @endif
                </div>

            </div>

            <div class="col-sm-7">
                <div class="product-information">
                    <h2>{{ $product->name }}</h2>
                    <span>
                        <span>Giá: ${{ number_format((float) $product->price, 2) }}</span>
                    </span>
                    <p><b>Danh mục:</b> {{ $product->category ?: 'Chưa cập nhật' }}</p>
                    <p><b>Thương hiệu:</b> {{ $product->brand ?: 'Chưa cập nhật' }}</p>
                    <label for="product-quantity">Số lượng:</label>
                    <input type="number" id="product-quantity" min="1" max="99" value="1">
                    <button type="button" class="btn btn-default add-to-cart"
                        data-add-to-cart
                        data-product-id="{{ $product->id }}"
                        data-cart-url="{{ route('cart.add', $product->id) }}">
                        <i class="fa fa-shopping-cart"></i> Add to cart
                    </button>
                </div>
            </div>
        </div>
        <div id="product-image-modal" class="product-image-lightbox" aria-hidden="true">
            <button type="button" class="product-image-modal-close" data-close-product-image aria-label="Đóng ảnh">
                &times;
            </button>
            <img id="product-image-modal-image" src="{{ asset('storage/' . $images[0]) }}"
                alt="{{ $product->name }}">
        </div>
    @endif

    <div class="category-tab shop-details-tab">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab">Details</a></li>
                <li><a href="#company-profile" data-toggle="tab">Company Profile</a></li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="details">
                <div class="col-sm-12">
                    <p>{{ $product->description ?: 'Chưa có mô tả sản phẩm.' }}</p>
                </div>
            </div>

            <div class="tab-pane fade" id="company-profile">
                <div class="col-sm-12">
                    <p>{{ $product->company_profile ?: 'Chưa có thông tin công ty.' }}</p>
                </div>
            </div>

        </div>
    </div>
@endsection
