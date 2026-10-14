@extends('layouts.app')

@section('title', $isEdit ? 'Edit Case ' . $case->case_number : 'Create New Implant Case')
@section('page_title', $isEdit ? 'Edit Implant Case: ' . $case->case_number : 'Create New Implant Case')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Navigation -->
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('cases.index') }}" class="hover:text-teal-700 flex items-center gap-1 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Cases
        </a>
        @if($isEdit)
            <span>/</span>
            <a href="{{ route('cases.show', $case) }}" class="hover:text-teal-700">{{ $case->case_number }}</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Edit</span>
        @else
            <span>/</span>
            <span class="text-slate-800 font-semibold">New Case</span>
        @endif
    </div>

    <!-- Reusable Case Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 sm:p-10">
        <div class="mb-8 pb-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    {{ $isEdit ? 'Update Surgical Case Information' : 'Implant Case Registration' }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $isEdit ? 'Modify clinical details, priority, or progression status' : 'Enter procedural details, reference identifiers, and implant specifications' }}
                </p>
            </div>
            @if($isEdit)
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">
                    ID: #{{ $case->id }}
                </span>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="flex items-center gap-2 font-semibold mb-1">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    <span>Please correct the errors indicated below.</span>
                </div>
            </div>
        @endif

        <form action="{{ $isEdit ? route('cases.update', $case) : route('cases.store') }}" 
              method="POST" 
              id="case-form" 
              class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Case Number -->
                <div>
                    <label for="case_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Case Number <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="case_number" 
                           id="case_number" 
                           value="{{ old('case_number', $case->case_number) }}" 
                           required 
                           placeholder="e.g. CASE-0001" 
                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('case_number') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent font-medium">
                    @error('case_number')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-[11px] text-slate-400">Unique corporate case tracking identifier</p>
                    @enderror
                </div>

                <!-- Patient Reference (DUMMY CODE ONLY) -->
                <div>
                    <label for="patient_reference" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Patient Reference Code <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="patient_reference" 
                           id="patient_reference" 
                           value="{{ old('patient_reference', $case->patient_reference) }}" 
                           required 
                           placeholder="e.g. PAT-9482-X" 
                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('patient_reference') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent font-mono text-xs">
                    @error('patient_reference')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-[11px] text-amber-600 font-medium">Notice: Never enter real names or HIPAA-identifiable data</p>
                    @enderror
                </div>

                <!-- Surgeon Name -->
                <div>
                    <label for="surgeon_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Surgeon Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="surgeon_name" 
                           id="surgeon_name" 
                           value="{{ old('surgeon_name', $case->surgeon_name) }}" 
                           required 
                           placeholder="e.g. Dr. Marcus Vance" 
                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('surgeon_name') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent font-medium">
                    @error('surgeon_name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Implant Type -->
                <div>
                    <label for="implant_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Implant Type / Model <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="implant_type" 
                           id="implant_type" 
                           value="{{ old('implant_type', $case->implant_type) }}" 
                           required 
                           placeholder="e.g. Ceramic Hip Prosthesis - 36mm" 
                           class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('implant_type') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent font-medium">
                    @error('implant_type')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Select (Allowed 6 values) -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Case Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required 
                            class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('status') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white font-medium">
                        <option value="">Select Status</option>
                        @foreach(App\Models\CaseRecord::STATUSES as $statusOption)
                            <option value="{{ $statusOption }}" {{ old('status', $case->status) === $statusOption ? 'selected' : '' }}>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority Select (Low, Medium, High) -->
                <div>
                    <label for="priority" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Priority Level <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority" 
                            required 
                            class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('priority') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white font-medium">
                        @foreach(App\Models\CaseRecord::PRIORITIES as $priorityOption)
                            <option value="{{ $priorityOption }}" {{ old('priority', $case->priority ?? 'Medium') === $priorityOption ? 'selected' : '' }}>
                                {{ $priorityOption }} Priority
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Surgery Date (Nullable Date) -->
                <div class="md:col-span-2">
                    <label for="surgery_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Scheduled Surgery Date <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input type="date" 
                           name="surgery_date" 
                           id="surgery_date" 
                           value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}" 
                           class="w-full md:w-1/2 px-4 py-2.5 rounded-lg border {{ $errors->has('surgery_date') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    @error('surgery_date')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-[11px] text-slate-400">Leave blank if procedure is in early draft or awaiting hospital scheduling</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ $isEdit ? route('cases.show', $case) : route('cases.index') }}" 
                   class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        id="submit-case-btn"
                        class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold shadow-md shadow-teal-500/20 transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>{{ $isEdit ? 'Save Changes' : 'Create Case' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
