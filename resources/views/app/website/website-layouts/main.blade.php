
<!DOCTYPE html>
<html>

<head>
    <title>Shop - Compraspesa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" />
  
  
    <link rel="stylesheet" href="{{ asset('../../tenant/assets/css/style.css') }}">

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
</body>

</html>