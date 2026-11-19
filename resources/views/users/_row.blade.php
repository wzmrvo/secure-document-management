{{-- One user table row. Used by the page loop AND by the <template> that JS clones when adding a user. --}}
@php
    /** @var array $user  name, email, role, status, date (ISO) */
    $initials = collect(explode(' ', preg_replace('/^Dr\.\s+/', '', $user['name'])))->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
    $dateLabel = $user['date'] ? \Carbon\Carbon::parse($user['date'])->format('M j, Y') : '';
    $isActive = $user['status'] === 'Active';
@endphp
<tr data-row data-name="{{ $user['name'] }}" data-email="{{ $user['email'] }}" data-role="{{ $user['role'] }}" data-status="{{ $user['status'] }}" data-date="{{ $user['date'] }}">
    <td>
        <div class="user-cell">
            <span class="avatar avatar--blue js-initials" aria-hidden="true">{{ $initials }}</span>
            <div>
                <div class="cell-doc__title js-name">{{ $user['name'] }}</div>
                <div class="cell-doc__sub js-email">{{ $user['email'] }}</div>
            </div>
        </div>
    </td>
    <td class="link-text js-role">{{ $user['role'] }}</td>
    <td><x-badge :type="$isActive ? 'success' : 'neutral'" class="js-status">{{ $user['status'] }}</x-badge></td>
    <td class="js-date">{{ $dateLabel }}</td>
    <td>
        <div class="row-actions" style="gap: var(--space-3);">
            <button type="button" class="text-link-btn" data-user-action="edit">Edit</button>
            <button type="button" class="text-link-btn {{ $isActive ? 'text-link-btn--danger' : '' }} js-toggle" data-user-action="toggle">{{ $isActive ? 'Deactivate' : 'Activate' }}</button>
        </div>
    </td>
</tr>
