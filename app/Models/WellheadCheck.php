<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Operator wellhead check: valves operated, turns counted, greased, annulus pressures bled/verified.
 */
class WellheadCheck extends Model
{
    public const STATUSES = ['online' => 'Online', 'offline' => 'Offline', 'suspended' => 'Suspended', 'shut_in' => 'Shut-in'];
    public const TREE_CAP = ['ok' => 'Ok', 'not_ok' => 'Not ok', 'na' => 'N/A'];
    public const CONDITIONS = ['ok' => 'Ok', 'stiff' => 'Stiff', 'leaking' => 'Leaking', 'seized' => 'Not operating'];

    /** code => [label, barrier element type it exercises] */
    public const VALVES = [
        'SV'  => ['Safety valve (SV)',      null],
        'UMV' => ['Upper master valve (UMV)', 'master_valve'],
        'LMV' => ['Lower master valve (LMV)', 'master_valve'],
        'WV'  => ['Wing valve (WV)',        'wing_valve'],
    ];

    /** annulus code => [default valve ref, default turns, default DBB side] */
    public const ANNULI = ['A' => ['ABCD', 13, 'RHS'], 'B' => ['BD', 13, 'LHS'], 'C' => ['CD', 13, 'RHS']];

    protected $fillable = [
        'oil_string_id', 'check_date', 'wht', 'well_no', 'oem', 'string_no', 'well_status', 'whcip',
        'tree_cap', 'remarks', 'checked_by', 'created_by',
    ];

    protected $casts = ['check_date' => 'date', 'whcip' => 'float'];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WellheadCheckItem::class)->orderBy('id');
    }

    public function valves()
    {
        return $this->items->where('item_type', 'valve');
    }

    public function annuli()
    {
        return $this->items->where('item_type', 'annulus');
    }

    /** True when anything was not ok */
    public function hasIssues(): bool
    {
        return $this->tree_cap === 'not_ok' || $this->items->contains(fn ($i) => $i->condition && $i->condition !== 'ok');
    }

    /** The report exactly as the operators send it (WhatsApp style) */
    public function reportText(): string
    {
        $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
        $L = [];
        $L[] = $this->check_date->format('d/m/Y');
        $L[] = '';
        $L[] = 'WHT : ' . ($this->wht ?: '-');
        $L[] = '';
        $L[] = 'WELL NO:  ' . ($this->well_no ?: '-');
        $L[] = 'OEM : ' . ($this->oem ?: '-');
        $L[] = '';
        $L[] = 'STRING -' . $this->string_no;
        $L[] = '';
        $L[] = 'Status : ' . strtolower(self::STATUSES[$this->well_status] ?? $this->well_status);
        if ($this->whcip !== null) {
            $L[] = 'WHCIP : ' . $n($this->whcip) . ' psi';
        }
        $L[] = 'Tree cap : ' . (self::TREE_CAP[$this->tree_cap] ?? $this->tree_cap);
        foreach ($this->valves() as $v) {
            $L[] = $v->code . ' : ' . (self::CONDITIONS[$v->condition] ?? '-');
            if ($v->turns !== null) {
                $L[] = 'Turns: ' . $n($v->turns) . ' turns';
            }
            if ($v->time_sec !== null) {
                $L[] = 'Turns/Time: ' . $v->time_sec . ' sec';
            }
            if ($v->greased) {
                $L[] = 'Greased: Yes';
            }
            if ($v->notes) {
                $L[] = 'Note: ' . $v->notes;
            }
        }
        $L[] = '';
        $L[] = 'Remarks:';
        $L[] = $this->remarks ?: '-';
        foreach ($this->annuli() as $a) {
            $L[] = '';
            $L[] = 'Annulus ' . $a->code . ' - ' . ($a->pressure !== null ? $n($a->pressure) : '-') . ' psi';
            if ($a->valve_ref) {
                $L[] = 'Valve ' . $a->valve_ref . ($a->turns !== null ? ' (' . $n($a->turns) . ' turns)' : '')
                    . ($a->dbb_side ? ' DBB at ' . $a->dbb_side : '')
                    . ($a->condition && $a->condition !== 'ok' ? ' - ' . strtoupper(self::CONDITIONS[$a->condition]) : '')
                    . ($a->greased ? ' - greased' : '');
            }
        }

        return implode("\n", $L);
    }
}
