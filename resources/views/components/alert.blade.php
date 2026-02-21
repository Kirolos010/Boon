{{-- Alert Component --}}
@if($message ?? false)
    <div class="alert alert-{{ $type ?? 'info' }} alert-dismissible fade show" role="alert">
        <i class="fas fa-{{ $icon ?? 'info-circle' }}" style="margin-left: 10px;"></i>
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
