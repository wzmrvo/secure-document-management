{{--
    Usage:
    <x-modal id="logout-modal" title="Log out">
        Body content...
        <x-slot:footer> buttons here </x-slot:footer>
    </x-modal>
    Open with any element that has data-modal-open="#logout-modal".
    Close with any element that has data-modal-close (the backdrop, X and Esc already do).
--}}
@props(['id', 'title'])
<div class="modal" id="{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog">
        <div class="modal__head">
            <h3 id="{{ $id }}-title">{{ $title }}</h3>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close dialog"><x-icon name="x" /></button>
        </div>
        <div class="modal__body">{{ $slot }}</div>
        @isset($footer)
            <div class="modal__foot">{{ $footer }}</div>
        @endisset
    </div>
</div>
