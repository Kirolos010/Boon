@extends('layouts.app')

@section('title', 'المنتجات')
@section('navbar-title', 'إدارة المنتجات')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">المنتجات</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">المنتجات</h1>
            <p class="page-title-subtitle">إدارة وتحديث جميع منتجات المتجر</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> منتج جديد
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" icon="check-circle" />
    @endif
    @if(session('error'))
        <x-alert message="{{ session('error') }}" type="danger" icon="exclamation-circle" />
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الكود..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">-- جميع الفئات --</option>
                        <option value="coffee" {{ request('category') == 'coffee' ? 'selected' : '' }}>القهوة</option>
                        <option value="tea" {{ request('category') == 'tea' ? 'selected' : '' }}>الشاي</option>
                        <option value="herbs" {{ request('category') == 'herbs' ? 'selected' : '' }}>الأعشاب</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="in-stock" {{ request('status') == 'in-stock' ? 'selected' : '' }}>متوفر</option>
                        <option value="low-stock" {{ request('status') == 'low-stock' ? 'selected' : '' }}>حد أدنى</option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>نفد</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>اسم المنتج</th>
                        <th>الكود</th>
                        <th>الفئة</th>
                        <th>الكمية (كج)</th>
                        <th>سعر الشراء</th>
                        <th>سعر البيع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name_ar ?? $product->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $product->sku }}</code>
                            </td>
                            <td>{{ $product->subCategory->name_ar ?? 'N/A' }}</td>
                            <td>{{ $product->current_stock_kg }} كج</td>
                            <td>{{ number_format($product->purchase_price_per_kg, 2) }} ر.س</td>
                            <td>{{ number_format($product->selling_price_per_kg, 2) }} ر.س</td>
                            <td>
                                @if($product->current_stock_kg > $product->minimum_stock_alert)
                                    <span class="badge badge-success">متوفر</span>
                                @elseif($product->current_stock_kg > 0)
                                    <span class="badge badge-warning">حد أدنى</span>
                                @else
                                    <span class="badge badge-danger">نفد</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا توجد منتجات</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            {{ $products->links() }}
        @endif
    </x-card>
@endsection
