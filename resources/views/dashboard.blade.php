@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@php
    /*
     |----------------------------------------------------------------------
     | MOCK DATA (frontend phase). Replace with controller data later:
     | return view('dashboard', compact('stats', 'chart', 'recentDocuments', ...));
     |----------------------------------------------------------------------
     */
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    $stats = [
        ['label' => 'Total documents',          'value' => '1,248', 'note' => '↑ 32 added this month', 'up' => true],
        ['label' => 'Active documents',         'value' => '1,186', 'note' => '95% of all documents',  'up' => false],
        ['label' => 'Pending access requests',  'value' => '12',    'note' => 'Awaiting your review',   'up' => false],
        ['label' => 'Registered users',         'value' => '256',   'note' => 'Across 3 roles',         'up' => false],
    ];

    // Last 7 days: [label, uploaded, downloaded]
    $chart = [
        ['Mon', 12, 20], ['Tue', 18, 14], ['Wed', 9, 25], ['Thu', 22, 18],
        ['Fri', 15, 30], ['Sat', 6, 10], ['Sun', 8, 12],
    ];
    $chartMax = max(array_map(fn ($day) => max($day[1], $day[2]), $chart));

    $recentDocuments = [
        ['title' => 'Faculty handbook 2026.pdf',    'meta' => 'Academic · Sarah Chen', 'class' => 'Confidential'],
        ['title' => 'IT security policy.pdf',       'meta' => 'Policies · Alex Morgan', 'class' => 'Internal'],
        ['title' => 'Q4 financial report.xlsx',     'meta' => 'Finance · James Wilson', 'class' => 'Restricted'],
        ['title' => 'Campus safety guidelines.pdf', 'meta' => 'Policies · Maria Santos', 'class' => 'Public'],
    ];
    $classTone = ['Public' => 'neutral', 'Internal' => 'info', 'Confidential' => 'gold', 'Restricted' => 'danger'];

    $activities = [
        ['title' => 'Sarah Chen uploaded a document',        'sub' => 'Faculty handbook 2026', 'time' => '2 min ago'],
        ['title' => 'Dr. Emily Davis requested access',      'sub' => 'Q4 financial report',   'time' => '18 min ago'],
        ['title' => 'James Wilson downloaded a document',    'sub' => 'IT security policy',    'time' => '1 hour ago'],
        ['title' => 'Alex Morgan approved an access request','sub' => 'Campus safety guidelines','time' => '3 hours ago'],
    ];
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $greeting }}, Alex</h1>
            <p>Here's what's happening with your institution's documents.</p>
        </div>
        <x-button variant="primary" :href="route('documents.create')">Upload document</x-button>
    </div>

    {{-- Stat cards --}}
    <section class="grid grid--4" aria-label="Key figures">
        @foreach ($stats as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']" :note="$stat['note']" :up="$stat['up']" />
        @endforeach
    </section>

    <div class="grid grid--2-1" style="margin-top: var(--space-4);">
        {{-- Document activity --}}
        <section class="card" aria-labelledby="activity-chart-title">
            <div class="card__head">
                <h3 id="activity-chart-title">Document activity</h3>
                <span class="muted" style="font-size: var(--fs-sm);">Last 7 days</span>
            </div>
            <div class="card__body">
                <div class="activity-chart" role="img" aria-label="Grouped bar chart of documents uploaded and downloaded over the last 7 days">
                    @foreach ($chart as [$day, $uploaded, $downloaded])
                        <div class="activity-chart__col">
                            <div class="activity-chart__bars">
                                <div class="activity-chart__bar activity-chart__bar--uploaded" style="height: {{ round($uploaded / $chartMax * 100) }}%;" title="{{ $uploaded }} uploaded"></div>
                                <div class="activity-chart__bar activity-chart__bar--downloaded" style="height: {{ round($downloaded / $chartMax * 100) }}%;" title="{{ $downloaded }} downloaded"></div>
                            </div>
                            <span class="activity-chart__label">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="legend">
                    <span><span class="legend__swatch" style="background: var(--blue);"></span>Uploaded</span>
                    <span><span class="legend__swatch" style="background: var(--gold);"></span>Downloaded</span>
                </div>
            </div>
        </section>

        {{-- Security overview --}}
        <section class="card" aria-labelledby="security-title">
            <div class="card__head">
                <h3 id="security-title">Security overview</h3>
                <x-badge type="neutral">Sample data</x-badge>
            </div>
            <div class="card__body">
                <div class="security-score">
                    <x-icon name="shield-check" />
                    <span class="security-score__value">95% classified</span>
                </div>
                <div class="progress" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" aria-label="Documents with a classification">
                    <div class="progress__bar" style="width: 95%;"></div>
                </div>
                <p class="muted" style="font-size: var(--fs-sm); margin: var(--space-3) 0;">Sample figures: 1,186 of 1,248 documents have a classification. Real protection status must come from backend checks.</p>
                <a class="link-arrow" href="{{ route('settings.index') }}">View security settings <x-icon name="arrow-right" style="width:14px;height:14px;vertical-align:-2px;" /></a>
            </div>
        </section>
    </div>

    <div class="grid grid--2-1" style="margin-top: var(--space-4);">
        {{-- Recent documents --}}
        <section class="card" aria-labelledby="recent-docs-title">
            <div class="card__head">
                <h3 id="recent-docs-title">Recent documents</h3>
                <a class="link-arrow" href="{{ route('documents.index') }}">View all <x-icon name="arrow-right" style="width:14px;height:14px;vertical-align:-2px;" /></a>
            </div>
            <ul class="doc-list">
                @foreach ($recentDocuments as $doc)
                    <li class="doc-list__item">
                        <span class="file-icon"><x-icon name="file" /></span>
                        <div class="doc-list__main">
                            <div class="cell-doc__title">{{ $doc['title'] }}</div>
                            <div class="cell-doc__sub">{{ $doc['meta'] }}</div>
                        </div>
                        <x-badge :type="$classTone[$doc['class']]">{{ $doc['class'] }}</x-badge>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Recent activity --}}
        <section class="card" aria-labelledby="activity-title">
            <div class="card__head">
                <h3 id="activity-title">Recent activity</h3>
            </div>
            <ul class="activity-list">
                @foreach ($activities as $activity)
                    <li class="activity-item">
                        <div>
                            <div class="activity-item__text"><strong>{{ $activity['title'] }}</strong></div>
                            <div class="cell-doc__sub">{{ $activity['sub'] }}</div>
                            <div class="activity-item__time">{{ $activity['time'] }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
