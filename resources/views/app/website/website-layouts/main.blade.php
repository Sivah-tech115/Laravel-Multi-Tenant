<!DOCTYPE html>
<html>

<head>
    <title>Shop - Compraspesa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if (request()->is('product/*'))

    <meta name="title" content="@yield('meta_title', 'Compraspesa')" />
    <meta name="description" content="@yield('meta_description', 'Compraspesa')" />
    <meta name="keywords" content="@yield('meta_keywords', 'Compraspesa')" />

    @else

    <meta name="title" content="{{ setting('meta_title', 'Default title') }}" />
    <meta name="description" content="{{ setting('meta_description', 'Default description') }}" />
    <meta name="keywords" content="{{ setting('meta_keywords', 'Default keywords') }}" />

    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" />
    <link rel="stylesheet" href="{{ asset('../../tenant/assets/css/style.css') }}">
    {!! App\Models\SeoSetting::first()->header_scripts ?? '' !!}
</head>

<body>


    <div class="wrapper">
        @include('app.website.website-layouts.header')
        @yield('content')
        @include('app.website.website-layouts.footer')

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>

    <script src="{{ asset('../../tenant/assets/js/script.js') }}"></script>
    {!! App\Models\SeoSetting::first()->footer_scripts ?? '' !!}
</body>

</html>