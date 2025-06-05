@extends('app.website.website-layouts.main')
@section('content')

<section class="page_banner banner_bg">
    <div class="container">
        <div class="banner_txt">
            <h1>Search Result</h1>
            <div class="breadcrumb">
                <a href="shop.html">Shop</a>
                <span>/</span>
                <span>Search</span>
            </div>
        </div>
    </div>
</section>

<section class="searchresult_section">
    <div class="container">
        <div class="main_heading">
            <h3>Search Result For: {{request()->query('query')}}</h3>
            <div class="sorting_options">
                <form action="{{ route('tanant.website') }}" method="GET">
                    <input type="hidden" id="search" name="query" value="{{request()->query('query')}}" placeholder="Enter your search term" class="form_control" required>

                    <!-- Sorting options -->
                    <select name="sorting" id="sorting" onchange="this.form.submit()">
                        <option value="popularity" {{ request('sorting') == 'popularity' ? 'selected' : '' }}>{{ t('shop.sort.popularity') }}</option>
                        <option value="rating" {{ request('sorting') == 'rating' ? 'selected' : '' }}>{{ t('shop.sort.rating') }}</option>
                        <option value="newest" {{ request('sorting') == 'newest' ? 'selected' : '' }}>{{ t('shop.sort.newest') }}</option>
                        <option value="lowest_price" {{ request('sorting') == 'lowest_price' ? 'selected' : '' }}>{{ t('shop.sort.lowest_price') }}</option>
                        <option value="highest_price" {{ request('sorting') == 'highest_price' ? 'selected' : '' }}>{{ t('shop.sort.highest_price') }}</option>
                    </select>
                </form>

            </div>
        </div>


        <ul class="product_grid">
            @forelse ($products as $item)
            <li class="pro_box">
                <!-- Link to the single product page, dynamically using the product's slug -->
                <a href="{{ route('single.product', ['productSlug' => $item->slug]) }}" class="pro_img">
                    <img src="{{ $item->merchant_image_url }}" alt="{{ $item->product_name }}">
                </a>
                <div class="pro_content">
                    <h5><a href="{{ route('single.product', ['productSlug' => $item->slug]) }}">{{ $item->product_name }}</a></h5>
                    <span class="pro_price regu_price">€{{ $item->search_price }}</span>
                    <span class="pro_price regu_price" style="display: none;">{{ $item->id }}</span>
                    <a href="{{ $item->aw_deep_link }}" target="_blank" class="btn">{{ t('product.view_offer') }}</a>
                </div>
            </li>
            @empty
            <li class="no_data_found">
                <span>{{ t('No products found') }}</span>
            </li>
            @endforelse
        </ul>


        <div class="pagination">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>

    </div>
</section>

@endsection
