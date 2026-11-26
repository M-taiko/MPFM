<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Top of the hierarchy: Location (onshore / offshore / ...) > Oil field > Tower > Well string.
 */
class Location extends Model
{
    use SoftDeletes;

    /** key => [label, colour, icon, description] */
    public const TYPES = [
        'onshore'   => ['Onshore',        '#fbbf24', 'fe-sun',      'Desert / land: wellsites, towers, stations'],
        'offshore'  => ['Offshore',       '#38bdf8', 'fe-anchor',   'Sea: platforms, wellhead towers, subsea, floating units'],
        'nearshore' => ['Shallow water',  '#2dd4bf', 'fe-droplet',  'Nearshore and shallow-water sites'],
        'island'    => ['Island',         '#a78bfa', 'fe-flag',     'Natural or artificial islands'],
    ];

    public const STATUSES = ['active' => 'Active', 'maintenance' => 'Maintenance', 'inactive' => 'Inactive'];

    protected $fillable = ['name', 'code', 'environment', 'status', 'latitude', 'longitude', 'water_depth_m', 'description'];

    protected $casts = ['latitude' => 'float', 'longitude' => 'float', 'water_depth_m' => 'float'];

    public function fields(): HasMany
    {
        return $this->hasMany(OilField::class);
    }

    public function testVessels(): HasMany
    {
        return $this->hasMany(TestVessel::class);
    }

    public function envLabel(): string
    {
        return self::TYPES[$this->environment][0] ?? ucfirst($this->environment);
    }

    public function envColor(): string
    {
        return self::TYPES[$this->environment][1] ?? '#94a3b8';
    }

    public function envIcon(): string
    {
        return self::TYPES[$this->environment][2] ?? 'fe-map-pin';
    }

    /** Offshore-like types record a water depth */
    public function hasWaterDepth(): bool
    {
        return in_array($this->environment, ['offshore', 'nearshore'], true);
    }

    /**
     * Fields, towers and wells per location, in three grouped queries.
     * @return array<int, array{fields:int, towers:int, wells:int}>
     */
    public static function stats(?array $ids = null): array
    {
        $fields = OilField::whereNotNull('location_id')->when($ids, fn ($q) => $q->whereIn('location_id', $ids))
            ->selectRaw('location_id, count(*) c')->groupBy('location_id')->pluck('c', 'location_id');
        $towers = Tower::join('oil_fields', 'oil_fields.id', '=', 'towers.oil_field_id')->whereNull('oil_fields.deleted_at')->whereNull('towers.deleted_at')
            ->whereNotNull('oil_fields.location_id')->when($ids, fn ($q) => $q->whereIn('oil_fields.location_id', $ids))
            ->selectRaw('oil_fields.location_id as lid, count(towers.id) c')->groupBy('oil_fields.location_id')->pluck('c', 'lid');
        $wells = OilString::join('towers', 'towers.id', '=', 'oil_strings.tower_id')->join('oil_fields', 'oil_fields.id', '=', 'towers.oil_field_id')
            ->whereNull('oil_strings.deleted_at')->whereNull('towers.deleted_at')->whereNull('oil_fields.deleted_at')
            ->whereNotNull('oil_fields.location_id')->when($ids, fn ($q) => $q->whereIn('oil_fields.location_id', $ids))
            ->selectRaw('oil_fields.location_id as lid, count(oil_strings.id) c')->groupBy('oil_fields.location_id')->pluck('c', 'lid');

        $out = [];
        foreach (array_unique(array_merge($fields->keys()->all(), $towers->keys()->all(), $wells->keys()->all(), $ids ?? [])) as $id) {
            $out[$id] = ['fields' => (int) ($fields[$id] ?? 0), 'towers' => (int) ($towers[$id] ?? 0), 'wells' => (int) ($wells[$id] ?? 0)];
        }

        return $out;
    }
}
