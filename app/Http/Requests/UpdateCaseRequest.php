<?php

namespace App\Http\Requests;

use App\Models\CaseRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $case = $this->route('case') ?? $this->route('id');
        if (is_numeric($case)) {
            $case = CaseRecord::find($case);
        }

        return $this->user() && $case && $this->user()->can('update', $case);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $case = $this->route('case') ?? $this->route('id');
        $caseId = is_object($case) ? $case->id : $case;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:150'],
            'implant_type' => ['required', 'string', 'max:200'],
            'status' => ['required', 'string', Rule::in(CaseRecord::STATUSES)],
            'priority' => ['required', 'string', Rule::in(CaseRecord::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Custom messages for validation.
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'The case number is required.',
            'case_number.unique' => 'The case number has already been taken.',
            'patient_reference.required' => 'The patient reference is required.',
            'surgeon_name.required' => 'The surgeon name is required.',
            'implant_type.required' => 'The implant type is required.',
            'status.required' => 'Please select a valid case status.',
            'status.in' => 'The selected status is invalid.',
            'priority.required' => 'Please select a valid priority level.',
            'priority.in' => 'The selected priority is invalid.',
            'surgery_date.date' => 'The surgery date must be a valid date.',
        ];
    }
}
