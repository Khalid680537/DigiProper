@php
    $items = [
        ['route' => 'dashboard',        'pattern' => 'dashboard',     'icon' => 'home',            'label' => 'Home'],
        ['route' => 'properties.index', 'pattern' => 'properties.*',  'icon' => 'building-office', 'label' => 'Properties'],
        ['route' => 'profile.edit',     'pattern' => 'profile.*',     'icon' => 'user',            'label' => 'Profile'],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'fixed inset-x-0 bottom-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-t border-ink-100 dark:border-ink-800 pb-[env(safe-area-inset-bottom)]']) }}>
    <ul class="grid grid-cols-3 px-2 py-2 gap-1">
        @foreach ($items as $item)
            @php $active = request()->routeIs($item['pattern']); @endphp
            <li>
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center gap-0.5 py-2 rounded-xl transition ease-spring duration-150 {{ $active ? 'bg-primary-50 dark:bg-primary-950/50 text-primary-700 dark:text-primary-200 -translate-y-0.5' : 'text-ink-500 dark:text-ink-400' }}">
                    <x-icon :name="$item['icon']" :solid="$active" class="h-6 w-6" />
                    <span class="text-[11px] font-semibold">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
