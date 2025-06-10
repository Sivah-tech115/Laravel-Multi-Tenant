<ul class="product_grid">
    @forelse ($products as $item)
    <li class="pro_box">
        <!-- Link to the single product page, dynamically using the product's slug -->
        <a href="{{ route('single.product', ['slug' => $item->slug]) }}" class="pro_img" >
            <img src="{{ $item->merchant_image_url }}" alt="{{ $item->product_name }}" loading="lazy">
        </a>
        <div class="pro_content">
            <h5><a href="{{ route('single.product', ['slug' => $item->slug]) }}">{{ $item->product_name }}</a></h5>
            <span class="pro_price regu_price">€{{ $item->search_price }}</span>
            <span class="pro_price regu_price" style="display: none;">{{ $item->id }}</span>

            <a href="{{ $item->aw_deep_link }}" target="_blank" class="btn">{{ t('product.view_offer') }}</a>
        </div>
    </li>
    @empty
    <li class="no_data_found">
        <span>{{ t('product.no_data_found') }}</span>
    </li>
    @endforelse
</ul>


<!-- <div class="pagination"> -->
    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
<!-- </div> -->