<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shop Bán Hàng')</title>

    <!-- Link CSS Frontend từ thư mục public -->
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.css') }}">
    @yield('css')
</head>
<body>

    <!-- 1. Header dùng chung -->
    @include('frontend.layouts.header')

    <!-- 2. Slider / Banner (Có thể bật/tắt tùy trang) -->
    @yield('slide')

    <div class="container my-4">
        <div class="row">
            <!-- 3. Menu Left (Nếu trang nào cần) -->
            @section('menu_left')
                <div class="col-lg-3">
                    @includeIf('frontend.layouts.menu_left')
                </div>
            @show

            <!-- 4. Nội dung thay đổi ở các trang con -->
            <div class="col-lg-9">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- 5. Footer dùng chung -->
    @include('frontend.layouts.footer')

    <script src="{{ asset('frontend/js/jquery.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('frontend/js/price-range.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.prettyPhoto.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
    @yield('js')
</body>
</html>