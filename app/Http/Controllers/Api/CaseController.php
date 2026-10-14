<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Http\Resources\CaseRecordResource;
use App\Models\CaseRecord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a paginated listing of cases.
     * Permission: Admin, Viewer, Engineer.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CaseRecord::class);

        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');

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

        $perPage = 10;
        $paginated = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CaseRecordResource::collection($paginated->items()),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
            'message' => 'Cases retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created case.
     * Permission: Admin (and Engineer). Viewer forbidden (403).
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = CaseRecord::create($validated);
        $case->load('creator');

        return response()->json([
            'success' => true,
            'data' => new CaseRecordResource($case),
            'message' => 'Case created successfully.',
        ], 201);
    }

    /**
     * Display the specified case.
     * Permission: Admin, Viewer, Engineer.
     */
    public function show(int $id): JsonResponse
    {
        $case = CaseRecord::with('creator')->find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        $this->authorize('view', $case);

        return response()->json([
            'success' => true,
            'data' => new CaseRecordResource($case),
            'message' => 'Case retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified case.
     * Permission: Admin (and Engineer). Viewer forbidden (403).
     */
    public function update(UpdateCaseRequest $request, int $id): JsonResponse
    {
        $case = CaseRecord::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        $this->authorize('update', $case);

        $validated = $request->validated();
        $case->update($validated);
        $case->load('creator');

        return response()->json([
            'success' => true,
            'data' => new CaseRecordResource($case),
            'message' => 'Case updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified case.
     * Permission: Admin ONLY. Viewer/Engineer forbidden (403).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $case = CaseRecord::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Case not found.',
            ], 404);
        }

        $this->authorize('delete', $case);

        $case->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Case deleted successfully.',
        ], 200);
    }
}
