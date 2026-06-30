<x-app-layout>
         <div 
        x-data="{ 
            search: '', 
            selectedIds: {{ json_encode(array_map('strval', $assignedIds)) }},
            allIds: {{ json_encode($outcomes->pluck('id')->map(fn($id) => (string)$id)->toArray()) }},
            
            toggleAll() {
                if (this.selectedIds.length === this.allIds.length) {
                    this.selectedIds = []; // Deselect all
                } else {
                    this.selectedIds = [...this.allIds]; // Select all
                }
            }
        }" 
        class="space-y-6"
    >

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-600">Access Management</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Effectiveness Outcomes Matrix</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Control which Immediate Outcome workspaces an institution can access. Select an institution from the sidebar to manage their permissions.
                </p>
            </div>
        </div>

              <a 
    href="{{ route('fiu.dashboard') }}" 
    class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900"
>
    <svg class="h-4 w-4 text-slate-400 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Back to Dashboard
</a>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                {{ session('status') }}
            </div>
        @endif


        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex min-h-[700px] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm xl:flex-row">
            
            <aside class="w-full border-b border-slate-200 bg-slate-50 xl:w-80 xl:min-w-[20rem] xl:border-b-0 xl:border-r">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="font-bold text-slate-900">Institutions</h3>
                    <p class="mt-1 text-xs text-slate-500">Select an institution to edit rights.</p>
                </div>
                
                <nav class="h-[calc(100%-5rem)] space-y-1 overflow-y-auto p-3">
                    @foreach($institutions as $inst)
                        @php $isActive = $activeInstitution && $activeInstitution->id === $inst->id; @endphp
                        <a 
                            href="{{ route('fiu.access.effectiveness.index', $inst->id) }}" 
                            class="flex items-center justify-between rounded-xl px-4 py-3 text-sm transition {{ $isActive ? 'bg-white font-bold text-violet-700 shadow-sm ring-1 ring-slate-200' : 'font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <span class="truncate pr-2">{{ $inst->name }}</span>
                            <span class="inline-flex shrink-0 items-center justify-center rounded-full px-2 py-0.5 text-xs {{ $isActive ? 'bg-violet-100 text-violet-700' : 'bg-slate-200 text-slate-500' }}">
                                {{ $inst->effectivenessImmediateOutcomes()->count() }}
                            </span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            <section class="flex flex-1 flex-col bg-white">
                
                @if($activeInstitution)
                    <form action="{{ route('fiu.access.effectiveness.sync', $activeInstitution->id) }}" method="POST" class="flex h-full flex-col">
                        @csrf
                        
                        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Managing: {{ $activeInstitution->name }}</h2>
                                <p class="text-sm text-slate-500">Select the Immediate Outcomes this institution is required to report on.</p>
                            </div>
                            
                            <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                Save Access Rights
                            </button>
                        </div>

                        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-3">
                            <div class="relative max-w-sm flex-1">
                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <input 
                                    type="text" 
                                    x-model="search" 
                                    placeholder="Search outcomes..." 
                                    class="w-full rounded-lg border-slate-200 py-2 pl-9 pr-4 text-sm focus:border-violet-500 focus:ring-violet-500"
                                >
                            </div>

                            <button 
                                type="button" 
                                @click="toggleAll()" 
                                class="text-sm font-medium text-violet-600 hover:text-violet-800"
                            >
                                <span x-text="selectedIds.length === allIds.length ? 'Deselect All' : 'Select All'"></span>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30">
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                
                            @foreach($outcomes as $outcome)
                                    <label 
                                        x-data="{ code: '{{ strtolower(addslashes($outcome->code)) }}', desc: '{{ strtolower(addslashes($outcome->description ?? '')) }}' }"
                                        x-show="search === '' || code.includes(search.toLowerCase()) || desc.includes(search.toLowerCase())"
                                        class="group flex cursor-pointer items-start justify-between rounded-2xl border px-5 py-4 transition-all"
                                        :class="selectedIds.includes('{{ $outcome->id }}') ? 'border-violet-500 bg-violet-50/80 shadow-sm ring-1 ring-violet-500' : 'border-slate-200 bg-white hover:border-slate-300'"
                                    >
                                        <input 
                                            type="checkbox" 
                                            name="outcome_ids[]" 
                                            value="{{ $outcome->id }}" 
                                            x-model="selectedIds"
                                            class="hidden"
                                        >
                                        
                                        <div class="flex gap-4">
                                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl font-bold transition-colors"
                                                :class="selectedIds.includes('{{ $outcome->id }}') ? 'bg-violet-600 text-white shadow-md shadow-violet-200' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold transition-colors" :class="selectedIds.includes('{{ $outcome->id }}') ? 'text-violet-900' : 'text-slate-900'">
                                                    {{ $outcome->code }}
                                                </p>
                                                <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $outcome->description ?: 'Main Immediate Outcome Workspace' }}</p>
                                            </div>
                                        </div>

                                        <div 
                                            class="relative mt-1 inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="selectedIds.includes('{{ $outcome->id }}') ? 'bg-violet-600' : 'bg-slate-200'"
                                        >
                                            <span 
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                :class="selectedIds.includes('{{ $outcome->id }}') ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </div>
                                    </label>
                                @endforeach  

                            </div>
                        </div>
                    </form>
                @else
                    <div class="flex flex-1 items-center justify-center p-12 text-center text-slate-500">
                        <p>Select an Institution from the sidebar to manage their IO access.</p>
                    </div>
                @endif
                
            </section>
        </div>
    </div>
</x-app-layout>