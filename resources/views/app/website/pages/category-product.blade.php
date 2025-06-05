@extends('app.website.website-layouts.main')
@section('content')
<style>
    .sub_category_list {
        display: none;
        /* Default: hide */
    }

    .sub_category_list.active {
        display: block;
        /* Show when 'active' class is added */
    }
</style>
<section class="page_banner banner_bg">
    <div class="container">
        <div class="banner_txt">
            <h1>{{$merchantquery}}</h1>
            <p>Shop now, not later. Browse the best of our favorite sale styles and brands.</p>
            <div class="breadcrumb">
                <a href="{{route('tanant.website')}}">Shop</a>
                <span>/</span>
                <span>{{$merchantquery}}</span>
                @if ($categoryquery)
                <span>/</span>
                <span>{{ $categoryquery }}</span>
                @endif

                @if ($subsubcategoryquery)
                <span>/</span>
                <span>{{ $subsubcategoryquery }}</span>
                @endif

            </div>
        </div>
    </div>
</section>
<section class="products_section">
    <div class="container">
        <div class="sidebar">
            <div class="filter_list">
                <div class="filter_box">
                    <h6>{{ t('shop.product_categories') }}</h6>
                    <ul class="category_list">
                        @foreach($merchants as $merchantData)
                        @if($merchantData->merchant_name)
                        @php
                        $isMerchantActive = $merchantData->merchant_name === $merchantquery;
                        @endphp
                        <li class="have_sub_cat {{ $isMerchantActive ? 'open' : '' }}">
                            <a href="{{ route('category.product', [Str::slug($merchantData->merchant_name)]) }}"
                                class="collec_item {{ $isMerchantActive ? 'active' : '' }}">
                                {{ $merchantData->merchant_name }}
                                <i class="fa-solid fa-angle-down toggle_arrow"></i>
                            </a>

                            <ul class="sub_category_list {{ $isMerchantActive ? 'active' : '' }}">
                                @foreach($merchantData->subcategories as $subcategory)
                                @if($subcategory->name)
                                @php
                                $isSubActive = isset($categoryquery) && $subcategory->name === $categoryquery;
                                $hasChildren = $subcategory->children->count() > 0;
                                $isSubOpen = $isSubActive || (isset($subsubcategoryquery) && $subcategory->children->contains('name', $subsubcategoryquery));
                                @endphp
                                <li class="{{ $hasChildren ? 'have_subsub_cat' : '' }} {{ $isSubOpen ? 'open' : '' }}">
                                    <a href="{{ route('category.product', [
                                        Str::slug($merchantData->merchant_name),
                                        Str::slug($subcategory->name)
                                    ]) }}" class="collec_item {{ $isSubActive ? 'active' : '' }}">
                                        <i class="fa fa-hashtag" aria-hidden="true"></i>

                                        {{ $subcategory->name }}
                                        @if($hasChildren)
                                        <i class="fa-solid fa-caret-right sub_toggle_arrow"></i>
                                        @endif
                                    </a>

                                    @if($hasChildren)
                                    <ul class="subsub_category_list" style = "{{ $isSubOpen ? 'display: block;' : 'display: none;' }}">
                                        @foreach($subcategory->children as $child)
                                        @if($child->name)
                                        @php
                                        $isChildActive = isset($subsubcategoryquery) && $child->name === $subsubcategoryquery;
                                        @endphp
                                        <li>
                                            <a href="{{ route('category.product', [
                                                            Str::slug($merchantData->merchant_name),
                                                            Str::slug($subcategory->name),
                                                            Str::slug($child->name)
                                                        ]) }}" class="collec_item {{ $isChildActive ? 'active' : '' }}">
                                                <i class="fa fa-circle" style="font-size: 8px;" aria-hidden="true"></i>

                                                {{ $child->name }}
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
        <div class="main_content">
            <div class="main_heading">
                <h3>Shop Now</h3>
                <div class="sorting_options">
                    <form action="{{ route('category.product',
                    [
                        Str::slug($merchantData->merchant_name),
                        isset($categoryquery) ? Str::slug($categoryquery) : '',
                        isset($subsubcategoryquery) ? Str::slug($subsubcategoryquery) : '',
                    ]) }}" method="GET">
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


            <!-- <div class="pagination"> -->
                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
            <!-- </div> -->
        </div>
    </div>
</section>

@endsection