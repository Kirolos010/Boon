<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-coffee"></i> الغــــالـــى
        </div>
        <div class="sidebar-tagline">🎋 للبن والأعشاب 🎋</div>
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>لوحة التحكم</span>
            </a>
        </li>

        <!-- Sales Section -->
        <li>
            <a href="javascript:void(0);" class="menu-toggle" data-toggle="sales">
                <i class="fas fa-shopping-cart"></i>
                <span>المبيعات</span>
                <i class="fas fa-chevron-left" style="margin-left: auto;"></i>
            </a>
            <ul class="submenu {{ request()->routeIs('invoices.*', 'quick-sales.*') ? 'show' : '' }}">
                <li><a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> الفواتير
                </a></li>
                <li><a href="{{ route('quick-sales.index') }}" class="{{ request()->routeIs('quick-sales.*') ? 'active' : '' }}">
                    <i class="fas fa-bolt"></i> البيع السريع
                </a></li>
            </ul>
        </li>

        <!-- Inventory Section -->
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>المخزون</span>
            </a>
        </li>

        {{-- Inventory Section with submenu (commented out)
        <li>
            <a href="javascript:void(0);" class="menu-toggle" data-toggle="inventory">
                <i class="fas fa-boxes"></i>
                <span>المخزون</span>
                <i class="fas fa-chevron-left" style="margin-left: auto;"></i>
            </a>
            <ul class="submenu {{ request()->routeIs('products.*', 'purchases.*') ? 'show' : '' }}">
                <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i> المنتجات
                </a></li>
                <li><a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="fas fa-dolly"></i> طلبات الشراء
                </a></li>
            </ul>
        </li>
        --}}

        <!-- Clients -->
        <li>
            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>العملاء</span>
            </a>
        </li>

        <!-- Expenses -->
        <li>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>النفقات</span>
            </a>
        </li>

        <!-- Reports Section -->
        <li>
            <a href="javascript:void(0);" class="menu-toggle" data-toggle="reports">
                <i class="fas fa-chart-bar"></i>
                <span>التقارير</span>
                <i class="fas fa-chevron-left" style="margin-left: auto;"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('reports.sales') }}">
                    <i class="fas fa-chart-line"></i> تقرير المبيعات
                </a></li>
                <li><a href="{{ route('reports.profit') }}">
                    <i class="fas fa-chart-pie"></i> تقرير الأرباح
                </a></li>
                <li><a href="{{ route('reports.inventory') }}">
                    <i class="fas fa-boxes"></i> تقرير المخزون
                </a></li>
            </ul>
        </li>

        <hr style="opacity: 0.2; margin: 15px 0;">

        <!-- Settings -->
        <li>
            <a href="javascript:void(0);" class="menu-toggle" data-toggle="settings">
                <i class="fas fa-cog"></i>
                <span>الإعدادات</span>
                <i class="fas fa-chevron-left" style="margin-left: auto;"></i>
            </a>
            <ul class="submenu {{ request()->routeIs('settings.*') ? 'show' : '' }}">
                <li><a href="{{ route('settings.users.index') }}" class="{{ request()->routeIs('settings.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> إدارة المستخدمين
                </a></li>
                <li><a href="{{ route('settings.categories.index') }}" class="{{ request()->routeIs('settings.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> الفئات
                </a></li>
                {{-- <li><a href="{{ route('settings.suppliers.index') }}" class="{{ request()->routeIs('settings.suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> الموردون
                </a></li> --}}
            </ul>
        </li>

        @if(auth()->user()->isAdmin())
        <!-- Admin Only -->
        {{-- <li>
            <a href="#">
                <i class="fas fa-shield-alt"></i>
                <span>إدارة النظام</span>
            </a>
        </li> --}}
        @endif
    </ul>

    <style>
        .sidebar-menu > li > a {
            display: flex;
            gap: 10px;
        }

        .sidebar-menu .submenu li a {
            display: flex;
            gap: 8px;
        }

        /* Arrow Rotation Animation */
        .menu-toggle .fa-chevron-left {
            transition: transform 0.3s ease;
        }

        /* Submenu items indent with bullet */
        .sidebar-menu .submenu li a {
            padding-right: 45px !important;
            position: relative;
        }

        .sidebar-menu .submenu li a::before {
            content: '';
            position: absolute;
            right: 28px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
        }
    </style>
</div>
