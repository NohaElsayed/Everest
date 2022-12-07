<style>
    .navbar-vertical .nav-link {
        color: #041562;

    }

    .navbar .nav-link:hover {
        color: #041562;


    .navbar .active > .nav-link, .navbar .nav-link.active, .navbar .nav-link.show, .navbar .show > .nav-link {
        color: #F14A16;
    }

    .navbar-vertical .active .nav-indicator-icon, .navbar-vertical .nav-link:hover .nav-indicator-icon, .navbar-vertical .show > .nav-link > .nav-indicator-icon {
        color: #F14A16;
    }

    .nav-subtitle {
        display: block;
        color: #041562;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03125rem;
    }

    .side-logo {
        background-color: #ffffff;
    }

    .nav-sub {
        background-color: #ffffff!important;
    }

    .nav-indicator-icon {
        margin-left: {{Session::get('direction') === "rtl" ? '6px' : ''}};
    }
</style>
<div id="sidebarMain" class="d-none">
    <aside
        style="background: #ffffff!important;  font-weight: bold !important; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset" style="padding-bottom: 0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    {{-- @php($shop=\App\Model\Shop::where(['seller_id'=>auth('seller')->id()])->first()) --}}
                    <a class="navbar-brand" href="{{route('admin-delivery.dashboard')}}" aria-label="Front">
                        {{-- @if (isset($shop))
                            <img onerror="this.src='{{asset('public/assets/back-end/img/900x400/img1.jpg')}}'"
                                class="navbar-brand-logo-mini for-seller-logo"
                                src="{{asset("storage/app/public/shop/$shop->image")}}" alt="Logo">
                        @else
                            <img class="navbar-brand-logo-mini for-seller-logo"
                                src="{{asset('public/assets/back-end/img/900x400/img1.jpg')}}" alt="Logo">
                        @endif --}}
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                            class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                  <!-- End POS -->
                        {{-- @if(\App\CPU\Helpers::module_permission_check('order_management')) --}}
                            <li class="nav-item">
                                <small class="nav-subtitle" style=" font-size: 13px; font-weight: bold !important;" title="">{{\App\CPU\translate('order_management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <!-- Order -->
                            <li class="navbar-vertical-aside-has-menu">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-shopping-cart-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('orders')}}
                                </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                                    {{-- <li class="nav-item">
                                        <a class="nav-link" href="{{route('admin-delivery.orders.list',['all'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('All')}}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['pending'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'pending'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li> --}}
                                    {{-- <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['confirmed'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'confirmed'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['processing'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('Processing')}}
                                                <span class="badge badge-warning badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'processing'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li> --}}
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['out_for_delivery'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('out_for_delivery')}}
                                                <span class="badge badge-warning badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'out_for_delivery'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['delivered'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('delivered')}}
                                                <span class="badge badge-success badge-pill ml-1">
                                                    {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'delivered'])->count()}}
                                                </span>
                                        </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['returned'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'returned'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['failed'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('failed')}}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'failed'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link " href="{{route('admin-delivery.orders.list',['canceled'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                            {{\App\CPU\translate('canceled')}}
                                                <span class="badge badge-danger badge-pill ml-1">
                                                {{\App\Model\Order::where('order_type','default_type')->where(['order_status'=>'canceled'])->count()}}
                                            </span>
                                        </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                    
                    <!--order management ends-->
                     <li class="nav-item {{Request::is('admin/delivery-man*')?'scroll-here':''}}">
                                <small style=" font-size: 13px; font-weight: bold !important;" class="nav-subtitle">{{\App\CPU\translate('delivery_man_management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/delivery-man*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('delivery-man')}}
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                                    <li class="nav-item {{Request::is('admin/delivery-man/add')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin-delivery.delivery-man.add')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{\App\CPU\translate('add_new')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{route('admin-delivery.delivery-man.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{\App\CPU\translate('List')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

