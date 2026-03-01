{{-- Stat Card Component --}}
<div class="stat-card">
    <div class="stat-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="stat-label">{{ $label }}</div>
    <div class="stat-value">{{ $value }}</div>
    @if($change ?? false)
        <div class="stat-change {{ $changeClass ?? 'positive' }}">
            <i class="fas fa-arrow-{{ ($changeClass ?? 'positive') === 'positive' ? 'up' : 'down' }}"></i>
            {{ $change }}
        </div>
    @endif
</div>
