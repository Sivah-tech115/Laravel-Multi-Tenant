@extends('app.website.website-layouts.main')
@section('content')

<section class="page_banner banner_bg hero_banner">
    <div class="container">
        <div class="banner_txt">
            <h1>Everything You Need – All in One Place!</span></h1>
            <p>From cutting-edge electronics to trendy fashion, smart gadgets, and must-haves for kids – shop the best at unbeatable prices.</p>
            <a href="{{route('categories.all')}}" class="btn white_btn">View Product Categories</a>
        </div>
    </div>
</section>

<section class="products_section">
    <div class="container">
        <div class="sidebar">
            <div class="filter_list">
                <div class="filter_box">
                    <h6>{{ t('shop.product_categories') }}</h6>
                    @include('app.website.partials.merchant-category', ['merchants' => $merchants])

                </div>
            </div>

        </div>
        <div class="main_content">
            <div class="main_heading">
                <h3>Shop Now</h3>
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

            @include('app.website.partials.products', ['products' => $products])
        </div>
    </div>
</section>

@endsection