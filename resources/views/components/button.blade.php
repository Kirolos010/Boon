{{-- Button Component --}}
<a href="{{ $href ?? '#' }}"
   class="btn btn-{{ $variant ?? 'primary' }} {{ $size ? 'btn-' . $size : '' }} {{ $class ?? '' }}"
   {{ $attributes }}>
    @if($icon ?? false)
        <i class="{{ $icon }}" style="margin-left: 5px;"></i>
    @endif
    {{ $slot }}
</a>
