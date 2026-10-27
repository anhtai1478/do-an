@extends('frontend.layouts.master')

@section('title', 'My Product')

@section('menu_left')
    <div class="col-lg-3">
        @include('frontend.layouts.menu-account')
    </div>
@endsection

@section('content')
    <div id="cart_items">
        <div class="heading">
            <h2>My Product</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="text-right" style="margin-bottom: 15px">
            <a href="{{ route('account.add-product') }}" class="btn btn-default">Add New</a>
        </div>

        <div class="table-responsive cart_info">
            <table class="table table-condensed account-products-table">
                <colgroup>
                    <col class="account-products-image-column">
                    <col class="account-products-name-column">
                    <col class="account-products-price-column">
                    <col class="account-products-action-column">
                </colgroup>
                <thead>
                    <tr class="cart_menu">
                        <td class="image">Image</td>
                        <td class="description">Name</td>
                        <td class="price">Price</td>
                        <td class="total">Action</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="cart_product image">
                                @if ($product->primaryImage())
                                    <img src="{{ asset('storage/' . $product->primaryImage()) }}"
                                        alt="{{ $product->name }}" width="110" height="120"
                                        style="object-fit: cover">
                                @else
                                    <span>Chưa có ảnh</span>
                                @endif
                            </td>
                            <td class="cart_description">
                                <h4>{{ $product->name }}</h4>
                                @if ($product->description)
                                    <p>{{ $product->description }}</p>
                                @endif
                            </td>
                            <td class="cart_price">
                                @if ($product->discountedPrice() !== null)
                                    <p class="account-products-original-price">
                                        ${{ number_format((float) $product->price, 2) }}
                                    </p>
                                    <p class="account-products-discount">
                                        -{{ $product->discount_percent }}%
                                    </p>
                                    <p class="account-products-sale-price">
                                        ${{ number_format((float) $product->discountedPrice(), 2) }}
                                    </p>
                                @else
                                    <p>${{ number_format((float) $product->price, 2) }}</p>
                                @endif
                            </td>
                            <td class="cart_total account-products-action-cell">
                                <div class="account-products-actions">
                                    <a href="{{ route('account.products.edit', $product) }}" class="btn btn-primary btn-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('account.products.destroy', $product) }}" method="POST"
                                        class="account-products-delete-form"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Bạn chưa có mặt hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
@endsection
