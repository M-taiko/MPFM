<?php

namespace App\Http\Controllers;

use App\Models\Tower;
use App\Models\OilField;
use App\Http\Requests\StoreTowerRequest;
use App\Http\Requests\UpdateTowerRequest;
use App\Services\DataTable\TowerDataTableService;
use Illuminate\Http\Request;

class TowerController extends Controller
{
    protected $dataTableService;

    public function __construct(TowerDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(Tower::class, 'tower');
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
        return view('towers.index', compact('oilFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $oilFields = OilField::active()->get();
        return view('towers.create', compact('oilFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTowerRequest $request)
    {
        Tower::create($request->validated());

        return redirect()->route('towers.index')
            ->with('success', 'Tower created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tower $tower)
    {
        $tower->load(['oilField', 'oilStrings', 'mpfmOperationalPlans', 'mpfmTests']);
        return view('towers.show', compact('tower'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tower $tower)
    {
        $oilFields = OilField::active()->get();
        return view('towers.edit', compact('tower', 'oilFields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTowerRequest $request, Tower $tower)
    {
        $tower->update($request->validated());

        return redirect()->route('towers.index')
            ->with('success', 'Tower updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tower $tower)
    {
        $tower->delete();

        return redirect()->route('towers.index')
            ->with('success', 'Tower deleted successfully.');
    }
}

