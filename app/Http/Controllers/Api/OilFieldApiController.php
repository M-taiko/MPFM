<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OilField;
use Illuminate\Http\Request;

class OilFieldApiController extends Controller
{
    /**
     * Get towers for a specific oil field
     */
    public function getTowers($oilFieldId)
    {
        $oilField = OilField::findOrFail($oilFieldId);
        $towers = $oilField->towers()->select('id', 'name', 'oil_field_id')->get();

        return response()->json($towers);
    }

    /**
     * Get oil strings for a specific oil field
     */
    public function getOilStrings($oilFieldId)
    {
        $oilField = OilField::with('towers.oilStrings')->findOrFail($oilFieldId);

        $oilStrings = collect();
        foreach ($oilField->towers as $tower) {
            foreach ($tower->oilStrings as $string) {
                $oilStrings->push([
                    'id' => $string->id,
                    'name' => $string->name,
                    'tower' => [
                        'id' => $tower->id,
                        'name' => $tower->name
                    ]
                ]);
            }
        }

        return response()->json($oilStrings);
    }
}
