@extends('app.website.website-layouts.main')
@section('content')

<section class="page_banner banner_bg">
    <div class="container">
        <div class="banner_txt">
            <h1>Product Categories</h1>
            <div class="breadcrumb">
                <a href="shop.html">Shop</a>
                <span>/</span>
                <span>Product Categories</span>
            </div>
        </div>
    </div>
</section>
<section class="category_section">
    <div class="container">
        <ul class="category_grid">
            @foreach($merchants as $category)
            <li class="category_box">
                <a href="{{ route('category.product', [Str::slug($category->merchant_name)]) }}" class="overlay"></a>
                <img src="{{ $category->image_url ? url($category->image_url) : asset('path/to/placeholder-image.png') }}" alt="{{ $category->merchant_name }}">
                <div class="collec_text">
                    <h3>{{ $category->merchant_name }}</h3>
                    <a href="{{ route('category.product', [Str::slug($category->merchant_name)]) }}">Shop Now</a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="pagination">
    {{ $merchants->links('pagination::bootstrap-4') }}
</div>
</section>

@endsection