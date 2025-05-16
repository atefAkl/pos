<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام نقاط البيع') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white">
            <div class="sidebar-header p-3 border-bottom">
                <h3 class="fs-5 mb-0">نظام نقاط البيع</h3>
            </div>

            <ul class="list-unstyled p-3">
                <li class="mb-2">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('home') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-home me-2"></i> الرئيسية
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('products.index') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('products.*') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-box me-2"></i> المنتجات
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('categories.index') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('categories.*') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-tags me-2"></i> الفئات
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('customers.index') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('customers.*') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-users me-2"></i> العملاء
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('pos.index') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('pos.*') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-cash-register me-2"></i> نقطة البيع
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('invoices.index') }}" class="text-white text-decoration-none d-block p-2 rounded {{ request()->routeIs('invoices.*') ? 'bg-primary' : 'hover-bg-primary' }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i> الفواتير
                    </a>
                </li>
                {{-- سيتم تفعيل هذا الرابط عند تنفيذ واجهات التقارير --}}
                <li class="mb-2">
                    <a href="#" class="text-white text-decoration-none d-block p-2 rounded hover-bg-primary">
                        <i class="fas fa-chart-bar me-2"></i> التقارير
                    </a>
                </li>
                @can('manage-users')
                <li class="mb-2">
                    <a href="#" class="text-white text-decoration-none d-block p-2 rounded hover-bg-primary">
                        <i class="fas fa-users-cog me-2"></i> المستخدمين
                    </a>
                </li>
                @endcan
                @can('manage-roles')
                <li class="mb-2">
                    <a href="#" class="text-white text-decoration-none d-block p-2 rounded hover-bg-primary">
                        <i class="fas fa-user-shield me-2"></i> الصلاحيات
                    </a>
                </li>
                @endcan
                <li class="mb-2">
                    <a href="#" class="text-white text-decoration-none d-block p-2 rounded hover-bg-primary">
                        <i class="fas fa-cog me-2"></i> الإعدادات
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content" class="flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-link">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random" alt="" width="32" height="32" class="rounded-circle me-2">
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> الملف الشخصي</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script src="{{ asset('build/assets/app.js') }}" defer></script>

    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
