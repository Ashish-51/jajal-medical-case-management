@extends('layouts.app')

@section('title', 'All Implant Cases')
@section('page_title', 'All Implant Cases')

@section('content')
<div class="space-y-6">
    <!-- Header with Search, Filter & Actions -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Input (Case Number or Surgeon Name) -->
            <div class="lg:col-span-5">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Search Cases
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ $search }}" 
                           placeholder="Search by Case Number or Surgeon..." 
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Status Filter
                </label>
                <select name="status" id="status" class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white">
                    <option value="">All Statuses</option>
                    @foreach(App\Models\CaseRecord::STATUSES as $item)
                        <option value="{{ $item }}" {{ $status === $item ? 'selected' : '' }}>
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter (Bonus B) -->
            <div class="lg:col-span-2">
                <label for="priority" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Priority
                </label>
                <select name="priority" id="priority" class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white">
                    <option value="">All Priorities</option>
                    @foreach(App\Models\CaseRecord::PRIORITIES as $item)
                        <option value="{{ $item }}" {{ $priority === $item ? 'selected' : '' }}>
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hidden Sorting Parameters preservation -->
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">

            <!-- Filter Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        id="filter-submit-btn"
                        class="flex-1 py-2 px-4 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-semibold text-sm shadow-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Apply
                </button>
                @if($search || $status || $priority)
                    <a href="{{ route('cases.index') }}" 
                       class="py-2 px-3 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-600 text-sm transition-colors"
                       title="Reset Filters">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Case Records Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if($cases->isEmpty())
            <div class="p-16 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 text-slate-400 mb-3">
                    <i data-lucide="folder-x" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-800">No matching cases found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Try adjusting your search criteria or filter options to view existing implant cases.
                </p>
                @if($search || $status || $priority)
                    <div class="mt-4">
                        <a href="{{ route('cases.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Clear all filters
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="cases-table">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <!-- Sortable columns helper -->
                            @php
                                $getSortUrl = function($column) use ($sort, $direction, $search, $status, $priority) {
                                    $newDir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                                    return route('cases.index', [
                                        'sort' => $column,
                                        'direction' => $newDir,
                                        'search' => $search,
                                        'status' => $status,
                                        'priority' => $priority
                                    ]);
                                };
                            @endphp

                            <th class="py-3.5 px-6">
                                <a href="{{ $getSortUrl('case_number') }}" class="inline-flex items-center gap-1 hover:text-teal-700">
                                    Case Number
                                    @if($sort === 'case_number')
                                        <i data-lucide="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-teal-600"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-6">Patient Ref</th>
                            <th class="py-3.5 px-6">
                                <a href="{{ $getSortUrl('surgeon_name') }}" class="inline-flex items-center gap-1 hover:text-teal-700">
                                    Surgeon
                                    @if($sort === 'surgeon_name')
                                        <i data-lucide="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-teal-600"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-6">Implant Type</th>
                            <th class="py-3.5 px-6">
                                <a href="{{ $getSortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-teal-700">
                                    Status
                                    @if($sort === 'status')
                                        <i data-lucide="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-teal-600"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-6">
                                <a href="{{ $getSortUrl('priority') }}" class="inline-flex items-center gap-1 hover:text-teal-700">
                                    Priority
                                    @if($sort === 'priority')
                                        <i data-lucide="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-teal-600"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-6">
                                <a href="{{ $getSortUrl('surgery_date') }}" class="inline-flex items-center gap-1 hover:text-teal-700">
                                    Surgery Date
                                    @if($sort === 'surgery_date')
                                        <i data-lucide="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-teal-600"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-6">Created By</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($cases as $case)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    <a href="{{ route('cases.show', $case) }}" class="text-teal-700 hover:text-teal-900 hover:underline">
                                        {{ $case->case_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-slate-600">
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
                                        $statusColors = [
                                            'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'Planning' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'In Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Approved' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$case->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $case->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $priorityColors = [
                                            'High' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Low' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $priorityColors[$case->priority] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $case->priority }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs">
                                    {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs">
                                    {{ $case->creator->name ?? 'System' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View: Everyone (Admin, Viewer, Engineer) -->
                                        <a href="{{ route('cases.show', $case) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-teal-700 hover:bg-slate-100 transition-colors"
                                           title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <!-- Edit: Admin, Engineer -->
                                        @can('update', $case)
                                        <a href="{{ route('cases.edit', $case) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-blue-700 hover:bg-slate-100 transition-colors"
                                           title="Edit Case">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        @endcan

                                        <!-- Delete: Admin ONLY -->
                                        @can('delete', $case)
                                        <form action="{{ route('cases.destroy', $case) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to permanently delete case {{ $case->case_number }}? This operation cannot be undone.');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                    title="Delete Case">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mandatory Pagination: 10 per page, preserving query strings -->
            <div class="p-4 px-6 border-t border-slate-200/80 bg-slate-50/50">
                {{ $cases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
