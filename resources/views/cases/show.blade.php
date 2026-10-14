@extends('layouts.app')

@section('title', 'Case Details - ' . $case->case_number)
@section('page_title', 'Case File: ' . $case->case_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('cases.index') }}" 
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-teal-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Cases List
        </a>

        <div class="flex items-center gap-3">
            @can('update', $case)
            <a href="{{ route('cases.edit', $case) }}" 
               class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold shadow-xs transition-colors flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-4 h-4 text-blue-600"></i> Edit Case
            </a>
            @endcan

            @can('delete', $case)
            <form action="{{ route('cases.destroy', $case) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you sure you want to permanently delete case {{ $case->case_number }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 rounded-lg bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 text-sm font-semibold shadow-xs transition-colors flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-4 h-4 text-rose-600"></i> Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Case Status Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $case->case_number }}</h2>
                <span class="font-mono text-xs px-2.5 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">
                    ID #{{ $case->id }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Implant Tracking Record • Jajal Medical Internal Operations</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Status Badge -->
            @php
                $statusBadges = [
                    'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                    'Planning' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'In Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Approved' => 'bg-teal-50 text-teal-700 border-teal-200',
                    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                ];
            @endphp
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadges[$case->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $case->status }}
                </span>
            </div>

            <!-- Priority Badge -->
            @php
                $priorityBadges = [
                    'High' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Low' => 'bg-slate-100 text-slate-600 border-slate-200',
                ];
            @endphp
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Priority</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $priorityBadges[$case->priority] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $case->priority }}
                </span>
            </div>
        </div>
    </div>

    <!-- Optional Bonus C: Public Holiday Banner -->
    @if(isset($holidayInfo) && $holidayInfo)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
            <div>
                <span class="font-bold">Public Holiday Advisory:</span>
                Scheduled surgery date coincides with <strong>{{ $holidayInfo['name'] }}</strong> ({{ $holidayInfo['date'] }}). 
                Please verify surgical team, OR access, and delivery courier schedules.
            </div>
        </div>
    @endif

    <!-- Two-Column Case Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Column 1: Clinical & Procedural Specifications -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                <i data-lucide="stethoscope" class="w-4 h-4 text-teal-600"></i>
                Clinical & Implant Specs
            </h3>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Patient Reference</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-slate-900 bg-slate-50 p-2.5 rounded-lg border border-slate-200/60 inline-block">
                        {{ $case->patient_reference }}
                    </dd>
                    <p class="text-[11px] text-slate-400 mt-1">Anonymized administrative reference identifier</p>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lead Surgeon</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900">{{ $case->surgeon_name }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Implant Specification</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800 bg-teal-50/40 p-3 rounded-lg border border-teal-100">
                        {{ $case->implant_type }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Surgery Date</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        {{ $case->surgery_date ? $case->surgery_date->format('l, F j, Y') : 'Not Scheduled (Pending)' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Column 2: Audit Trail & Administrative Creator Information -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-teal-600"></i>
                Administrative Audit Log
            </h3>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Created By User</dt>
                    <dd class="mt-1.5 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-teal-600/10 text-teal-700 font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($case->creator->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-900">{{ $case->creator->name ?? 'Unknown User' }}</span>
                            <span class="block text-xs text-slate-500">{{ $case->creator->email ?? 'N/A' }}</span>
                        </div>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Authorizing Role</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ strtoupper($case->creator->role ?? 'USER') }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Record Initialized</dt>
                    <dd class="mt-1 text-xs text-slate-700 font-mono">
                        {{ $case->created_at ? $case->created_at->format('Y-m-d H:i:s T') : '—' }}
                        <span class="text-slate-400 block text-[11px] font-sans mt-0.5">({{ $case->created_at ? $case->created_at->diffForHumans() : '' }})</span>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Last Modified</dt>
                    <dd class="mt-1 text-xs text-slate-700 font-mono">
                        {{ $case->updated_at ? $case->updated_at->format('Y-m-d H:i:s T') : '—' }}
                        <span class="text-slate-400 block text-[11px] font-sans mt-0.5">({{ $case->updated_at ? $case->updated_at->diffForHumans() : '' }})</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
