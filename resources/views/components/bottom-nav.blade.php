@php
    $items = [
        ['route' => 'dashboard',        'pattern' => 'dashboard',     'icon' => 'home',            'label' => 'Home'],
        ['route' => 'properties.index', 'pattern' => 'properties.*',  'icon' => 'building-office', 'label' => 'Properties'],
        ['route' => 'profile.edit',     'pattern' => 'profile.*',     'icon' => 'user',            'label' => 'Profile'],
    ];
@endphp

@unless (request()->routeIs('properties.create', 'properties.edit'))
<nav {{ $attributes->merge(['class' => 'fixed inset-x-0 bottom-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-t border-ink-100 dark:border-ink-800 pb-[env(safe-area-inset-bottom)]']) }}>
    <ul class="relative grid grid-cols-4 px-2 py-1.5 gap-1">
        @foreach ($items as $i => $item)
            @php $active = request()->routeIs($item['pattern']); @endphp
            <li>
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center justify-center gap-0.5 min-h-[3rem] py-2 rounded-xl transition ease-spring duration-150 {{ $active ? 'bg-primary-50 dark:bg-primary-950/50 text-primary-700 dark:text-primary-200 -translate-y-0.5' : 'text-ink-500 dark:text-ink-400' }}">
                    <x-icon :name="$item['icon']" :solid="$active" class="h-6 w-6" />
                    <span class="text-xs font-semibold">{{ $item['label'] }}</span>
                </a>
            </li>
            @if ($i === 1)
                {{-- Empty slot reserved for the floating + button so the grid stays aligned --}}
                <li aria-hidden="true"></li>
            @endif
        @endforeach
    </ul>

    <a href="{{ route('properties.create') }}"
       aria-label="Add property"
       class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 inline-flex items-center justify-center h-14 w-14 rounded-full bg-primary-600 hover:bg-primary-500 text-white shadow-glow ring-4 ring-white dark:ring-gray-900 transition ease-spring duration-150 active:scale-95">
        <x-icon name="plus" class="h-6 w-6" />
    </a>
</nav>
@endunless
