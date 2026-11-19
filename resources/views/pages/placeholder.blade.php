{{--
    Temporary page used by modules that are scheduled for a later phase, so that sidebar
    navigation works end to end. Each module replaces its route with its own view when built.
--}}
@extends('layouts.app')

@section('title', $title)
@section('page_title', $title)

@section('content')
    <div class="card placeholder-page">
        <x-icon :name="$icon" />
        <h2>{{ $title }}</h2>
        <p class="muted" style="margin-top: 8px;">This module is scheduled for {{ $phase }}. Navigation, layout and components are already in place.</p>
        <div style="margin-top: 20px;">
            <x-button variant="primary" :href="route('dashboard')">Back to Dashboard</x-button>
        </div>
    </div>
@endsection
