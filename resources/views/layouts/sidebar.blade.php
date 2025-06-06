    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar  menu-light icon-colored brand-info">
        <div class="navbar-wrapper">
            <div class="navbar-brand header-logo">
                <a href="#" class="b-brand">
                    <img src="{{ asset('../../tenant/assets/images/logo.webp') }}" alt="" class="logo images">
                    <img src="{{ asset('../../tenant/assets/images/logo.webp') }}" alt="" class="logo-thumb images">
                </a>
                <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            </div>
            <div class="navbar-content scroll-div">
                <ul class="nav pcoded-inner-navbar">
       
                    <li class="nav-item ">
                        <a href="{{route('tenants.index')}}" class="nav-link">
                            <span class="pcoded-micon">
                                <i class="feather icon-user"></i>
                            </span>
                            <span class="pcoded-mtext">Tenants</span>
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a href="{{route('tenants.create')}}" class="nav-link">
                            <span class="pcoded-micon">
                                <i class="feather icon-user"></i>
                            </span>
                            <span class="pcoded-mtext">Create Tenants</span>
                        </a>
                    </li>
      
                </ul>
            </div>
        </div>
    </nav>