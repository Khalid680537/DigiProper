{{-- Decorative purple-orange ribbons. Fixed to the viewport, sits behind all content. --}}
<div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 1440 900"
         preserveAspectRatio="xMidYMid slice"
         class="absolute inset-0 h-full w-full">
        <defs>
            <linearGradient id="ribbon-po" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"  stop-color="#684ed3" />
                <stop offset="55%" stop-color="#8e62d3" />
                <stop offset="100%" stop-color="#d97757" />
            </linearGradient>
            <linearGradient id="ribbon-op" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#d97757" />
                <stop offset="55%"  stop-color="#8e62d3" />
                <stop offset="100%" stop-color="#4b2fbf" />
            </linearGradient>
            <linearGradient id="ribbon-warm" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#eea776" />
                <stop offset="100%" stop-color="#684ed3" />
            </linearGradient>
            <filter id="ribbon-blur" x="-10%" y="-10%" width="120%" height="120%">
                <feGaussianBlur stdDeviation="6" />
            </filter>
        </defs>

        {{-- Top sweeping ribbon: purple → orange, gentle dip across the upper third --}}
        <path d="M -120 180 C 360 60, 980 360, 1560 200"
              stroke="url(#ribbon-po)"
              stroke-width="110"
              stroke-linecap="round"
              fill="none"
              opacity="0.14"
              filter="url(#ribbon-blur)" />

        {{-- Mid ribbon: orange → purple, lazy S-curve crossing the middle --}}
        <path d="M -160 520 C 320 380, 720 720, 1100 540 S 1500 420, 1620 480"
              stroke="url(#ribbon-op)"
              stroke-width="90"
              stroke-linecap="round"
              fill="none"
              opacity="0.12"
              filter="url(#ribbon-blur)" />

        {{-- Lower accent ribbon: warm peach → purple, shorter and lower --}}
        <path d="M -80 820 C 380 700, 880 900, 1520 760"
              stroke="url(#ribbon-warm)"
              stroke-width="70"
              stroke-linecap="round"
              fill="none"
              opacity="0.10"
              filter="url(#ribbon-blur)" />

        {{-- Crisp accent line for a touch of definition --}}
        <path d="M -120 180 C 360 60, 980 360, 1560 200"
              stroke="url(#ribbon-po)"
              stroke-width="2"
              fill="none"
              opacity="0.18" />
    </svg>
</div>
