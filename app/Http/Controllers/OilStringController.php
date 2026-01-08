<?php

namespace App\Http\Controllers;

use App\Models\OilString;
use App\Models\Tower;
use App\Models\OilField;
use App\Http\Requests\StoreOilStringRequest;
use App\Http\Requests\UpdateOilStringRequest;
use App\Services\DataTable\OilStringDataTableService;
use Illuminate\Http\Request;

class OilStringController extends Controller
{
    protected $dataTableService;

    public function __construct(OilStringDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(OilString::class, 'oil_string');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request);
        }

        $towers = Tower::with('oilField')->active()->get();
        $oilFields = OilField::active()->get();
        return view('oil_strings.index', compact('towers', 'oilFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $towers = Tower::with('oilField')->active()->get();
        return view('oil_strings.create', compact('towers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOilStringRequest $request)
    {
        OilString::create($request->validated());

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OilString $oilString)
    {
        $oilString->load(['tower.oilField', 'mpfmTests']);
        return view('oil_strings.show', compact('oilString'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OilString $oilString)
    {
        $towers = Tower::with('oilField')->active()->get();
        return view('oil_strings.edit', compact('oilString', 'towers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOilStringRequest $request, OilString $oilString)
    {
        $oilString->update($request->validated());

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OilString $oilString)
    {
        $oilString->delete();

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String deleted successfully.');
    }
}

