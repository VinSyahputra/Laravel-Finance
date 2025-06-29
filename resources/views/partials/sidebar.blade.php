    <!-- Sidebar Start -->
    <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="/" wire:navigate class="text-nowrap logo-img">
                    <img src="{{ asset('assets/images/logos/new-logo2.png') }}" width="150" alt="" />
                </a>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                <ul id="sidebarnav">
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="/" wire:navigate aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">PAGES</span>
                    </li>
                    @can('view transactions')
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="/transactions" wire:navigate aria-expanded="false">
                                <span>
                                    <i class="ti ti-businessplan"></i>
                                </span>
                                <span class="hide-menu">Transaction</span>
                            </a>
                        </li>
                    @endcan

                    @can('view setting role')
                    <li class="sidebar-item {{ Request::is('settings*') ? 'active selected' : '' }}">
                        <a class="sidebar-link" href="{{ url('/settings/roles') }}" wire:navigate aria-expanded="false">
                            <span>
                                <i class="ti ti-settings"></i>
                            </span>
                            <span class="hide-menu">Setting</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
