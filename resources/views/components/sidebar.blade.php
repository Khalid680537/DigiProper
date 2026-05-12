@php
    $navItems = [
        ['route' => 'dashboard',        'pattern' => 'dashboard',     'icon' => 'home',            'label' => 'Dashboard'],
        ['route' => 'properties.index', 'pattern' => 'properties.*',  'icon' => 'building-office', 'label' => 'Properties'],
        ['route' => 'profile.edit',     'pattern' => 'profile.*',     'icon' => 'user',            'label' => 'Profile'],
    ];
    $user = auth()->user();
    $initial = $user ? strtoupper(mb_substr($user->name, 0, 1)) : '';
@endphp

<aside {{ $attributes->merge(['class' => 'w-64 shrink-0 flex-col bg-white dark:bg-gray-900 border-r border-ink-100 dark:border-ink-800 md:sticky md:top-0 md:h-screen']) }}>
    {{-- Logo header --}}
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 h-16 border-b border-ink-100 dark:border-ink-800">
        <span class="h-9 w-9 rounded-xl bg-hero-purple flex items-center justify-center shadow-glow-sm ring-1 ring-white/20">
            <x-application-logo class="h-5 w-5 fill-current text-white" />
        </span>
        <span class="inline-flex items-center gap-0.5">
            <span class="font-extrabold text-lg tracking-tight text-accent-600 dark:text-accent-400">Digi<span class="text-accent-700 dark:text-accent-300 underline decoration-blue-500 decoration-2 underline-offset-2">Proper</span></span>
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-3.5 w-3.5 -translate-y-1.5 text-primary-500 dark:text-primary-400">
                <path d="M12 2Q13 11 22 12Q13 13 12 22Q11 13 2 12Q11 11 12 2Z"/>
            </svg>
        </span>
    </a>

    {{-- Nav --}}
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        @foreach ($navItems as $item)
            @php $active = request()->routeIs($item['pattern']); @endphp
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition ease-spring duration-150 {{ $active ? 'bg-primary-600 text-white shadow-glow-sm' : 'text-ink-600 dark:text-ink-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-200' }}">
                <x-icon :name="$item['icon']" :solid="$active" class="h-5 w-5 {{ $active ? 'text-white' : 'text-ink-400 group-hover:text-primary-600 dark:group-hover:text-primary-300' }}" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- User card --}}
    @if ($user)
        <div class="m-3 p-3 rounded-2xl bg-surface-100 dark:bg-gray-800 ring-1 ring-ink-100 dark:ring-ink-800 flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-700 text-white font-bold flex items-center justify-center shrink-0">
                {{ $initial }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-ink-800 dark:text-ink-100 truncate">{{ $user->name }}</p>
                <p class="text-xs text-ink-500 dark:text-ink-400 truncate">{{ $user->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Sign out" class="inline-flex items-center justify-center h-9 w-9 rounded-full text-ink-500 hover:bg-white dark:hover:bg-gray-700 hover:text-red-600 transition">
                    <x-icon name="logout" class="h-4 w-4" />
                </button>
            </form>
        </div>
    @endif
</aside>
