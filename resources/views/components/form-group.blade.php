{{-- Form Group Component --}}
<div class="form-group">
    @if($label ?? false)
        <label class="form-label" for="{{ $name ?? '' }}">
            {{ $label }}
            @if($required ?? false)
                <span style="color: #dc3545;">*</span>
            @endif
        </label>
    @endif

    @if(($type ?? '') === 'textarea' || ($type ?? '') === 'text-area')
        <textarea
            class="form-control @error($name ?? '') is-invalid @enderror"
            name="{{ $name ?? '' }}"
            id="{{ $name ?? '' }}"
            placeholder="{{ $placeholder ?? '' }}"
            rows="{{ $rows ?? 3 }}"
            {{ ($required ?? false) ? 'required' : '' }}>{{ old($name ?? '', $value ?? '') }}</textarea>
    @elseif(($type ?? '') === 'select')
        <select
            class="form-select @error($name ?? '') is-invalid @enderror"
            name="{{ $name ?? '' }}"
            id="{{ $name ?? '' }}"
            {{ ($required ?? false) ? 'required' : '' }}>
            <option value="">-- اختر --</option>
            @foreach($options ?? [] as $key => $option)
                <option value="{{ $key }}" {{ (old($name ?? '', $value ?? '') == $key) ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @else
        <input
            type="{{ $type ?? 'text' }}"
            class="form-control @error($name ?? '') is-invalid @enderror"
            name="{{ $name ?? '' }}"
            id="{{ $name ?? '' }}"
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ old($name ?? '', $value ?? '') }}"
            @if(isset($step)) step="{{ $step }}" @endif
            @if(isset($min)) min="{{ $min }}" @endif
            @if(isset($max)) max="{{ $max }}" @endif
            {{ ($required ?? false) ? 'required' : '' }}>
    @endif

    @error($name ?? '')
        <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
        </div>
    @enderror

    @if($help ?? false)
        <small class="form-text text-muted" style="margin-top: 5px; display: block;">
            {{ $help }}
        </small>
    @endif
</div>
