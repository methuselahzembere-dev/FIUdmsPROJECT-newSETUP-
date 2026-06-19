<x-app-layout>
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-wide">Multi-Tenant Framework Allocation Matrix</h1>
        <p class="text-sm font-medium text-slate-500">Map standard Effectiveness Immediate Outcomes (IOs) to authorized institutions across the multi-institution document network.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold shadow-xs">
            <p class="font-bold mb-1">Please correct the following errors:</p>
            <ul class="list-disc pl-5 font-medium text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fiu.outcomes.assignments.store') }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @csrf

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-4 px-6 min-w-[320px]">Immediate Outcome Definition</th>
                        <th class="py-4 px-6">Target Institutional Allocations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($outcomes as $outcome)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-5 px-6 align-top">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center bg-blue-600 text-white font-black text-xs px-2.5 py-1 rounded-lg shrink-0">
                                        {{ $outcome->code }}
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-800">
                                            IO {{ $outcome->number }}: {{ $outcome->title }}
                                        </h3>
                                        @if($outcome->description)
                                            <p class="text-xs font-medium text-slate-400 mt-1 max-w-xl leading-relaxed">
                                                {{ Str::limit($outcome->description, 140) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-5 px-6 align-top">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($institutions as $institution)
                                        @php
                                            // Determine if this specific institution is already assigned to this outcome
                                            // Works seamlessly if you pre-loaded the relationship via with('institutions')
                                            $isAssigned = isset($outcome->institutions) && $outcome->institutions->contains($institution->id);
                                        @endphp
                                        <label class="relative flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 hover:border-blue-500 bg-white hover:bg-blue-50/10 cursor-pointer select-none transition group">
                                            <input type="checkbox" 
                                                   name="assignments[{{ $outcome->id }}][]" 
                                                   value="{{ $institution->id }}"
                                                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer"
                                                   {{ $isAssigned ? 'checked' : '' }}>
                                            
                                            <div class="text-xs">
                                                <span class="block font-bold text-slate-700 group-hover:text-blue-900 transition-colors">
                                                    {{ $institution->name }}
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-slate-50/80 border-t border-slate-200 px-6 py-4 flex items-center justify-end backdrop-blur-md">
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-700 px-6 text-xs font-black text-white transition hover:bg-blue-800 shadow-sm cursor-pointer whitespace-nowrap uppercase tracking-wider">
                Synchronize Allocation Grid Matrix
            </button>
        </div>
    </form>
</div>

</x-app-layout>