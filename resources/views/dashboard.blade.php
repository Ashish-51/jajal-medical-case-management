@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Case Intelligence Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header & Quick Action Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white shadow-lg border border-slate-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-semibold mb-2 border border-teal-500/30">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Role: {{ strtoupper(auth()->user()->role) }}</span>
            </div>
            <h2 class="text-xl font-bold tracking-tight">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-300 mt-1 max-w-xl">
                Real-time tracking of surgical implant cases, procedural statuses, and scheduling pipelines.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cases.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-medium text-sm border border-slate-700 transition-colors flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4"></i>
                Browse All Cases
            </a>
            @can('create', App\Models\CaseRecord::class)
            <a href="{{ route('cases.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold text-sm shadow-md shadow-teal-500/20 transition-colors flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                New Case
            </a>
            @endcan
        </div>
    </div>

    <!-- 5 Mandatory Statistic Cards (Live Database Queries) -->
    <div>
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Pipeline Key Performance Metrics</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Total Cases -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Cases</span>
                    <span class="p-2 rounded-xl bg-slate-100 text-slate-700">
                        <i data-lucide="folder" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight" id="stat-total-cases">{{ $totalCases }}</div>
                <div class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                    <span>Draft: {{ $draftCount }}</span>
                    <span>•</span>
                    <span>Cancelled: {{ $cancelledCount }}</span>
                </div>
            </div>

            <!-- 2. Planning -->
            <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-xs hover:border-blue-200 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Planning</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-blue-900 tracking-tight" id="stat-planning-cases">{{ $planningCount }}</div>
                <div class="mt-2 text-xs text-slate-500">Implant sizing & templating</div>
            </div>

            <!-- 3. In Review -->
            <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-xs hover:border-amber-200 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">In Review</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-amber-900 tracking-tight" id="stat-review-cases">{{ $inReviewCount }}</div>
                <div class="mt-2 text-xs text-slate-500">Clinical & design validation</div>
            </div>

            <!-- 4. Approved -->
            <div class="bg-white p-5 rounded-2xl border border-teal-100 shadow-xs hover:border-teal-200 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Approved</span>
                    <span class="p-2 rounded-xl bg-teal-50 text-teal-600">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-teal-900 tracking-tight" id="stat-approved-cases">{{ $approvedCount }}</div>
                <div class="mt-2 text-xs text-slate-500">Ready for sterile manufacture</div>
            </div>

            <!-- 5. Completed -->
            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs hover:border-emerald-200 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Completed</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="text-3xl font-extrabold text-emerald-900 tracking-tight" id="stat-completed-cases">{{ $completedCount }}</div>
                <div class="mt-2 text-xs text-slate-500">Implantation complete</div>
            </div>
        </div>
    </div>

    <!-- Recent Cases Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Cases</h3>
                <p class="text-xs text-slate-500 mt-0.5">Most recently created or updated implant procedures</p>
            </div>
            <a href="{{ route('cases.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                View all cases <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($recentCases->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                    <i data-lucide="folder-x" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No cases recorded</h4>
                <p class="text-xs text-slate-500 mt-1">Get started by creating your first implant tracking case.</p>
                @can('create', App\Models\CaseRecord::class)
                <div class="mt-4">
                    <a href="{{ route('cases.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold">
                        <i data-lucide="plus" class="w-4 h-4"></i> Create Case
                    </a>
                </div>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Case Number</th>
                            <th class="py-3.5 px-6">Patient Ref</th>
                            <th class="py-3.5 px-6">Surgeon</th>
                            <th class="py-3.5 px-6">Implant Type</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Priority</th>
                            <th class="py-3.5 px-6">Surgery Date</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($recentCases as $case)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    <a href="{{ route('cases.show', $case) }}" class="text-teal-700 hover:text-teal-900 hover:underline">
                                        {{ $case->case_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-mono text-xs">
                                    {{ $case->patient_reference }}
                                </td>
                                <td class="py-4 px-6 text-slate-800">
                                    {{ $case->surgeon_name }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 max-w-xs truncate" title="{{ $case->implant_type }}">
                                    {{ $case->implant_type }}
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $statusStyles = [
                                            'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'Planning' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'In Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Approved' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusStyles[$case->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $case->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $priorityStyles = [
                                            'High' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Low' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $priorityStyles[$case->priority] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $case->priority }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs">
                                    {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('cases.show', $case) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-slate-100 transition-colors"
                                           title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 transition-colors"
                                           title="Edit Case">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
