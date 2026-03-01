<div class="navbar">
    <div class="navbar-brand">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="cursor: pointer; display: none;" id="sidebarToggle"></i>
        <span>@yield('navbar-title', 'لوحة التحكم')</span>
    </div>

    <div class="navbar-end">
        <!-- Search Bar -->
        <div style="position: relative; display: none;" id="searchBar">
            <input type="text" class="form-control" placeholder="بحث..." style="width: 250px; border-radius: 20px;">
        </div>

        {{-- <!-- Notifications -->
        <div style="position: relative;">
            <a href="#" data-bs-toggle="dropdown" style="font-size: 20px; color: var(--coffee-dark); text-decoration: none;">
                <i class="fas fa-bell"></i>
                <span style="position: absolute; top: -5px; left: -5px; background: #dc3545; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">2</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                <li><a class="dropdown-item" href="#">
                    <i class="fas fa-warning"></i> منتج تحت الحد الأدنى للمخزون
                    <br><small class="text-muted">قبل 5 دقائق</small>
                </a></li>
                <li><a class="dropdown-item" href="#">
                    <i class="fas fa-check"></i> تم استلام طلب شراء
                    <br><small class="text-muted">قبل ساعة</small>
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="#">عرض جميع الإشعارات</a></li>
            </ul>
        </div> --}}

        <!-- User Profile -->
        <div class="user-profile" data-bs-toggle="dropdown">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-weight: 600; font-size: 14px; color: var(--coffee-dark);">{{ auth()->user()->name }}</div>
                <div style="font-size: 12px; color: var(--coffee-light);">{{ auth()->user()->role->name_ar ?? 'مستخدم' }}</div>
            </div>
        </div>

        <!-- User Dropdown Menu -->
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                <i class="fas fa-user"></i> الملف الشخصي
            </a></li>
            <li><a class="dropdown-item" href="{{ route('profile.change-password') }}">
                <i class="fas fa-key"></i> تغيير كلمة السر
            </a></li>
            {{-- <li><a class="dropdown-item" href="#">
                <i class="fas fa-bell"></i> الإشعارات
            </a></li> --}}
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width: 100%; text-align: right; border: none; background: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .dropdown-item {
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: var(--cream-medium);
        color: var(--coffee-dark);
    }

    .dropdown-item i {
        margin-left: 10px;
        width: 20px;
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('show');
    }

    // Show sidebar toggle button on mobile
    if (window.innerWidth <= 768) {
        document.getElementById('sidebarToggle').style.display = 'block';
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').style.display = 'block';
        } else {
            document.getElementById('sidebarToggle').style.display = 'none';
        }
    });
</script>
