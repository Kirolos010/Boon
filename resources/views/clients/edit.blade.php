@extends('layouts.app')

@section('title', 'تعديل العميل')
@section('navbar-title', 'تعديل العميل')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">العملاء</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <x-card>
                @slot('header')
                    <i class="fas fa-edit"></i> تعديل بيانات العميل
                @endslot

                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group mb-3">
                        <label for="name_ar" class="form-label"><strong>اسم العميل</strong></label>
                        <input type="text" name="name_ar" id="name_ar" value="{{ old('name_ar', $client->name_ar) }}" class="form-control form-control-lg" required>
                        @error('name_ar')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone" class="form-label"><strong>رقم الهاتف</strong></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" class="form-control form-control-lg" required>
                        @error('phone')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="address" class="form-label"><strong>العنوان</strong></label>
                        <textarea name="address" id="address" rows="3" class="form-control form-control-lg">{{ old('address', $client->address) }}</textarea>
                        @error('address')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="notes" class="form-label"><strong>ملاحظات</strong></label>
                        <textarea name="notes" id="notes" rows="4" class="form-control form-control-lg">{{ old('notes', $client->notes) }}</textarea>
                        @error('notes')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
@endsection
