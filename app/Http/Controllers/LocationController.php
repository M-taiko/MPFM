<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\OilField;
use App\Models\OilString;
use App\Models\TestVessel;
use App\Models\Tower;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Location > Oil field > Tower > Well string. This controller owns the first level.
 */
class LocationController extends Controller
{
    private function canManage(): bool
    {
        return auth()->user()?->canManageLocations() ?? false;
    }

    private function requireManage(): void
    {
        abort_unless($this->canManage(), 403, 'You are not allowed to manage locations.');
    }

    // ── List ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $q = Location::orderBy('environment')->orderBy('name');

        if ($request->filled('environment')) { $q->where('environment', $request->input('environment')); }
        if ($request->filled('status'))      { $q->where('status', $request->input('status')); }
        if ($request->filled('q')) {
            $term = '%' . $request->input('q') . '%';
            $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('code', 'like', $term));
        }
        $page = $q->paginate(12)->withQueryString();

        $byType = Location::selectRaw('environment, count(*) c')->groupBy('environment')->pluck('c', 'environment');
        $all = Location::stats();

        return view('locations.index', [
            'locations' => $page,
            'stats'     => Location::stats($page->pluck('id')->all()),
            'byType'    => $byType,
            'totals'    => ['locations' => $byType->sum(), 'fields' => array_sum(array_column($all, 'fields')), 'towers' => array_sum(array_column($all, 'towers')), 'wells' => array_sum(array_column($all, 'wells'))],
            'canManage' => $this->canManage(),
        ]);
    }

    // ── Create / store ───────────────────────────────────────────
    public function create(Request $request)
    {
        $this->requireManage();

        return view('locations.form', ['location' => null, 'preset' => ['environment' => $request->input('environment')]]);
    }

    public function store(Request $request)
    {
        $this->requireManage();
        $location = Location::create($this->validated($request));

        return redirect()->route('locations.show', $location)->with('success', 'Location created. Now add the oil fields that belong to it.');
    }

    // ── Show: the fields inside this location ────────────────────
    public function show(Location $location)
    {
        $fields = $location->fields()->withCount('towers')->orderBy('name')->get();
        $wellsByField = $fields->isEmpty() ? collect() : OilString::join('towers', 'towers.id', '=', 'oil_strings.tower_id')
            ->whereNull('towers.deleted_at')->whereIn('towers.oil_field_id', $fields->pluck('id'))
            ->selectRaw('towers.oil_field_id as fid, count(oil_strings.id) c')->groupBy('towers.oil_field_id')->pluck('c', 'fid');

        $towers = Tower::whereIn('oil_field_id', $fields->pluck('id'))->with('oilField')->withCount('oilStrings')->orderBy('name')->get();

        return view('locations.show', [
            'location'    => $location->load('testVessels'),
            'towers'      => $towers,
            'fields'      => $fields,
            'wellsByField' => $wellsByField,
            'freeFields'  => OilField::whereNull('location_id')->orderBy('name')->get(['id', 'name', 'code']),
            'freeVessels' => TestVessel::whereNull('location_id')->orderBy('name')->get(['id', 'name', 'code']),
            'canManage'   => $this->canManage(),
        ]);
    }

    // ── Edit / update / delete ───────────────────────────────────
    public function edit(Location $location)
    {
        $this->requireManage();

        return view('locations.form', ['location' => $location, 'preset' => []]);
    }

    public function update(Request $request, Location $location)
    {
        $this->requireManage();
        $location->update($this->validated($request, $location));

        return redirect()->route('locations.show', $location)->with('success', 'Location updated.');
    }

    public function destroy(Location $location)
    {
        $this->requireManage();
        // the fields, towers and wells stay in the system, they just lose their location
        OilField::where('location_id', $location->id)->update(['location_id' => null]);
        TestVessel::where('location_id', $location->id)->update(['location_id' => null]);
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted. Its fields and testing units are now unassigned.');
    }

    // ── Assign / release existing fields and testing units ───────
    public function attach(Request $request, Location $location)
    {
        $this->requireManage();
        $data = $request->validate(['kind' => ['required', Rule::in(['field', 'vessel'])], 'id' => 'required|integer']);
        $model = $data['kind'] === 'field' ? OilField::findOrFail($data['id']) : TestVessel::findOrFail($data['id']);
        $model->update(['location_id' => $location->id]);

        return back()->with('success', "{$model->name} assigned to {$location->name}.");
    }

    public function detach(Request $request, Location $location)
    {
        $this->requireManage();
        $data = $request->validate(['kind' => ['required', Rule::in(['field', 'vessel'])], 'id' => 'required|integer']);
        $model = $data['kind'] === 'field'
            ? OilField::where('location_id', $location->id)->findOrFail($data['id'])
            : TestVessel::where('location_id', $location->id)->findOrFail($data['id']);
        $model->update(['location_id' => null]);

        return back()->with('success', "{$model->name} removed from this location (nothing was deleted).");
    }

    private function validated(Request $request, ?Location $location = null): array
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => ['required', 'string', 'max:60', Rule::unique('locations', 'code')->whereNull('deleted_at')->ignore($location?->id)],
            'environment'   => ['required', Rule::in(array_keys(Location::TYPES))],
            'status'        => ['nullable', Rule::in(array_keys(Location::STATUSES))],
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'water_depth_m' => 'nullable|numeric|min:0|max:5000',
            'description'   => 'nullable|string|max:3000',
        ]);
        $data['status'] = $data['status'] ?? 'active';
        if (!in_array($data['environment'], ['offshore', 'nearshore'], true)) {
            $data['water_depth_m'] = null;
        }

        return $data;
    }
}
