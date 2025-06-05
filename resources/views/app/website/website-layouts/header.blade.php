<header>
        <nav class="container">
            <a href="{{route('tanant.website')}}" class="logo"><img src="{{ asset('../../tenant/assets/images/logo.webp') }}" alt="Compraspesa Logo"></a>
            <ul class="main_menu">
                <li class="menu_item"><a href="{{route('tanant.website')}}"><img src="{{ asset('../../tenant/assets/images/logo.webp') }}" alt="Compraspesa Logo"></a></li>
                <li class="menu_item active"><a href="{{route('tanant.website')}}">Shop</a></li>
                <li class="menu_item">
                    <a href="{{route('categories.all')}}">All Product Categories</a>
                </li>
            </ul>
            <ul class="header_icons">
                <li class="icon_items search">
                    <a href="#">
                        <svg aria-hidden="true" fill="none" focusable="false" width="24" class="svg-search"
                            viewBox="0 0 24 24">
                            <path d="M10.364 3a7.364 7.364 0 1 0 0 14.727 7.364 7.364 0 0 0 0-14.727Z"
                                stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"></path>
                            <path d="M15.857 15.858 21 21.001" stroke="currentColor" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round"></path>
                        </svg>
                    </a>
                </li>
            </ul>
            <span class="mobile_menu">
                <i class="fa-solid fa-bars open_menu"></i>
                <i class="fa-solid fa-xmark close_menu"></i>
            </span>
        </nav>
    </header>