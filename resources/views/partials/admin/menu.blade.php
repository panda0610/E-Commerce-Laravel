@php
$logo = asset(Storage::url('uploads/logo/'));
$company_logo = \App\Models\Utility::GetLogo();
$plan = \App\Models\Plan::where('id', \Auth::user()->plan)->first();
@endphp

<nav class="dash-sidebar light-sidebar transprent-bg">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}"
                    alt="{{ config('app.name', 'Storego') }}" class="logo logo-lg nav-sidebar-logo" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                @if (Auth::user()->type == 'Owner')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == 'storeanalytic' ? ' active' : 'collapsed' }}">
                        <a href="#" class="dash-link"><span class="dash-micon">
                                <i class="ti ti-home"></i>
                            </span><span class="dash-mtext">{{ __('Dashboard') }}</span><span
                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                        <ul
                            class="dash-submenu {{ Request::segment(1) == 'dashboard' || Request::segment(1) == 'storeanalytic' ? ' show' : '' }}">
                            <li class="dash-item {{ Request::route()->getName() == 'dashboard' ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li
                                class="dash-item {{ Request::route()->getName() == 'storeanalytic' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('storeanalytic') }}">{{ __('Store Analytics') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'product' || Request::segment(1) == 'product_categorie' ||Request::segment(1) == 'product_tax' ||Request::segment(1) == 'product-coupon' ||Request::segment(1) == 'shipping' ||Request::segment(1) == 'subscriptions' ||Request::segment(1) == 'custom-page' ||Request::segment(1) == 'blog'? ' active dash-trigger': 'collapsed' }}">
                        <a href="#" class="dash-link"><span class="dash-micon">
                                <i class="ti ti-layout-2"></i>
                            </span><span class="dash-mtext">{{ __('Shop') }}</span><span
                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu {{ Request::segment(1) == 'product.index' ? ' show' : '' }}">
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product.index' || Request::route()->getName() == 'product.create' || Request::route()->getName() == 'product.edit' || Request::route()->getName() == 'product.show'   || Request::route()->getName() == 'product.grid'? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product.index') }}">{{ __('Products') }}</a>
                            </li>
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product_categorie.index' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product_categorie.index') }}">{{ __('Product Category') }}</a>
                            </li>
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product_tax.index' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product_tax.index') }}">{{ __('Product Tax') }}</a>
                            </li>
                            <li
                                class="dash-item {{ Request::route()->getName() == 'product-coupon.index' || Request::route()->getName() == 'product-coupon.show' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('product-coupon.index') }}">{{ __('Product Coupon') }}</a>
                            </li>
                            <li
                                class="dash-item {{ Request::route()->getName() == 'subscriptions.index' ? ' active' : '' }}">
                                <a class="dash-link"
                                    href="{{ route('subscriptions.index') }}">{{ __('Subscriber') }}</a>
                            </li>
                            @if ($plan->shipping_method == 'on')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'shipping.index' ? ' active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('shipping.index') }}">{{ __('Shipping') }}</a>
                                </li>
                            @endif
                            @if ($plan->additional_page == 'on')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'custom-page.index' ? ' active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('custom-page.index') }}">{{ __('Custom Page') }}</a>
                                </li>
                            @endif
                            @if ($plan->blog == 'on')
                                <li
                                    class="dash-item {{ Request::route()->getName() == 'blog.index' ? ' active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('blog.index') }}">{{ __('Blog') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'customer.index' ||  Request::route()->getName() == 'customer.show' ? ' active dash-trigger ' : 'collapsed' }}">
                        <a href="{{ route('customer.index') }}"
                            class="dash-link {{ request()->is('customer.index') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i data-feather="user"></i>
                            </span><span class="dash-mtext">{{ __('Customers') }}</span></a>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'orders.index' ||  Request::route()->getName() == 'orders.show' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('orders.index') }}"
                            class="dash-link {{ request()->is('orders.index') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i data-feather="credit-card"></i>
                            </span><span class="dash-mtext">{{ __('Orders') }}</span></a>
                    </li>
                @endif
                @if (Auth::user()->type == 'super admin')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'dashboard' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('dashboard') }}"
                            class="dash-link {{ request()->is('dashboard') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i class="ti ti-home"></i>
                            </span><span class="dash-mtext">{{ __('Dashboard') }}</span></a>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'store-resource'  || Request::route()->getName() == 'store.grid' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('store-resource.index') }}"
                            class="dash-link {{ request()->is('store-resource') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i data-feather="user"></i>
                            </span><span class="dash-mtext">{{ __('Stores') }}</span></a>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'coupons' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('coupons.index') }}"
                            class="dash-link {{ request()->is('coupons') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i class="ti ti-tag"></i>
                            </span><span class="dash-mtext">{{ __('Coupons') }}</span></a>
                    </li>
                @endif
                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'plans' || Request::route()->getName() == 'stripe' ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="{{ route('plans.index') }}"
                        class="dash-link {{ request()->is('plans') ? 'active' : '' }}"><span class="dash-micon">
                            <i class="ti ti-trophy"></i>
                        </span><span class="dash-mtext">{{ __('Plans') }}</span></a>
                </li>
                @if (Auth::user()->type == 'super admin')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'plan_request' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('plan_request.index') }}"
                            class="dash-link {{ request()->is('plan_request') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i class="ti ti-brand-telegram"></i>
                            </span><span class="dash-mtext">{{ __('Plan Request') }}</span></a>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'email_template'  || Request::route()->getName() == 'manage.email.language' ? ' active dash-trigger' : 'collapsed' }}">
                        <a href="{{ route('email_template.index') }}"
                            class="dash-link {{ request()->is('email_template') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i class="ti ti-mail"></i>
                            </span><span class="dash-mtext">{{ __('Email Templates') }}</span></a>
                    </li>
                @endif
                @if (Auth::user()->type == 'super admin')
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'manage-language' ? ' active' : 'collapsed' }}">
                        <a href="{{ route('manage.language', [$currantLang]) }}"
                            class="dash-link {{ request()->is('manage-language') ? 'active' : '' }}"><span
                                class="dash-micon">
                                <i class="ti ti-world nocolor"></i>
                            </span><span class="dash-mtext">{{ __('Language') }}</span></a>
                    </li>
                @endif
                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'settings'  || Request::route()->getName() == 'store.editproducts'  ? ' active dash-trigger' : 'collapsed' }}">
                    <a href="{{ route('settings') }}"
                        class="dash-link {{ request()->is('settings') ? 'active' : '' }}">
                        <span class="dash-micon"> <i data-feather="settings"></i>
                        </span><span class="dash-mtext">
                            @if (Auth::user()->type == 'super admin')
                                {{ __('Settings') }}
                            @else
                                {{ __('Store Settings') }}
                            @endif
                        </span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
