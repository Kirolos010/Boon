@extends('layouts.app')

@section('title', 'المستخدمين')
@section('navbar-title', 'إدارة المستخدمين')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item active">المستخدمين</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">المستخدمين</h1>
            <p class="page-title-subtitle">إدارة مستخدمي النظام</p>
        </div>
        <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> مستخدم جديد
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert message="{{ session('success') }}" type="success" icon="check-circle" />
    @endif
    @if(session('error'))
        <x-alert message="{{ session('error') }}" type="danger" icon="exclamation-circle" />
    @endif

    <!-- Search Card -->
    <x-card title="بحث">
        <form method="GET" action="{{ route('settings.users.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="البحث عن... (الاسم، البريد)" value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-search"></i> بحث
            </button>
            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> مسح
            </a>
        </form>
    </x-card>

    <!-- Users Table -->
    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ handleRoleColor($user->role_id) }}">
                                    {{ $user->role->name_ar ?? 'بدون دور' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('settings.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger" disabled title="لا يمكن حذف حسابك">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">لا يوجد مستخدمين</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            {{ $users->links() }}
        @endif
    </x-card>
@endsection

@php
function handleRoleColor($roleId) {
    $colors = [
        1 => '#dc3545', // Admin
        2 => '#007bff', // Manager
        3 => '#28a745', // Employee
    ];
    return $colors[$roleId] ?? '#6c757d';
}
@endphp
