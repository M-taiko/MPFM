<?php

namespace App\Http\Controllers;

use App\Models\OilField;
use App\Http\Requests\StoreOilFieldRequest;
use App\Http\Requests\UpdateOilFieldRequest;
use App\Services\DataTable\OilFieldDataTableService;
use Illuminate\Http\Request;

class OilFieldController extends Controller
{
    protected $dataTableService;

    public function __construct(OilFieldDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(OilField::class, 'oil_field');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request);
        }

        return view('oil_fields.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('oil_fields.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOilFieldRequest $request)
    {
        OilField::create($request->validated());

        return redirect()->route('oil_fields.index')
            ->with('success', 'Oil Field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OilField $oilField)
    {
        $oilField->load(['towers', 'mpfmOperationalPlans', 'mpfmTests']);
        return view('oil_fields.show', compact('oilField'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OilField $oilField)
    {
        return view('oil_fields.edit', compact('oilField'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOilFieldRequest $request, OilField $oilField)
    {
        $oilField->update($request->validated());

        return redirect()->route('oil_fields.index')
            ->with('success', 'Oil Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OilField $oilField)
    {
        $oilField->delete();

        return redirect()->route('oil_fields.index')
            ->with('success', 'Oil Field deleted successfully.');
    }
}

