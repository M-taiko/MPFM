<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WellBarrierElement extends Model
{
    /** key => [label, envelope, default test interval (days)] */
    public const TYPES = [
        'tubing'         => ['Production tubing',        'primary',   365],
        'packer'         => ['Production packer',        'primary',   365],
        'scssv'          => ['SCSSV / DHSV',             'primary',   180],
        'prod_casing'    => ['Production casing',        'secondary', 365],
        'tubing_hanger'  => ['Tubing hanger & seals',    'secondary', 365],
        'wellhead'       => ['Wellhead / casing hanger', 'secondary', 365],
        'master_valve'   => ['Master valve(s)',          'secondary', 180],
        'wing_valve'     => ['Wing valve',               'secondary', 180],
        'annulus_valve'  => ['Annulus valves',           'secondary', 180],
    ];

    public const ENVELOPES = ['primary' => 'Primary barrier envelope', 'secondary' => 'Secondary barrier envelope'];

    public const STATUSES = ['untested' => 'Not tested', 'healthy' => 'Healthy', 'degraded' => 'Degraded', 'failed' => 'Failed'];

    protected $fillable = [
        'oil_string_id', 'envelope', 'element_type', 'name', 'status', 'test_interval_days', 'last_test_date', 'next_due_date', 'remarks',
    ];

    protected $casts = ['last_test_date' => 'date', 'next_due_date' => 'date', 'test_interval_days' => 'integer'];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(WellIntegrityTest::class)->orderByDesc('test_date')->orderByDesc('id');
    }

    public function isOverdue(): bool
    {
        return $this->next_due_date !== null && $this->next_due_date->isPast() && !$this->next_due_date->isToday();
    }

    /** Re-derive status / last test / next due from the latest test on record */
    public function syncFromTests(): void
    {
        $last = $this->tests()->first();
        if (!$last) {
            $this->update(['status' => 'untested', 'last_test_date' => null, 'next_due_date' => null]);
            return;
        }
        $this->update([
            'status'         => ['pass' => 'healthy', 'marginal' => 'degraded', 'fail' => 'failed'][$last->result],
            'last_test_date' => $last->test_date,
            // a failed element must be re-tested sooner
            'next_due_date'  => $last->test_date->copy()->addDays($last->result === 'fail' ? 30 : $this->test_interval_days),
        ]);
    }
}
