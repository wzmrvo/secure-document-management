{{-- Usage: <x-stat-card label="Total documents" value="1,248" note="+32 this month" :up="true" /> (icon optional, per Figma none is used) --}}
@props(['label', 'value', 'icon' => null, 'tone' => 'blue', 'note' => null, 'up' => false])
<div {{ $attributes->merge(['class' => 'card stat-card']) }}>
    @if ($icon)
        <div class="stat-card__icon stat-card__icon--{{ $tone }}"><x-icon :name="$icon" /></div>
    @endif
    <div>
        <div class="stat-card__label">{{ $label }}</div>
        <div class="stat-card__value">{{ $value }}</div>
        @if ($note)
            <div class="stat-card__note {{ $up ? 'stat-card__note--up' : '' }}">{{ $note }}</div>
        @endif
    </div>
</div>
