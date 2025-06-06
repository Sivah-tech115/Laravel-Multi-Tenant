<ul class="category_list">
    @foreach($merchants as $merchantData)
    @if($merchantData->merchant_name)
    <li class="have_sub_cat {{ $merchantData->isActive ? 'open' : '' }}">
        <a href="{{ route('category.product', [Str::slug($merchantData->merchant_name)]) }}" class="collec_item {{ $merchantData->isActive ? 'active' : '' }}">
            {{ $merchantData->merchant_name }}
            <i class="fa-solid fa-angle-down toggle_arrow"></i>
        </a>
        <ul class="sub_category_list {{ $merchantData->isActive ? 'active' : '' }}">
            @foreach($merchantData->subcategories as $subcategory)
            <li class="{{ $subcategory->children->count() ? 'have_subsub_cat' : '' }} {{ $subcategory->isActive ? 'open' : '' }}">
                <a href="{{ route('category.product', [Str::slug($merchantData->merchant_name), Str::slug($subcategory->name)]) }}" class="collec_item {{ $subcategory->isActive ? 'active' : '' }}">
                    <i class="fa fa-hashtag" aria-hidden="true"></i>{{ $subcategory->name }}
                    @if($subcategory->children->count()) <i class="fa-solid fa-caret-right sub_toggle_arrow"></i> @endif
                </a>

                @if($subcategory->children->count())
                <ul class="subsub_category_list" style="{{ $subcategory->isActive ? 'display: block;' : 'display: none;' }}">
                    @foreach($subcategory->children as $child)
                    <li>
                        <a href="{{ route('category.product', [Str::slug($merchantData->merchant_name), Str::slug($subcategory->name), Str::slug($child->name)]) }}" class="collec_item {{ $child->isActive ? 'active' : '' }}">
                            <i class="fa fa-circle" style="font-size: 8px;" aria-hidden="true"></i>{{ $child->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </li>
    @endif
    @endforeach
</ul>
