{{--
    Replaceable logo placeholder.
    TO USE THE OFFICIAL CvSU LOGO LATER:
      1. Put the file in public/images/cvsu-logo.png
      2. Set $src below to asset('images/cvsu-logo.png')
    The box keeps its size and uses object-fit: contain so the logo's original
    aspect ratio is never stretched. No unofficial logo is generated here.
--}}
@props(['size' => 'md', 'variant' => 'dark', 'src' => null])
@php
    $classes = 'logo-slot'
        . ($size === 'lg' ? ' logo-slot--lg' : '')
        . ($variant === 'light' ? ' logo-slot--light' : '');
@endphp
<div {{ $attributes->merge(['class' => $classes]) }} role="img" aria-label="Cavite State University logo placeholder">
    @if ($src)
        <x-logo src="{{ asset('images/cvsu-logo.png') }}" />
    @else
        <span>CvSU<br>LOGO</span>
    @endif
</div>
