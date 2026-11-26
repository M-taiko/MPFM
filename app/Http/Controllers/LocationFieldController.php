<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\OilField;
use App\Models\OilString;
use App\Models\Tower;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Levels below a location: Oil field > Tower (wellhead tower, platform, ...) > Well string 1, 2, 3...
 */
class LocationFieldController extends Controller
{
    private function canManage(): bool
    {
        return auth()->user()?->canManageLocations() ?? false;
    }

    private function requireManage(): void
    {
        abort_unless($this->canManage(), 403, 'You are not allowed to manage locations.');
    }

    private function inLocation(Location $location, OilField $field): void
    {
        abort_unless($field->location_id === $location->id, 404);
    }

    private function inField(OilField $field, Tower $tower): void
    {
        abort_unless($tower->oil_field_id === $field->id, 404);
    }

    // ═════ Oil field ═════════════════════════════════════════════
    public function field(Location $location, OilField $field)
    {
        $this->inLocation($location, $field);
        $towers = $field->towers()->withCount('oilStrings')->orderBy('name')->get();

        return view('locations.field', [
            'location'  => $location,
            'field'     => $field,
            'towers'    => $towers,
            'wellCount' => $towers->sum('oil_strings_count'),
            'canManage' => $this->canManage(),
        ]);
    }

    public function storeField(Request $request, Location $location)
    {
        $this->requireManage();
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:255', Rule::unique('oil_fields', 'code')],
            'status'      => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:3000',
        ]);
        $field = OilField::create($data + ['location_id' => $location->id]);

        return redirect()->route('locations.field', [$location, $field])->with('success', "Field {$field->name} created. Add its towers below.");
    }

    public function updateField(Request $request, Location $location, OilField $field)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $field->update($request->validate([
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:255', Rule::unique('oil_fields', 'code')->ignore($field->id)],
            'status'      => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:3000',
        ]));

        return back()->with('success', 'Field updated.');
    }

    public function destroyField(Location $location, OilField $field)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        if ($field->towers()->exists()) {
            return back()->withErrors(['delete' => "Field {$field->name} still has towers. Delete or move its towers first."]);
        }
        $field->delete();

        return redirect()->route('locations.show', $location)->with('success', "Field {$field->name} deleted.");
    }

    // ═════ Towers managed straight from the location page ════════
    public function storeLocationTower(Request $request, Location $location)
    {
        $this->requireManage();
        $field = $this->fieldFor($request, $location);
        $tower = Tower::create($this->towerData($request) + ['oil_field_id' => $field->id]);

        return redirect()->route('locations.show', $location)->with('success', "{$tower->typeLabel()} {$tower->name} added to {$location->name}.");
    }

    public function updateLocationTower(Request $request, Location $location, Tower $tower)
    {
        $this->requireManage();
        abort_unless(optional($tower->oilField)->location_id === $location->id, 404);
        $data = $this->towerData($request, $tower);
        if ($request->filled('oil_field_id')) {
            $data['oil_field_id'] = $this->fieldFor($request, $location)->id;
        }
        $tower->update($data);

        return redirect()->route('locations.show', $location)->with('success', "{$tower->name} updated.");
    }

    public function destroyLocationTower(Location $location, Tower $tower)
    {
        $this->requireManage();
        abort_unless(optional($tower->oilField)->location_id === $location->id, 404);
        if ($tower->oilStrings()->exists()) {
            return back()->withErrors(['delete' => "{$tower->name} still has wells. Delete or move its wells first."]);
        }
        $tower->delete();

        return redirect()->route('locations.show', $location)->with('success', "{$tower->name} deleted.");
    }

    /** The oil field a new tower goes into: the chosen one, else the first of the location, else a default one named after the location */
    private function fieldFor(Request $request, Location $location): OilField
    {
        if ($request->filled('oil_field_id')) {
            return OilField::where('location_id', $location->id)->findOrFail($request->input('oil_field_id'));
        }
        $existing = $location->fields()->orderBy('id')->first();
        if ($existing) {
            return $existing;
        }
        $base = ($location->code ?: 'LOC') . '-F';
        $code = $base;
        for ($i = 2; OilField::withTrashed()->where('code', $code)->exists(); $i++) {
            $code = $base . $i;
        }

        return OilField::create(['name' => $location->name, 'code' => $code, 'status' => 'active', 'location_id' => $location->id]);
    }

    // ═════ Tower ═════════════════════════════════════════════════
    public function storeTower(Request $request, Location $location, OilField $field)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $tower = Tower::create($this->towerData($request) + ['oil_field_id' => $field->id]);

        return redirect()->route('locations.tower', [$location, $field, $tower])->with('success', "{$tower->typeLabel()} {$tower->name} created. Add its wells below.");
    }

    public function updateTower(Request $request, Location $location, OilField $field, Tower $tower)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        $tower->update($this->towerData($request, $tower));

        return back()->with('success', 'Tower updated.');
    }

    public function destroyTower(Location $location, OilField $field, Tower $tower)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        if ($tower->oilStrings()->exists()) {
            return back()->withErrors(['delete' => "{$tower->name} still has wells. Delete or move its wells first."]);
        }
        $tower->delete();

        return redirect()->route('locations.field', [$location, $field])->with('success', "{$tower->name} deleted.");
    }

    public function tower(Location $location, OilField $field, Tower $tower)
    {
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        $wells = $tower->oilStrings()->withCount('mpfmTests')->withMax('mpfmTests as last_test_at', 'test_date')
            ->orderByRaw('string_number is null')->orderBy('string_number')->orderBy('name')->get();

        return view('locations.tower', [
            'location'  => $location,
            'field'     => $field,
            'tower'     => $tower,
            'wells'     => $wells,
            'nextString' => ($wells->max('string_number') ?? 0) + 1,
            'canManage' => $this->canManage(),
        ]);
    }

    // ═════ Well string ═══════════════════════════════════════════
    public function storeWell(Request $request, Location $location, OilField $field, Tower $tower)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        OilString::create($this->wellData($request) + ['tower_id' => $tower->id]);

        return back()->with('success', 'Well added.');
    }

    public function updateWell(Request $request, Location $location, OilField $field, Tower $tower, OilString $well)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        abort_unless($well->tower_id === $tower->id, 404);
        $well->update($this->wellData($request, $well));

        return back()->with('success', 'Well updated.');
    }

    public function destroyWell(Location $location, OilField $field, Tower $tower, OilString $well)
    {
        $this->requireManage();
        $this->inLocation($location, $field);
        $this->inField($field, $tower);
        abort_unless($well->tower_id === $tower->id, 404);
        if ($well->mpfmTests()->exists() || $well->wirelineOperations()->exists()) {
            return back()->withErrors(['delete' => "{$well->name} has recorded tests or Wire Line work, so it cannot be deleted. Set its status to inactive instead."]);
        }
        $well->delete();

        return back()->with('success', "{$well->name} deleted.");
    }

    // ── validation ───────────────────────────────────────────────
    private function towerData(Request $request, ?Tower $tower = null): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:255', Rule::unique('towers', 'code')->whereNull('deleted_at')->ignore($tower?->id)],
            'tower_type'  => ['required', Rule::in(array_keys(Tower::TYPES))],
            'status'      => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:3000',
        ]);
    }

    private function wellData(Request $request, ?OilString $well = null): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => ['required', 'string', 'max:255', Rule::unique('oil_strings', 'code')->ignore($well?->id)],
            'string_number' => 'nullable|integer|min:1|max:99',
            'status'        => 'required|in:active,inactive,maintenance',
            'description'   => 'nullable|string|max:3000',
        ]);
    }
}
