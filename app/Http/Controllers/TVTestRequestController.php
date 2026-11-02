<?php

namespace App\Http\Controllers;

use App\Models\TVTestRequest;
use App\Models\TVTestRequestWell;
use App\Models\TestVessel;
use App\Models\TestVesselTest;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Http\Requests\StoreTVTestRequestRequest;
use App\Services\DataTable\TVTestRequestDataTableService;
use Illuminate\Http\Request;

class TVTestRequestController extends Controller
{
    protected $dataTableService;

    public function __construct(TVTestRequestDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(TVTestRequest::class, 'tv_test_request');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request);
        }

        $oilFields = OilField::active()->get();
        $requests = TVTestRequest::with(['testVessel', 'tower', 'oilField', 'requestedBy', 'wells'])
            ->latest('test_date')->latest('id')->get();

        return view('tv_test_requests.index', compact('oilFields', 'requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $testVessels = TestVessel::active()->get();
        $oilFields = OilField::active()->get();
        return view('tv_test_requests.create', compact('testVessels', 'oilFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTVTestRequestRequest $request)
    {
        $data = $request->validated();
        $data['requested_by_user_id'] = auth()->id();

        // Create the request
        $tvTestRequest = TVTestRequest::create($data);

        // Create wells for each selected oil string
        $oilStringIds = $request->get('oil_string_ids', []);
        foreach ($oilStringIds as $oilStringId) {
            TVTestRequestWell::create([
                'tv_test_request_id' => $tvTestRequest->id,
                'oil_string_id' => $oilStringId,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('tv_test_requests.show', $tvTestRequest->id)
            ->with('success', 'Test request created successfully. Operators can now start tests.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TVTestRequest $tvTestRequest)
    {
        $tvTestRequest->load([
            'wells.oilString',
            'wells.testVesselTest.reviewedBy',
            'testVessel',
            'tower',
            'oilField',
            'requestedBy'
        ]);

        return view('tv_test_requests.show', compact('tvTestRequest'));
    }

    /**
     * Start a test for a specific well in the request
     */
    public function startTest(TVTestRequest $tvTestRequest, TVTestRequestWell $well)
    {
        // Verify the well belongs to this request
        if ($well->tv_test_request_id !== $tvTestRequest->id) {
            abort(404);
        }

        // Authorize: mpfm_operator or admin
        $this->authorize('startTest', $tvTestRequest);

        // Update well status
        $well->update([
            'status' => 'in_progress',
            'started_by_user_id' => auth()->id(),
            'started_at' => now(),
        ]);

        // Redirect to create test form with pre-fill parameters
        return redirect()->route('tv_tests.create', [
            'test_vessel_id' => $tvTestRequest->test_vessel_id,
            'tower_id' => $tvTestRequest->tower_id,
            'oil_field_id' => $tvTestRequest->oil_field_id,
            'oil_string_id' => $well->oil_string_id,
            'tv_test_request_well_id' => $well->id,
        ]);
    }

    /**
     * Approve a test vessel test
     */
    public function approveTest(TestVesselTest $tvTest)
    {
        // Authorize: job_officer, surveillance_supervisor, admin
        if (!auth()->user()->hasRole(['admin', 'job_officer', 'surveillance_supervisor'])) {
            abort(403, 'Unauthorized');
        }

        $tvTest->update([
            'status' => 'approved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Update the request well status if linked
        if ($tvTest->requestWell) {
            $tvTest->requestWell->update(['status' => 'approved']);

            // Check if all wells in the request are done
            $request = $tvTest->requestWell->request;
            $allDone = $request->wells()
                ->whereNotIn('status', ['pending', 'in_progress'])
                ->count() === $request->wells()->count();

            if ($allDone) {
                $request->update(['status' => 'completed']);
            }
        }

        return redirect()->back()->with('success', 'Test approved successfully.');
    }

    /**
     * Reject a test vessel test
     */
    public function rejectTest(TestVesselTest $tvTest, Request $request)
    {
        // Authorize: job_officer, surveillance_supervisor, admin
        if (!auth()->user()->hasRole(['admin', 'job_officer', 'surveillance_supervisor'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $tvTest->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        // Update the request well status if linked
        if ($tvTest->requestWell) {
            $tvTest->requestWell->update(['status' => 'rejected']);
        }

        return redirect()->back()->with('success', 'Test rejected successfully.');
    }
}
