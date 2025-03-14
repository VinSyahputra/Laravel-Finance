    <!-- Sidebar Start -->
    <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
          <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="/" class="text-nowrap logo-img">
              <img src="{{ asset('assets/images/logos/dark-logo.svg') }}" width="180" alt="" />
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
                <a class="sidebar-link" href="/" aria-expanded="false">
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

              <li class="sidebar-item">
                <a class="sidebar-link" href="/transactions" aria-expanded="false">
                  <span>
                    <i class="ti ti-businessplan"></i>
                  </span>
                  <span class="hide-menu">Transaction</span>
                </a>
              </li>
              
              <li class="sidebar-item">
                <a class="sidebar-link" href="/categories" aria-expanded="false">
                  <span>
                    <i class="ti ti-category-2"></i>
                  </span>
                  <span class="hide-menu">Category</span>
                </a>
              </li>
  
            </ul>
          </nav>
          <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
      </aside>
      <!--  Sidebar End -->