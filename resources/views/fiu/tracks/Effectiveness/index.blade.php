<x-app-layout>
    <!-- 3D flip utilities -->
    <style>
        .perspective-1000 { perspective: 1000px; }
        .preserve-3d { transform-style: preserve-3d; }
        .flip-active { transform: rotateX(-90deg); opacity: 0; }
    </style>

    <!-- 🌟 AMBIENT BACKGROUND -->
    <!-- Added a slightly darker base (bg-slate-100) so the white glass pops! -->
    <div class="relative min-h-screen bg-slate-100 pb-12 overflow-hidden">
        
        <!-- Decorative blurred orbs (Explicitly pushed to the back with z-0) -->
        <div class="absolute top-0 left-1/4 h-[500px] w-[500px] rounded-full bg-violet-400/20 blur-[100px] z-0 pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 h-[500px] w-[500px] rounded-full bg-blue-400/20 blur-[100px] z-0 pointer-events-none"></div>

        <!-- MAIN CONTENT (Explicitly pulled to the front with z-10) -->
        <div class="relative z-10 space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- SLEEK HEADER -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/80 backdrop-blur-md border border-white shadow-sm text-xs font-black uppercase tracking-widest text-violet-700 mb-3">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        Effectiveness Workspace
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900">Immediate Outcomes</h1>
                </div>

                <div class="flex gap-6 text-right">
                    <div class="bg-white/80 backdrop-blur-md border border-white shadow-sm rounded-xl px-4 py-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total IOs</p>
                        <p class="text-2xl font-black text-slate-800">{{ $immediateOutcomes->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- 🌟 THE GLASSMORPHIC LIST -->
            @php
                // High-visibility themes. Using borders, glowing shadows, and icons to differentiate.
                $palettes = [
                    ['border' => 'border-l-violet-500', 'shadow' => 'hover:shadow-violet-500/20', 'icon' => 'text-violet-600', 'bg' => 'bg-violet-50'],
                    ['border' => 'border-l-blue-500', 'shadow' => 'hover:shadow-blue-500/20', 'icon' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                    ['border' => 'border-l-emerald-500', 'shadow' => 'hover:shadow-emerald-500/20', 'icon' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                    ['border' => 'border-l-rose-500', 'shadow' => 'hover:shadow-rose-500/20', 'icon' => 'text-rose-600', 'bg' => 'bg-rose-50'],
                ];
            @endphp

            <div class="mt-4">
                <ul role="list" class="space-y-4">
                    @forelse($immediateOutcomes as $immediateOutcome)
                        @php
                            $justUpdated = session('recently_updated_ios') && in_array($immediateOutcome->id, session('recently_updated_ios'));
                            $subDocumentTotal = $immediateOutcome->subOutcomes->sum(fn ($subOutcome) => $documentCounts[$subOutcome->id] ?? 0);
                            $theme = $palettes[$loop->index % count($palettes)];
                        @endphp

                        <li class="perspective-1000 relative group">
                            <!-- High Contrast Card: Solid white base with 80% opacity for clear visibility -->
                            <div 
                                onclick="executeDashboardFlip(event, this, '{{ route('fiu.effectiveness.folders.show', $immediateOutcome->code) }}')"
                                class="preserve-3d relative flex items-center justify-between px-6 py-5 cursor-pointer transition-all duration-300 ease-out rounded-xl bg-white/80 backdrop-blur-xl border border-white/50 border-l-[6px] {{ $theme['border'] }} shadow-sm hover:shadow-lg {{ $theme['shadow'] }} hover:-translate-y-1"
                            >
                                <!-- Left Side -->
                                <div class="flex items-center gap-5 min-w-0 flex-1">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $theme['bg'] }}">
                                        <svg class="h-6 w-6 {{ $theme['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <p class="text-lg font-bold text-slate-900 truncate">
                                                {{ $immediateOutcome->code }}
                                            </p>
                                            @if($justUpdated)
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider text-emerald-800 animate-pulse">
                                                    New Uploads
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500 truncate max-w-2xl">
                                            {{ $immediateOutcome->description ?: 'Manage effectiveness documents and evaluate compliance tracking.' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Side -->
                                <div class="ml-6 flex items-center gap-8 shrink-0">
                                    <div class="hidden sm:block text-right">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sub-IOs</p>
                                        <p class="text-sm font-bold text-slate-900">{{ $immediateOutcome->sub_outcomes_count }}</p>
                                    </div>

                                    <div class="text-right w-16">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Docs</p>
                                        <p class="text-sm font-bold text-slate-900">{{ number_format($subDocumentTotal) }}</p>
                                    </div>

                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-slate-200 transition-colors">
                                        <svg class="h-4 w-4 text-slate-500 group-hover:text-slate-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-16 text-center rounded-2xl border border-dashed border-slate-300 bg-white/80">
                            <span class="font-semibold text-slate-600">No Immediate Outcomes Initialized.</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT FOR 3D FLIP -->
    <script>
        function executeDashboardFlip(event, element, targetUrl) {
            event.preventDefault();
            element.classList.add('flip-active');
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 300);
        }
    </script>
</x-app-layout>