{{-- One category card. Used by the page loop AND by the <template> that JS clones when adding a category. --}}
@php
    /** @var array $category  name, description, classification */
    /** @var int $count */
@endphp
<article class="card category-card" data-row data-name="{{ $category['name'] }}" data-classification="{{ $category['classification'] }}">
    <div class="category-card__top">
        <x-icon name="folder" class="icon category-card__icon" />
        <x-classification-badge :level="$category['classification']" class="js-class" />
    </div>
    <h3 class="js-name">{{ $category['name'] }}</h3>
    <p class="category-card__desc js-desc">{{ $category['description'] }}</p>
    <div class="category-card__foot">
        <a class="link-text js-count-link" href="{{ route('documents.index', ['category' => $category['name']]) }}"><span class="js-count">{{ $count }}</span> documents</a>
        <div class="category-card__links">
            <button type="button" class="text-link-btn" data-category-action="edit">Edit</button>
            <button type="button" class="text-link-btn text-link-btn--danger" data-category-action="delete">Delete</button>
        </div>
    </div>
</article>
