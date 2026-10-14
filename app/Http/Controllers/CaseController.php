<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseRecord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of cases.
     * Supports search by case_number/surgeon_name, status filter, priority filter, and column sorting.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CaseRecord::class);

        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $sort = $request->query('sort', 'created_at');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Safe column allowlisting for sorting (Bonus B)
        $allowedSorts = [
            'case_number',
            'patient_reference',
            'surgeon_name',
            'implant_type',
            'status',
            'priority',
            'surgery_date',
            'created_at'
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $query = CaseRecord::with('creator');

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($status)) {
            $query->withStatus($status);
        }

        if (!empty($priority)) {
            $query->withPriority($priority);
        }

        // Mandatory exactly 10 records per page, preserving query strings
        $cases = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('cases.index', compact('cases', 'search', 'status', 'priority', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new case.
     */
    public function create(): View
    {
        $this->authorize('create', CaseRecord::class);

        $case = new CaseRecord();
        $isEdit = false;

        return view('cases.form', compact('case', 'isEdit'));
    }

    /**
     * Store a newly created case in storage.
     */
    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        // Server-side assignment of created_by to prevent spoofing
        $validated['created_by'] = $request->user()->id;

        $case = CaseRecord::create($validated);

        return redirect()
            ->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} created successfully.");
    }

    /**
     * Display the specified case.
     */
    public function show(int $id): View
    {
        $case = CaseRecord::with('creator')->findOrFail($id);
        $this->authorize('view', $case);

        // Optional Bonus C: Public holiday integration for surgery date
        $holidayInfo = null;
        if ($case->surgery_date) {
            $holidayInfo = $this->fetchHolidayForDate($case->surgery_date);
        }

        return view('cases.show', compact('case', 'holidayInfo'));
    }

    /**
     * Show the form for editing the specified case.
     */
    public function edit(int $id): View
    {
        $case = CaseRecord::findOrFail($id);
        $this->authorize('update', $case);

        $isEdit = true;

        return view('cases.form', compact('case', 'isEdit'));
    }

    /**
     * Update the specified case in storage.
     */
    public function update(UpdateCaseRequest $request, int $id): RedirectResponse
    {
        $case = CaseRecord::findOrFail($id);
        $this->authorize('update', $case);

        $validated = $request->validated();
        $case->update($validated);

        return redirect()
            ->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} updated successfully.");
    }

    /**
     * Remove the specified case from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $case = CaseRecord::findOrFail($id);
        $this->authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()
            ->route('cases.index')
            ->with('success', "Case {$caseNumber} deleted successfully.");
    }

    /**
     * Optional Bonus C: Graceful fetch of public holiday for surgery date.
     */
    private function fetchHolidayForDate($date): ?array
    {
        try {
            $baseUrl = config('services.holidays.base_url', env('HOLIDAY_API_BASE_URL', 'https://date.nager.at/api/v3'));
            $country = config('services.holidays.country', env('HOLIDAY_DEFAULT_COUNTRY', 'US'));
            $year = date('Y', strtotime($date));
            $formattedDate = date('Y-m-d', strtotime($date));

            // 2 second reasonable timeout, handle errors gracefully without blocking UI
            $response = Http::timeout(2)->get("{$baseUrl}/PublicHolidays/{$year}/{$country}");

            if ($response->successful()) {
                $holidays = $response->json();
                if (is_array($holidays)) {
                    foreach ($holidays as $h) {
                        if (isset($h['date']) && $h['date'] === $formattedDate) {
                            return [
                                'is_holiday' => true,
                                'name' => $h['name'] ?? $h['localName'] ?? 'Public Holiday',
                                'date' => $formattedDate,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Holiday API query bypassed: ' . $e->getMessage());
        }

        return null;
    }
}
