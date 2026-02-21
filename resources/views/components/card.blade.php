{{-- Resources/views/components/card.blade.php --}}
<div class="card {{ $class ?? '' }}">
    @if($header ?? false)
        <div class="card-header">
            {{ $header }}
        </div>
    @endif

    @if($slot)
        <div class="card-body">
            {{ $slot }}
        </div>
    @endif

    @if($footer ?? false)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
