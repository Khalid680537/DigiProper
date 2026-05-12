{{-- Subtle background pattern: dot grid with purple/orange tinted corners. --}}
{{-- Fixed to the viewport, sits behind page content. --}}
<div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    {{-- Purple tint blob, bottom-left --}}
    <div class="absolute -bottom-40 -left-40 h-[44rem] w-[44rem] rounded-full bg-primary-500/25 blur-3xl"></div>

    {{-- Orange tint blob, top-right --}}
    <div class="absolute -top-40 -right-40 h-[42rem] w-[42rem] rounded-full bg-accent-400/22 blur-3xl"></div>

    {{-- Dot grid overlay --}}
    <div class="absolute inset-0"
         style="background-image: radial-gradient(circle, var(--dot-color) 1px, transparent 1.5px);
                background-size: 22px 22px;"></div>
</div>
