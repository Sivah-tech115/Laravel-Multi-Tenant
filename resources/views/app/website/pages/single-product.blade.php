@extends('app.website.website-layouts.main')
@section('content')
@section('meta_title', $product->meta_title ?? 'offers | Compraspesa')
@section('meta_keywords', $product->keyword ?? 'offers | Compraspesa')
@section('meta_description', $product->meta_description ?? 'offers | Compraspesa')

<section class="single_pro_section">
    <div class="container">
        <div class="pro_gallery">
            <img src="{{$product->merchant_image_url}}" alt="">
        </div>
        <div class="pro_data">
            <div class="breadcrumb">
                <a href="{{route('tanant.website')}}">Shop</a>
                <span>/</span>
                @if($product->merchant->merchant_name)
                <a href="{{ route('category.product', [Str::slug($product->merchant->merchant_name)]) }}">{{$product->merchant->merchant_name}}</a>
                <span>/</span>
                @endif
                @if($product->merchant_product_category_path)
                <a href="{{ route('category.product', [
                                        Str::slug($product->merchant->merchant_name),
                                        Str::slug($product->merchant_product_category_path)
                                    ]) }}">{{$product->merchant_product_category_path}}</a>
                <span>/</span>
                @endif
                @if($product->merchant_product_second_category)
                <a href="{{ route('category.product', [
                                        Str::slug($product->merchant->merchant_name),
                                        Str::slug($product->merchant_product_category_path),
                                           Str::slug($product->merchant_product_second_category)
                                    ]) }}">{{$product->merchant_product_second_category}}</a>
                <span>/</span>
                @endif
                <span>{{$product->product_name}}</span>

            </div>

            <h2>{{$product->product_name}}</h2>
            <span class="pro_price regu_price">€ {{$product->search_price}}</span>
            @if(!empty($product->brand->brand_name))
            <span class="Pro_meta">Brand: {{$product->brand->brand_name}}</span>
            @endif
            <p class="pro_description">{{$product->description}}</p>
            <a href="{{$product->merchant_deep_link}}" target="_blank" class="btn">{{ t('product.view_offer') }}</a>
        </div>
    </div>
</section>
<section class="related_pro_section">
    <div class="container">
        <h2>{{ t('product.related_Products') }}</h2>
        <ul class="product_grid relatedpro_slider">
            @foreach($relatedProducts as $relatedProduct)
            <li class="pro_box">
                <a href="{{ route('single.product', ['slug' => $relatedProduct->slug]) }}" class="pro_img">
                    <img src="{{ $relatedProduct->merchant_image_url }}" alt="">
                </a>
                <div class="pro_content">
                    <h5><a href="{{ route('single.product', ['slug' => $relatedProduct->slug]) }}">{{ $relatedProduct->product_name }}</a></h5>
                    <span class="pro_price regu_price">€{{ $relatedProduct->search_price }}</span>
                    <a href="{{$relatedProduct->merchant_deep_link}}" target="_blank" class="btn">{{ t('product.view_offer') }}</a>
                </div>
            </li>

            @endforeach
        </ul>
    </div>
</section>


@endsection

@section('scripts')
<script>
    // Get the count of related products from Blade
    let relatedProductsCount = {{ count($relatedProducts) }};

    // Determine how many slides to show, max 4
    let slidesToShow = relatedProductsCount >= 4 ? 4 : relatedProductsCount;

    $('.relatedpro_slider').slick({
        dots: true,
        arrows: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2000,
        slidesToShow: slidesToShow,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1025,
                settings: {
                    slidesToShow: Math.min(slidesToShow, 3),
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: Math.min(slidesToShow, 2),
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 601,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
</script>

@endsection