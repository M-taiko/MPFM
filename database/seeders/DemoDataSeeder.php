<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\OilField;
use App\Models\OilString;
use App\Models\TestVessel;
use App\Models\Tower;
use App\Models\User;
use App\Models\WellBarrierElement;
use App\Models\WellTest;
use App\Models\WellIntegrityAnnulus;
use App\Models\WellIntegrityAnnulusReading;
use App\Models\WellIntegrityAnomaly;
use App\Models\WellIntegrityInspection;
use App\Models\WellIntegrityProfile;
use App\Models\WellIntegrityTest;
use App\Models\WellheadCheck;
use App\Models\WirelineGaugeRun;
use App\Models\WirelineOperation;
use App\Services\Wireline\GaugeRunAnalyzer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Presentation data for a client demo, built on the wells already in the database.
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Safe to run again: everything it creates is tagged "[demo]" (or belongs to the demo wells) and is rebuilt each time.
 * It never touches data that is not tagged, except the integrity records of the demo wells themselves.
 */
class DemoDataSeeder extends Seeder
{
    private const TAG = '[demo]';
    private Carbon $today;
    private array $operators = [];
    private array $wells = [];      // idx => ['id','name','tower','field', 'base' => [...]]
    private ?int $supervisor = null;
    private ?int $surveillance = null;

    public function run(): void
    {
        mt_srand(2026);
        $this->today = Carbon::create(2026, 11, 26)->startOfDay();

        DB::transaction(function () {
            $this->users();
            $this->ensureBase();
            $this->hierarchy();
            $this->pickWells();
            $this->vessels();
            $this->cleanup();
            $this->mpfmAndVesselTests();
            $this->archive();
            $this->vesselRequests();
            $this->wireline();
            $this->integrity();
        });

        $this->command?->info('Demo data ready. Login: donia.a5ra2019@gmail.com / 123456789');
    }

    // ═════ users ═════════════════════════════════════════════════
    private function users(): void
    {
        $user = User::updateOrCreate(['email' => 'donia.a5ra2019@gmail.com'], [
            'name' => 'Donia', 'password' => Hash::make('123456789'), 'email_verified_at' => now(),
        ]);
        foreach (['admin'] as $role) {
            if (!$user->hasRole($role)) { $user->assignRole($role); }
        }
        $this->operators = User::role(['mpfm_operator', 'operator'])->pluck('id')->all() ?: [$user->id];
        $this->supervisor = User::role('department_manager')->value('id') ?? $user->id;
        $this->surveillance = User::role('surveillance_supervisor')->value('id') ?? $user->id;
    }

    // ═════ base fields, towers and wells (so the seeder also works on an empty database) ═════
    private function ensureBase(): void
    {
        $defs = ['ZK' => 'Lower Zakum West Field', 'UM' => 'Umm Shaif Field'];
        foreach ($defs as $prefix => $name) {
            $field = OilField::firstOrCreate(['code' => $prefix], ['name' => $name, 'status' => 'active', 'description' => 'Offshore field']);
            foreach (range(100, 109) as $n) {
                $tn = "$prefix-$n";
                $tower = Tower::where('name', $tn)->first()
                    ?? Tower::create(['oil_field_id' => $field->id, 'name' => $tn, 'code' => strtolower($tn), 'tower_type' => 'wellhead_tower', 'status' => 'active']);
                foreach ([1, 2] as $s) {
                    $sn = "$tn/$s";
                    if (!OilString::where('name', $sn)->exists()) {
                        OilString::create(['tower_id' => $tower->id, 'name' => $sn, 'code' => $sn, 'string_number' => $s, 'status' => 'active']);
                    }
                }
            }
        }
    }

    // ═════ Test archive: the two latest approved MPFM tests of every well, as uploaded Vx log files ═════
    private const VX_COLUMNS = ['Date & Time', 'N32[cps]', 'N81[cps]', 'N356[cps]', 'NTotal[cps]', 'qo_lc[bbl/d]', 'qw_lc[bbl/d]', 'qg_lc[Mcf/d]', 'qo_sc[STB/d]', 'qw_sc[STB/d]', 'qg_sc[MMScf/d]',
        'qo_scnp[STB/d]', 'qw_scnp[STB/d]', 'qg_scnp[MMScf/d]', 'Fo_lc[%]', 'Fw_lc[%]', 'Fg_lc[%]', 'mo_lc[kg/d]', 'mw_lc[kg/d]', 'mg_lc[kg/d]', 'm_lc[kg/d]', 'mo_sc[kg/d]', 'mw_sc[kg/d]', 'mg_sc[kg/d]',
        'Do_lc[kg/m³]', 'Dw_lc[kg/m³]', 'Dg_lc[kg/m³]', 'Dm_lc[kg/m³]', 'Dl_lc[kg/m³]', 'Mu_o_lc[cP]', 'Mu_l_lc[cP]', 'WLR[%]', 'BSW[%]', 'GVF[%]', 'GLR[m³/m³]', 'GOR[Scf/STB]', 'GOR1[Scf/STB]', 'CGRAvg[Scf/STB]',
        'bo[Sm³/m³]', 'bw[Sm³/m³]', 'bg[Sm³/m³]', 'Z', 'Rst[Scf/STB]', 'Rwst[Scf/STB]', 'rgmp[STB/Scf]', 'Dyn_DP[mbar]', 'DPV[mbar]', 'PIN[bara]', 'TL[°C]', 'Pthroat', 'DAFC_InputVolt[V]', 'DAFC_InputCurr[A]',
        'DAFC_5Volt[V]', 'DAFC_24Volt[V]', 'DAFC_Temp[°C]', 'DAFC_Hum[%RH]', 'HVGain[V]', 'DetectorTemp[°C]', 'cvo_lc[bbl]', 'cvw_lc[bbl]', 'cvg_lc[cf]', 'cvo_sc[STB]', 'cvw_sc[STB]', 'cvg_sc[Scf]',
        'cvo_scnp[STB]', 'cvw_scnp[STB]', 'cvg_scnp[Scf]', 'cmo_lc[kg]', 'cmw_lc[kg]', 'cmg_lc[kg]', 'cmt_lc[kg]', 'OperatingPointLE[1/m]', 'OperatingPointHE[1/m]', 'OilPointLE[1/m]', 'OilPointHE[1/m]',
        'WaterPointLE[1/m]', 'WaterPointHE[1/m]', 'GasPointLE[1/m]', 'GasPointHE[1/m]', 'EVODeconvN32[cps]', 'EVODeconvN81[cps]', 'EVODeconvN356[cps]', 'EVOChiSquare', 'EVOPHR[%]', 'EVOHECenter',
        'EVOLP_VHE_VHE', 'EVOLP_HE_HE', 'EVOLP_VHE_HE', 'EVOLP_LE_LE', 'EVOLP_VHE_LE', 'EVOLP_HE_LE'];

    private function archive(): void
    {
        WellTest::where('log_file_path', 'like', 'demo/%')->delete();
        $cols = self::VX_COLUMNS;
        $colTypes = collect($cols)->mapWithKeys(fn ($c) => [$c => $c === 'Date & Time' ? 'text' : 'numeric'])->all();
        $fieldNames = OilField::pluck('name', 'id');
        $towerNames = Tower::pluck('name', 'id');
        $stringNames = OilString::pluck('name', 'id');
        $userNames = User::pluck('name', 'id');
        $fmt = fn ($v) => sprintf('%20s', rtrim(rtrim(number_format((float) $v, abs($v) >= 1000 ? 1 : 3, '.', ''), '0'), '.') ?: '0');
        $seq = 0;

        foreach ($this->wells as $w) {
            $tests = DB::table('mpfm_tests')->where('oil_string_id', $w['id'])->where('notes', 'like', self::TAG . '%')->where('status', 'approved')
                ->orderByDesc('test_date')->limit(2)->get();
            foreach ($tests as $t) {
                $seq++;
                $start = Carbon::parse($t->test_started_at);
                $nRows = 64; $stepMin = max(2, (int) round(($t->test_duration_minutes ?? 220) / $nRows));
                $oil = (float) $t->oil_rate; $wc = (float) $t->water_cut; $gor = (float) $t->gor; $gasMm = (float) $t->gas_rate;
                $water = $wc < 100 ? $oil * $wc / (100 - $wc) : 0.0;
                $pBar = (float) $t->pressure / 14.5038; $temp = (float) $t->temperature;
                $cumO = $cumW = $cumG = 0.0;
                $rows = []; $sumP = []; $sumT = [];
                for ($i = 0; $i < $nRows; $i++) {
                    $n = fn ($a = .02) => 1 + mt_rand(-1000, 1000) / 1000 * $a;
                    $ro = $oil * $n(); $rw = $water * $n(.04); $rg = $gasMm * $n(); $p = $pBar * $n(.004); $tp = $temp + mt_rand(-30, 30) / 100;
                    $dtDay = $stepMin / 1440; $cumO += $ro * $dtDay; $cumW += $rw * $dtDay; $cumG += $rg * 1e6 * $dtDay;
                    $liq = max(1e-6, $ro + $rw); $fo = 100 * $ro / ($liq + $rg * 40); $fw = 100 * $rw / ($liq + $rg * 40); $fg = 100 - $fo - $fw;
                    $gvf = min(95, max(20, $fg + mt_rand(-10, 10) / 10));
                    $v = [
                        'Date & Time' => $start->copy()->addMinutes($i * $stepMin)->addSeconds(mt_rand(0, 40))->format('d-M-Y H:i:s'),
                        'N32[cps]' => 20800 * $n(.03), 'N81[cps]' => 9880 * $n(.03), 'N356[cps]' => 5140 * $n(.03), 'NTotal[cps]' => 44200 * $n(.03),
                        'qo_lc[bbl/d]' => $ro * 1.18, 'qw_lc[bbl/d]' => $rw * 1.1, 'qg_lc[Mcf/d]' => $rg * 1000 * 0.09,
                        'qo_sc[STB/d]' => $ro, 'qw_sc[STB/d]' => $rw, 'qg_sc[MMScf/d]' => $rg, 'qo_scnp[STB/d]' => $ro, 'qw_scnp[STB/d]' => $rw, 'qg_scnp[MMScf/d]' => $rg * 0.93,
                        'Fo_lc[%]' => $fo, 'Fw_lc[%]' => $fw, 'Fg_lc[%]' => $fg,
                        'WLR[%]' => $wc * $n(.03), 'BSW[%]' => $wc * $n(.03), 'GVF[%]' => $gvf, 'GOR[Scf/STB]' => $gor * $n(.03), 'GOR1[Scf/STB]' => $gor * $n(.05),
                        'Dyn_DP[mbar]' => 380 * $n(.05), 'DPV[mbar]' => 470 * $n(.04), 'PIN[bara]' => $p, 'TL[°C]' => $tp,
                        'DAFC_InputVolt[V]' => 23.8 * $n(.01), 'DAFC_InputCurr[A]' => .565 * $n(.02), 'DAFC_5Volt[V]' => 4.977 * $n(.002), 'DAFC_24Volt[V]' => 24.1 * $n(.004),
                        'DAFC_Temp[°C]' => 35 + mt_rand(-20, 20) / 10, 'DAFC_Hum[%RH]' => 64 + mt_rand(-30, 30) / 10, 'HVGain[V]' => 580 * $n(.003), 'DetectorTemp[°C]' => 40.4 * $n(.01),
                        'cvo_sc[STB]' => $cumO, 'cvw_sc[STB]' => $cumW, 'cvg_sc[Scf]' => $cumG, 'cvo_lc[bbl]' => $cumO * 1.18, 'cvw_lc[bbl]' => $cumW * 1.1, 'cvg_lc[cf]' => $cumG * .09,
                        'Z' => .85 * $n(.01), 'bo[Sm³/m³]' => .83 * $n(.01), 'bw[Sm³/m³]' => .98 * $n(.005), 'bg[Sm³/m³]' => 68 * $n(.01), 'Rst[Scf/STB]' => 330 * $n(.02),
                        'Do_lc[kg/m³]' => 750 * $n(.01), 'Dw_lc[kg/m³]' => 1176 * $n(.005), 'Dg_lc[kg/m³]' => 64 * $n(.02), 'Mu_o_lc[cP]' => .64 * $n(.02),
                    ];
                    $r = [];
                    foreach ($cols as $c) {
                        $r[$c] = $c === 'Date & Time' ? $v[$c] : $fmt($v[$c] ?? (mt_rand(0, 100) / 100 * 5));
                    }
                    $rows[] = $r; $sumP[] = $p * 14.5038; $sumT[] = $tp;
                }
                $towerName = $towerNames[$w['tower_id']] ?? '';
                $stringName = $stringNames[$w['id']] ?? '';
                $stringNo = (int) (substr(strrchr($stringName, '/'), 1) ?: 1);
                $filename = sprintf('%s %d-wp%03d-%s-%s.vxlog', $towerName, $stringNo, $seq % 1000, $start->format('dMY'), $start->format('His'));
                $metrics = [
                    'oil_rate' => $oil, 'water_rate' => round($water, 2), 'gas_rate' => round($gasMm * 1000 * 0.09, 3), 'gor' => $gor, 'bsw' => $wc, 'wlr' => $wc,
                    'gvf' => round(collect($rows)->avg(fn ($r) => (float) $r['GVF[%]']), 2),
                    'max_pressure' => round(max($sumP), 1), 'min_pressure' => round(min($sumP), 1), 'avg_pressure' => round(array_sum($sumP) / count($sumP), 1),
                    'max_temperature' => round(max($sumT), 2), 'min_temperature' => round(min($sumT), 2), 'avg_temperature' => round(array_sum($sumT) / count($sumT), 2),
                    'cumulative_oil_volume' => round($cumO, 1), 'cumulative_water_volume' => round($cumW, 1), 'cumulative_gas_volume' => round($cumG, 0),
                    'h2s_avg' => 'N/A', 'h2s_max' => 'N/A', 'h2s_min' => 'N/A',
                    'bo' => .83, 'bw' => .978, 'bg' => 68.1, 'z_factor' => .87, 'rst' => 329.4, 'po' => 749.8, 'pw' => 1176.2, 'pg' => 64.1,
                    'wlr_formula' => $wc, 'gor_formula' => round($gor / 100000, 5), 'bsw_formula' => $wc, 'gvf_formula' => 4.27, 'safety_issues' => [],
                    'water_cut' => $wc, 'pressure' => round(array_sum($sumP) / count($sumP), 1), 'temperature' => round(array_sum($sumT) / count($sumT), 2),
                ];
                $wt = WellTest::create([
                    'client' => 'National Oil Company', 'field' => $fieldNames[$w['field_id']] ?? '', 'well' => $towerName, 'wellhead_tower' => $towerName, 'wellhead_string' => $stringName,
                    'operator' => $userNames[$t->user_id] ?? 'Operator', 'test_date' => $t->test_date, 'test_type' => 'flow',
                    'choke_size' => (string) [20, 24, 28, 32, 36, 40, 48][mt_rand(0, 6)], 'upstream_choke' => 'N/A', 'downstream_choke' => 'N/A',
                    'start_time' => $start, 'end_time' => $start->copy()->addMinutes(($nRows - 1) * $stepMin),
                    'metadata' => ['Log File Version' => '04.20', 'Vx DAFC version' => '4.6.25.1', 'File created:' => $start->format('d M Y, H:i:s'), 'DAFC Serial Number' => (string) (31140000 + $w['id']),
                        'DAFC HW Version' => '0103', 'DAFC SW Version' => '4.6.25.1', 'DAFC FW Version' => 'v0', 'GD Serial Number' => (string) (8200 + $w['id']),
                        'Configuration name' => $stringName, 'Mode' => 'Full Range Mode', 'total_rows' => $nRows, 'total_columns' => count($cols), 'delimiter' => "\t", 'column_types' => $colTypes],
                    'test_data' => ['columns' => $cols, 'rows' => array_map('array_values', $rows)],
                    'calculated_metrics' => $metrics, 'filename' => $filename, 'log_file_path' => 'demo/' . $filename,
                ]);
                // the log was uploaded the day it was tested
                DB::table('well_tests')->where('id', $wt->id)->update(['created_at' => $start->copy()->addHours(5), 'updated_at' => $start->copy()->addHours(5)]);
                DB::table('mpfm_tests')->where('id', $t->id)->update(['well_test_id' => $wt->id, 'log_file_path' => 'demo/' . $filename]);
            }
        }
    }

    // ═════ Location > Field > Tower > Well ═══════════════════════
    private function hierarchy(): void
    {
        $zk = OilField::where('code', 'ZK')->first();
        $um = OilField::where('code', 'UM')->first();

        $locZk = Location::whereIn('code', ['OFF-ZK'])->first() ?? Location::where('environment', 'offshore')->where('name', 'like', '%zakum%')->first()
            ?? Location::create(['name' => 'Zakum Offshore', 'code' => 'OFF-ZK', 'environment' => 'offshore', 'status' => 'active', 'water_depth_m' => 38, 'latitude' => 24.9, 'longitude' => 53.9]);
        $locUm = Location::updateOrCreate(['code' => 'OFF-UM'], ['name' => 'Umm Shaif Offshore', 'environment' => 'offshore', 'status' => 'active', 'water_depth_m' => 27, 'latitude' => 25.2, 'longitude' => 52.9, 'description' => 'Shallow-water platforms and wellhead towers']);
        $locBh = Location::updateOrCreate(['code' => 'ON-BH'], ['name' => 'Bu Hasa Desert', 'environment' => 'onshore', 'status' => 'active', 'latitude' => 23.6, 'longitude' => 53.3, 'description' => 'Desert wellsites and a gathering station']);
        Location::updateOrCreate(['code' => 'ISL-ZR'], ['name' => 'Zirku Island', 'environment' => 'island', 'status' => 'active', 'latitude' => 24.87, 'longitude' => 53.07, 'description' => 'Island terminal and wellsites (planned)']);

        if ($zk && !$zk->location_id) { $zk->update(['location_id' => $locZk->id]); }
        if ($um && !$um->location_id) { $um->update(['location_id' => $locUm->id]); }

        // onshore field with desert wellsites
        $bh = OilField::firstOrCreate(['code' => 'BH'], ['name' => 'Bu Hasa Field', 'status' => 'active', 'description' => 'Onshore desert field', 'location_id' => $locBh->id]);
        foreach ([['BH-100', 'desert_wellsite'], ['BH-101', 'desert_wellsite'], ['BH-102', 'desert_wellsite'], ['BH-GS1', 'flow_station']] as [$code, $type]) {
            $t = Tower::firstOrCreate(['code' => $code], ['oil_field_id' => $bh->id, 'name' => $code, 'tower_type' => $type, 'status' => 'active']);
            if ($type === 'desert_wellsite') {
                foreach ([1, 2] as $n) {
                    OilString::firstOrCreate(['code' => "$code-S$n"], ['tower_id' => $t->id, 'name' => "$code/$n", 'string_number' => $n, 'status' => 'active']);
                }
            }
        }

        // tower variety and string numbers on the existing wells
        $types = ['ZK-104' => 'platform', 'ZK-105' => 'platform', 'ZK-108' => 'subsea', 'UM-100' => 'platform', 'UM-104' => 'platform', 'UM-108' => 'subsea'];
        foreach ($types as $code => $type) { Tower::where('code', $code)->update(['tower_type' => $type]); }
        foreach (Tower::with(['oilStrings' => fn ($q) => $q->orderBy('id')])->get() as $tower) {
            foreach ($tower->oilStrings->values() as $i => $w) {
                if (!$w->string_number) { $w->update(['string_number' => $i + 1]); }
            }
        }
    }

    private function pickWells(): void
    {
        $towers = Tower::whereIn('code', ['ZK-100', 'ZK-101', 'ZK-102', 'ZK-103', 'ZK-104', 'ZK-105', 'ZK-106', 'ZK-107', 'UM-100', 'UM-101', 'UM-102', 'UM-103', 'UM-104', 'UM-105', 'UM-106', 'UM-107', 'BH-100', 'BH-101'])
            ->with(['oilStrings' => fn ($q) => $q->orderBy('string_number')->orderBy('id')])->orderByRaw("FIELD(code,'ZK-100','ZK-101','ZK-102','ZK-103','ZK-104','ZK-105','ZK-106','ZK-107','UM-100','UM-101','UM-102','UM-103','UM-104','UM-105','UM-106','UM-107','BH-100','BH-101')")->get();
        $ordered = [];
        foreach ($towers as $t) { if ($w = $t->oilStrings->first()) { $ordered[] = [$t, $w]; } }
        $seen = array_map(fn ($p) => $p[1]->id, $ordered);
        foreach (OilString::with('tower')->orderBy('tower_id')->orderBy('string_number')->orderBy('id')->get() as $w) {
            if ($w->tower && !in_array($w->id, $seen, true) && $w->status === 'active') { $ordered[] = [$w->tower, $w]; }
        }
        foreach ($ordered as [$t, $w]) {
            $this->wells[] = [
                'id' => $w->id, 'name' => $w->name, 'tower_id' => $t->id, 'field_id' => $t->oil_field_id, 'onshore' => str_starts_with($t->code, 'BH'),
                'oil0' => mt_rand(1100, 4300), 'decl' => mt_rand(40, 140) / 1000, 'wc0' => mt_rand(40, 220) / 10, 'wcs' => mt_rand(2, 9) / 10,
                'gor' => mt_rand(420, 980), 'whp' => mt_rand(1250, 2300), 'temp' => mt_rand(62, 98), 'bhp0' => mt_rand(3300, 4700),
            ];
        }
    }

    private function vessels(): void
    {
        $zkLoc = OilField::where('code', 'ZK')->value('location_id');
        $umLoc = OilField::where('code', 'UM')->value('location_id');
        $bhLoc = OilField::where('code', 'BH')->value('location_id');
        $zkF = OilField::where('code', 'ZK')->value('id'); $umF = OilField::where('code', 'UM')->value('id'); $bhF = OilField::where('code', 'BH')->value('id');
        $defs = [
            ['V-101', 'Offshore Test Separator V-101', 'active', $zkF, $zkLoc], ['V-203', 'Offshore Test Separator V-203', 'active', $umF, $umLoc],
            ['V-118', 'Onshore Test Separator V-118', 'active', $bhF, $bhLoc], ['V-122', 'Onshore Test Separator V-122', 'maintenance', $bhF, $bhLoc],
        ];
        foreach ($defs as [$code, $name, $st, $f, $l]) {
            TestVessel::updateOrCreate(['code' => $code], ['name' => $name, 'status' => $st, 'oil_field_id' => $f, 'location_id' => $l, 'description' => 'Three-phase horizontal separator ' . self::TAG]);
        }
    }

    // ═════ wipe what this seeder created before ══════════════════
    private function cleanup(): void
    {
        DB::table('test_vessel_tests')->where('notes', 'like', self::TAG . '%')->delete();
        DB::table('mpfm_tests')->where('notes', 'like', self::TAG . '%')->delete();
        $reqIds = DB::table('tv_test_requests')->where('notes', 'like', self::TAG . '%')->pluck('id');
        DB::table('tv_test_request_wells')->whereIn('tv_test_request_id', $reqIds)->delete();
        DB::table('tv_test_requests')->whereIn('id', $reqIds)->delete();
        WirelineOperation::withTrashed()->where('remarks', 'like', self::TAG . '%')->get()->each->forceDelete();

        $ids = array_column($this->wells, 'id');
        WellIntegrityAnomaly::whereIn('oil_string_id', $ids)->delete();
        WellIntegrityInspection::whereIn('oil_string_id', $ids)->delete();
        WellheadCheck::whereIn('oil_string_id', $ids)->get()->each->delete();
        WellIntegrityTest::whereIn('oil_string_id', $ids)->delete();
        WellBarrierElement::whereIn('oil_string_id', $ids)->delete();
        WellIntegrityAnnulusReading::whereIn('oil_string_id', $ids)->delete();
        WellIntegrityAnnulus::whereIn('oil_string_id', $ids)->delete();
        WellIntegrityProfile::whereIn('oil_string_id', $ids)->delete();
    }

    // ═════ MPFM tests + the matching test-vessel tests ═══════════
    private function mpfmAndVesselTests(): void
    {
        $vesselFor = fn ($w) => $w['onshore'] ? TestVessel::where('code', 'V-118')->value('id')
            : TestVessel::where('code', $w['field_id'] === OilField::where('code', 'ZK')->value('id') ? 'V-101' : 'V-203')->value('id');
        $start = Carbon::create(2026, 2, 9);

        foreach ($this->wells as $wi => $w) {
            $n = 9 + ($wi % 4);                      // 9..12 tests
            $date = $start->copy()->addDays(($wi * 3) % 17);
            for ($i = 0; $i < $n; $i++) {
                $date = $date->copy()->addDays(mt_rand(22, 31));
                if ($date->greaterThan($this->today->copy()->subDays(2))) { break; }
                $t = $start->diffInDays($date);
                $oil = round($w['oil0'] * exp(-$w['decl'] * $t / 365) * (1 + mt_rand(-20, 20) / 1000), 1);
                $wc = round(min(70, $w['wc0'] + $w['wcs'] * $t / 30 + mt_rand(-5, 5) / 10), 1);
                $gor = round($w['gor'] * (1 + 0.0008 * $t) + mt_rand(-12, 12), 0);
                $gas = round($oil * $gor / 1_000_000, 3);
                $whp = round($w['whp'] * (1 - 0.05 * $t / 365) + mt_rand(-25, 25), 0);
                $temp = round($w['temp'] + mt_rand(-8, 8) / 10, 1);
                $last = ($i === $n - 1) || $date->diffInDays($this->today) < 33;

                $status = 'approved'; $vs = 'pass'; $sug = 'approve'; $score = mt_rand(90, 99); $summary = 'All validation rules passed.';
                if ($last && $wi % 4 === 1) { $status = 'submitted'; }
                if ($last && $wi % 4 === 2) { $status = 'draft'; }
                if ($wi === 5 && $i === 4) { $status = 'rejected'; $vs = 'fail'; $sug = 'reject'; $score = 41; $summary = 'Water cut jumped 9 points between consecutive tests.'; $wc = round($wc + 9, 1); }
                if ($wi === 9 && $i === 6) { $vs = 'warning'; $sug = 'review'; $score = 74; $summary = 'GOR is 31% above the well history.'; }

                $shift = ['morning', 'afternoon', 'night'][mt_rand(0, 2)];
                $mid = DB::table('mpfm_tests')->insertGetId([
                    'user_id' => $this->operators[array_rand($this->operators)], 'department_id' => 1,
                    'oil_field_id' => $w['field_id'], 'tower_id' => $w['tower_id'], 'oil_string_id' => $w['id'],
                    'test_date' => $date->toDateString(), 'shift' => $shift,
                    'oil_rate' => $oil, 'gas_rate' => $gas, 'water_cut' => $wc, 'pressure' => $whp, 'temperature' => $temp, 'gor' => $gor,
                    'bsw' => $wc, 'pressure_avg' => $whp, 'temperature_avg' => $temp,
                    'calculated_kpis' => json_encode(['gor' => $gor, 'bsw' => $wc, 'total_liquid_rate' => round($oil / max(0.05, 1 - $wc / 100), 1), 'gvf' => round(mt_rand(300, 900) / 10, 1)]),
                    'notes' => self::TAG . ' ' . ($status === 'rejected' ? 'Rejected by supervisor' : 'Routine well test'),
                    'status' => $status, 'validation_status' => $vs, 'validation_suggestion' => $sug, 'validation_score' => $score, 'validation_summary' => $summary,
                    'last_validated_at' => $date->copy()->addHours(8),
                    'department_supervisor_id' => in_array($status, ['approved', 'rejected']) ? $this->supervisor : null,
                    'surveillance_supervisor_id' => $status === 'approved' ? $this->surveillance : null,
                    'approved_by_department_at' => $status === 'approved' ? $date->copy()->addDay() : null,
                    'approved_by_surveillance_at' => $status === 'approved' ? $date->copy()->addDay()->addHours(3) : null,
                    'rejected_by_user_id' => $status === 'rejected' ? $this->supervisor : null, 'rejected_by_role' => $status === 'rejected' ? 'department_supervisor' : null,
                    'rejected_reason' => $status === 'rejected' ? 'Water cut out of range' : null, 'rejected_at' => $status === 'rejected' ? $date->copy()->addDay() : null,
                    'test_duration_minutes' => mt_rand(210, 300), 'test_started_at' => $date->copy()->setTime(6, 15), 'test_completed_at' => $date->copy()->setTime(10, 40),
                    'is_active' => 1, 'processing_status' => 'completed', 'created_at' => $date->copy()->setTime(11, 0), 'updated_at' => $date->copy()->addDay(),
                ]);

                // a separator test for about a third of the approved MPFM tests, same day, within a few percent
                if ($status === 'approved' && ($i + $wi) % 3 === 0) {
                    $dev = (($wi + $i) % 11 === 0) ? mt_rand(110, 160) / 1000 : mt_rand(-35, 35) / 1000;
                    DB::table('test_vessel_tests')->insert([
                        'test_vessel_id' => $vesselFor($w), 'user_id' => $this->operators[array_rand($this->operators)],
                        'oil_field_id' => $w['field_id'], 'tower_id' => $w['tower_id'], 'oil_string_id' => $w['id'], 'mpfm_test_id' => $mid,
                        'test_date' => $date->toDateString(), 'shift' => $shift,
                        'oil_rate' => round($oil / (1 + $dev), 1), 'gas_rate' => round($gas / (1 + $dev / 2), 3), 'water_cut' => round($wc * (1 + mt_rand(-30, 30) / 1000), 1),
                        'pressure' => round($whp * (1 + mt_rand(-15, 15) / 1000), 0), 'temperature' => round($temp - mt_rand(2, 9) / 10, 1), 'gor' => round($gor * (1 + mt_rand(-40, 40) / 1000), 0),
                        'notes' => self::TAG . ' Separator test, 4 h stabilised', 'status' => 'approved',
                        'reviewed_by_user_id' => $this->supervisor, 'review_notes' => 'Reviewed against MPFM', 'reviewed_at' => $date->copy()->addDay(),
                        'created_at' => $date->copy()->setTime(14, 0), 'updated_at' => $date->copy()->addDay(),
                    ]);
                }
            }
        }
    }

    private function vesselRequests(): void
    {
        $plan = [
            ['V-101', 'ZK-101', 'open', 5, 'morning', 'Re-test after choke change'], ['V-101', 'ZK-103', 'in_progress', 0, 'afternoon', 'Quarterly comparison with MPFM'],
            ['V-203', 'UM-102', 'open', 9, 'morning', 'Water cut verification'], ['V-118', 'BH-100', 'completed', -12, 'night', 'Baseline test after workover'],
            ['V-101', 'ZK-105', 'completed', -20, 'morning', 'Annual separator test'], ['V-203', 'UM-105', 'cancelled', -6, 'afternoon', 'Cancelled: well shut in for inspection'],
        ];
        foreach ($plan as [$vcode, $tcode, $status, $dayOff, $shift, $note]) {
            $tower = Tower::where('code', $tcode)->first(); $vessel = TestVessel::where('code', $vcode)->first();
            if (!$tower || !$vessel) { continue; }
            $rid = DB::table('tv_test_requests')->insertGetId([
                'test_vessel_id' => $vessel->id, 'tower_id' => $tower->id, 'oil_field_id' => $tower->oil_field_id, 'requested_by_user_id' => $this->supervisor,
                'test_date' => $this->today->copy()->addDays($dayOff)->toDateString(), 'shift' => $shift, 'notes' => self::TAG . ' ' . $note, 'status' => $status,
                'created_at' => $this->today->copy()->addDays($dayOff - 4), 'updated_at' => now(),
            ]);
            foreach ($tower->oilStrings()->orderBy('string_number')->get() as $w) {
                DB::table('tv_test_request_wells')->insert([
                    'tv_test_request_id' => $rid, 'oil_string_id' => $w->id,
                    'status' => $status === 'completed' ? 'approved' : ($status === 'in_progress' && $w->string_number === 1 ? 'in_progress' : 'pending'),
                    'started_by_user_id' => $status === 'completed' ? $this->operators[0] : null, 'started_at' => $status === 'completed' ? $this->today->copy()->addDays($dayOff) : null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    // ═════ Wire Line: operations and memory-gauge surveys ════════
    private function wireline(): void
    {
        $analyzer = app(GaugeRunAnalyzer::class);
        foreach ($this->wells as $wi => $w) {
            if ($wi % 7 === 6) { continue; } // a few wells stay unsurveyed so the overdue list is not empty
            $count = $wi % 3 === 0 ? 3 : 2;
            if ($wi > 17) { $count = 2; }
            for ($k = 0; $k < $count; $k++) {
                $date = Carbon::create(2026, 3, 4)->addDays($wi * 6 + $k * 118 + mt_rand(0, 9));
                if ($date->greaterThan($this->today->copy()->subDays(3))) { continue; }
                $bhp = round($w['bhp0'] - 24 * $k * (1 + ($wi % 5) / 5) + mt_rand(-18, 18), 1);
                $type = ($k === 1 && $wi % 2 === 0) ? 'static_gradient' : (($k === 2 && $wi % 3 === 0) ? 'bhfp' : 'bhcip');
                $op = WirelineOperation::create([
                    'oil_string_id' => $w['id'], 'operation_type' => 'memory_gauge', 'run_number' => $k + 1, 'operation_date' => $date->toDateString(), 'status' => 'completed',
                    'tool_string' => 'Rope socket + stem + Kuster memory gauge', 'depth_from' => 0, 'depth_to' => $depth = mt_rand(2650, 3250), 'crew_leader' => ['A. Hassan', 'M. Rahman', 'S. Khalid'][$wi % 3],
                    'objective' => $type === 'bhcip' ? 'Bottom-hole closed-in pressure and temperature' : ($type === 'bhfp' ? 'Flowing pressure for productivity index' : 'Static gradient survey with fluid typing'),
                    'remarks' => self::TAG . ' Survey completed without issues', 'created_by' => $this->supervisor,
                ]);
                $this->gaugeRun($analyzer, $op, $type, $bhp, $depth, $date, $wi, $k);
            }
        }

        // other interventions, to show the operation mix
        $mix = [
            [2, 'gauge_cutter', '2026-05-20', 'completed', 'Drift run to 2,980 m, no obstruction'], [3, 'tag_td', '2026-06-02', 'completed', 'Tagged TD at 3,012 m'],
            [4, 'plug_setting', '2026-07-11', 'completed', 'Set plug for annulus repair'], [6, 'fishing', '2026-08-08', 'completed', 'Recovered slickline tools, 2 runs'],
            [7, 'sampling', '2026-09-14', 'completed', 'Bottom-hole sample taken'], [8, 'perforating', '2026-10-02', 'completed', 'Added 6 m perforation interval'],
            [9, 'logging', '2026-10-21', 'completed', 'Production logging, water entry at 2,940 m'],
            [10, 'memory_gauge', '2026-11-30', 'planned', 'Scheduled static gradient survey'], [12, 'gauge_cutter', '2026-11-27', 'planned', 'Scheduled drift run'],
            [13, 'memory_gauge', '2026-11-25', 'in_progress', 'Gauge in hole, on-bottom stop running'],
        ];
        foreach ($mix as [$wi, $type, $d, $status, $note]) {
            if (!isset($this->wells[$wi])) { continue; }
            WirelineOperation::create(['oil_string_id' => $this->wells[$wi]['id'], 'operation_type' => $type, 'run_number' => 1, 'operation_date' => $d, 'status' => $status,
                'tool_string' => 'Standard slickline string', 'depth_to' => mt_rand(2500, 3200), 'crew_leader' => 'M. Rahman', 'objective' => $note, 'remarks' => self::TAG . ' ' . $note, 'created_by' => $this->supervisor]);
        }
    }

    private function gaugeRun(GaugeRunAnalyzer $analyzer, WirelineOperation $op, string $type, float $bhp, int $depth, Carbon $date, int $wi, int $k): void
    {
        $interval = 30;
        $bht = round(88 + ($wi % 9) * 1.6 + mt_rand(-5, 5) / 10, 2);
        $wellHasWater = ($wi % 4 === 1);
        $fracs = [0.30, 0.55, 0.80];
        $pAt = function (float $d) use ($bhp, $depth, $wellHasWater) {
            $wl = $depth * 0.82;
            $gO = 0.312 + 0.004 * sin($d); $gW = 0.436;
            if (!$wellHasWater || $d <= $wl) {
                $base = $wellHasWater ? $bhp - $gW * ($depth - $wl) * 3.28084 : $bhp - 0.0;
                return $wellHasWater ? $base - $gO * ($wl - $d) * 3.28084 : $bhp - $gO * ($depth - $d) * 3.28084;
            }
            return $bhp - $gW * ($depth - $d) * 3.28084;
        };
        $noise = fn ($a = .12) => mt_rand(-100, 100) / 100 * $a;
        $r = []; $t = 0;
        $add = function ($p, $temp) use (&$r, &$t, $interval, $noise) {
            $r[] = [$t, round($p + $noise(), 2), round($temp + $noise(.04), 2), round($temp + 1.2 + $noise(.04), 2)];
            $t += $interval;
        };
        $tempAt = fn ($d) => 27 + ($bht - 27) * ($d / $depth);
        for ($i = 0; $i < 14; $i++) { $add(14.7, 27.4); }
        $prevD = 0; $prevP = 14.7;
        $leg = function (float $d0, float $p0, float $d1, float $p1) use (&$add, $tempAt) {
            $steps = max(4, (int) round(abs($d1 - $d0) / 25 * 2));
            for ($s = 1; $s <= $steps; $s++) { $f = $s / $steps; $add($p0 + ($p1 - $p0) * $f, $tempAt($d0 + ($d1 - $d0) * $f)); }
        };
        $dwell = function (float $d, float $p) use (&$add, $tempAt) { for ($i = 0; $i < 10; $i++) { $add($p, $tempAt($d)); } };
        foreach ($fracs as $f) { $d = $depth * $f; $p = $pAt($d); $leg($prevD, $prevP, $d, $p); $dwell($d, $p); $prevD = $d; $prevP = $p; }
        $leg($prevD, $prevP, $depth, $bhp - 8);
        $bottom = 54;
        for ($i = 0; $i < $bottom; $i++) { $add($bhp - 8 * exp(-$i / 7) + ($type === 'bhfp' ? -0.2 * $i : 0.01 * $i), $bht); }
        $prevD = $depth; $prevP = $bhp;
        foreach (array_reverse($fracs) as $f) { $d = $depth * $f; $p = $pAt($d) - 0.4; $leg($prevD, $prevP, $d, $p); $dwell($d, $p); $prevD = $d; $prevP = $p; }
        $leg($prevD, $prevP, 0, 14.7);
        for ($i = 0; $i < 16; $i++) { $add(14.7, 27.6); }

        $maxRange = 10000.0;
        $analysis = $analyzer->analyze($r, $interval, $maxRange, (float) $depth);

        if ($type !== 'bhcip' && !empty($analysis['stops'])) {
            $rih = 0; $pooh = 0;
            foreach ($analysis['stops'] as &$s) {
                $s['depth'] = $s['direction'] === 'RIH' ? round($depth * ($fracs[$rih++] ?? 0.8)) : round($depth * (array_reverse($fracs)[$pooh++] ?? 0.3));
            }
            unset($s);
            $analysis['gradients'] = $analyzer->gradients($analysis['stops']);
        }

        $started = $date->copy()->setTime(7, 30 + $wi % 20);
        $run = WirelineGaugeRun::create([
            'wireline_operation_id' => $op->id, 'survey_type' => $type, 'gauge_serial' => 'KQ-' . (41200 + $wi * 3), 'gauge_model' => 'Quantum 15K', 'gauge_manufacturer' => 'Kuster',
            'calibration_date' => $wi === 3 ? $date->copy()->subMonths(15)->toDateString() : $date->copy()->subMonths(mt_rand(2, 8))->toDateString(),
            'max_range' => $maxRange, 'pressure_unit' => 'psig', 'temperature_unit' => 'degC', 'sample_interval_sec' => $interval,
            'started_at' => $started, 'ended_at' => $started->copy()->addSeconds($r[count($r) - 1][0]), 'sample_count' => count($r), 'source_file' => 'demo_' . $op->id . '.log', 'analysis' => $analysis,
        ]);
        $batch = [];
        foreach ($r as [$tt, $p, $tp, $it]) {
            $batch[] = ['wireline_gauge_run_id' => $run->id, 't_sec' => $tt, 'pressure' => $p, 'temperature' => $tp, 'internal_temp' => $it];
            if (count($batch) === 500) { DB::table('wireline_gauge_readings')->insert($batch); $batch = []; }
        }
        if ($batch) { DB::table('wireline_gauge_readings')->insert($batch); }
    }

    // ═════ Well Integrity ════════════════════════════════════════
    private function integrity(): void
    {
        // status plan per demo well index: green by default
        $amberScp = 10; $amberValve = 11; $amberCorrosion = 12; $red = 13; $amberOverdue = 14;
        $withChecks = array_values(array_filter(array_keys($this->wells), fn ($i) => $i % 4 !== 3));
        foreach ($this->wells as $wi => $w) {
            $maaspA = [1500, 1400, 1600, 1500][$wi % 4]; $maaspB = [1000, 900, 1100, 1000][$wi % 4];
            WellIntegrityProfile::create([
                'oil_string_id' => $w['id'], 'well_type' => 'producer', 'lift_method' => ['natural_flow', 'esp', 'gas_lift', 'natural_flow'][$wi % 4],
                'tubing_size' => '4-1/2" 12.6# L80', 'casing_size' => '9-5/8" 47# L80', 'mawhp' => 3500,
                'max_oil_rate' => $wi === $red ? 1800 : null, 'restriction_note' => $wi === $red ? 'Rate limited until annulus B is repaired' : null,
                'review_interval_days' => 365, 'last_review_date' => $this->today->copy()->subDays($wi === $amberOverdue ? 410 : mt_rand(40, 240)), 'reviewed_by' => 'Integrity engineer',
            ]);
            $annA = WellIntegrityAnnulus::create(['oil_string_id' => $w['id'], 'name' => 'A', 'maasp' => $maaspA]);
            $annB = WellIntegrityAnnulus::create(['oil_string_id' => $w['id'], 'name' => 'B', 'maasp' => $maaspB]);

            // monthly annulus readings, June to November
            foreach ([0, 1, 2, 3, 4, 5] as $m) {
                $d = Carbon::create(2026, 6, 12)->addMonths($m)->addDays($wi % 6);
                if ($d->greaterThan($this->today)) { continue; }
                $a = mt_rand(0, 80); $b = mt_rand(0, 40); $rebuild = null;
                if ($wi === $amberScp) { $a = (int) ($maaspA * (0.55 + 0.062 * $m)); $rebuild = $a - mt_rand(40, 90); }
                if ($wi === $red) { $b = (int) ($maaspB * (0.7 + 0.065 * $m)); }
                WellIntegrityAnnulusReading::create(['well_integrity_annulus_id' => $annA->id, 'oil_string_id' => $w['id'], 'reading_date' => $d,
                    'pressure' => $a, 'bleed_down_pressure' => $wi === $amberScp ? 0 : null, 'rebuild_pressure' => $wi === $amberScp ? $a - 30 : null, 'rebuild_hours' => $wi === $amberScp ? 24 : null,
                    'fluid' => $wi === $amberScp ? 'gas' : null, 'remarks' => null, 'created_by' => $this->supervisor]);
                WellIntegrityAnnulusReading::create(['well_integrity_annulus_id' => $annB->id, 'oil_string_id' => $w['id'], 'reading_date' => $d, 'pressure' => $b, 'created_by' => $this->supervisor]);
            }

            // barrier elements and their latest tests
            foreach (WellBarrierElement::TYPES as $type => [$label, $env, $interval]) {
                $el = WellBarrierElement::create(['oil_string_id' => $w['id'], 'envelope' => $env, 'element_type' => $type, 'name' => $label, 'test_interval_days' => $interval]);
                $age = $interval === 180 ? mt_rand(30, 120) : mt_rand(60, 250);
                $result = 'pass';
                if ($wi === $amberValve && $type === 'scssv') { $result = 'marginal'; }
                if ($wi === $amberValve && $type === 'master_valve') { $age = 230; }
                if ($wi === $red && $type === 'scssv') { $result = 'fail'; $age = 18; }
                if ($wi === $amberOverdue && in_array($type, ['scssv', 'annulus_valve'], true)) { $age = $interval + 45; }
                // one older passing test first so there is a history
                WellIntegrityTest::create(['well_barrier_element_id' => $el->id, 'oil_string_id' => $w['id'], 'test_date' => $this->today->copy()->subDays($age + $interval), 'test_type' => in_array($type, ['scssv', 'master_valve', 'wing_valve', 'annulus_valve'], true) ? 'function' : 'pressure', 'result' => 'pass', 'test_pressure' => 3500, 'duration_min' => 15, 'tested_by' => 'Integrity team', 'created_by' => $this->supervisor]);
                WellIntegrityTest::create(['well_barrier_element_id' => $el->id, 'oil_string_id' => $w['id'], 'test_date' => $this->today->copy()->subDays($age), 'test_type' => in_array($type, ['scssv', 'master_valve', 'wing_valve', 'annulus_valve'], true) ? 'function' : 'pressure',
                    'result' => $result, 'test_pressure' => 3500, 'leak_rate' => $result === 'fail' ? 2.8 : ($result === 'marginal' ? 0.6 : 0), 'duration_min' => 15, 'tested_by' => 'Integrity team',
                    'remarks' => $result === 'fail' ? 'SCSSV did not hold: leak rate above acceptance' : null, 'created_by' => $this->supervisor]);
                $el->syncFromTests();
            }

            // wellhead checks (operator reports), about 4 months, 14 of the wells
            if (in_array($wi, $withChecks, true)) {
                foreach ([0, 1, 2, 3] as $c) {
                    $d = $this->today->copy()->subDays(8 + $wi % 7 + 28 * (3 - $c));
                    $stiff = ($wi === $amberValve && $c === 3);
                    $chk = WellheadCheck::create(['oil_string_id' => $w['id'], 'check_date' => $d, 'wht' => $this->towerCode($w['tower_id']), 'well_no' => $w['name'], 'oem' => ['FMC', 'Cameron', 'Dril-Quip'][$wi % 3],
                        'string_no' => (string) (OilString::find($w['id'])->string_number ?? 1), 'well_status' => 'online', 'whcip' => mt_rand(1150, 2300), 'tree_cap' => 'ok',
                        'remarks' => $stiff ? 'UMV hard to turn, grease applied' : 'All valves operated, no leaks', 'checked_by' => 'Field operator', 'created_by' => $this->supervisor]);
                    foreach (['SV' => 18.5, 'UMV' => 18.5, 'LMV' => 18.5, 'WV' => 18.5] as $code => $turns) {
                        $isStiffUmv = $stiff && $code === 'UMV';
                        $chk->items()->create(['item_type' => 'valve', 'code' => $code, 'condition' => $isStiffUmv ? 'stiff' : 'ok', 'turns' => $isStiffUmv ? 26 : round($turns + mt_rand(-3, 3) / 10, 1),
                            'time_sec' => $isStiffUmv ? 9 : mt_rand(5, 6), 'greased' => $isStiffUmv || mt_rand(0, 3) === 0]);
                    }
                    $a = $wi === $amberScp ? (int) ($maaspA * (0.7 + 0.05 * $c)) : mt_rand(0, 60);
                    $b = $wi === $red ? (int) ($maaspB * (0.85 + 0.07 * $c)) : mt_rand(0, 30);
                    $chk->items()->create(['item_type' => 'annulus', 'code' => 'A', 'pressure' => $a, 'valve_ref' => 'ABCD', 'turns' => 13, 'dbb_side' => 'RHS', 'condition' => 'ok']);
                    $chk->items()->create(['item_type' => 'annulus', 'code' => 'B', 'pressure' => $b, 'valve_ref' => 'BD', 'turns' => 13, 'dbb_side' => 'LHS', 'condition' => 'ok']);
                }
            }

            // inspections
            if ($wi % 2 === 0 || in_array($wi, [$amberCorrosion, $red], true)) {
                $wall = $wi === $amberCorrosion ? 34.5 : ($wi === $red ? 18.0 : mt_rand(30, 130) / 10);
                WellIntegrityInspection::create(['oil_string_id' => $w['id'], 'inspection_type' => $wi % 4 === 0 ? 'caliper' : 'mtt', 'inspection_date' => $this->today->copy()->subDays(mt_rand(60, 220)), 'depth_from' => 20, 'depth_to' => 2900,
                    'max_wall_loss_pct' => $wall, 'status' => $wall >= 30 ? 'monitor' : 'acceptable', 'next_due_date' => $this->today->copy()->addDays($wi === $amberOverdue ? -20 : mt_rand(120, 380)),
                    'finding' => $wall >= 30 ? 'Localised corrosion at 1,480 to 1,520 m, wall loss ' . $wall . '%' : 'No significant wall loss', 'created_by' => $this->supervisor]);
            }
            if ($wi % 3 === 0) {
                WellIntegrityInspection::create(['oil_string_id' => $w['id'], 'inspection_type' => 'cbl', 'inspection_date' => $this->today->copy()->subDays(mt_rand(250, 700)), 'status' => 'acceptable', 'finding' => 'Good cement bond across the production casing shoe', 'created_by' => $this->supervisor]);
            }
        }

        // anomaly register
        $an = [
            [$amberScp, 'Sustained casing pressure on annulus A', 'scp', 4, 3, 'open', 'Run a bleed and build-up test, then a leak-path diagnostic', 22],
            [$amberValve, 'Upper master valve stiff to operate', 'valve_failure', 3, 3, 'mitigated', 'Greased and re-tested; replace at next shutdown', 40],
            [$amberCorrosion, 'Wall loss of 34% on the production casing', 'corrosion', 3, 4, 'open', 'Caliper repeat in 6 months; evaluate burst margin', 75],
            [$red, 'SCSSV failed its function test', 'valve_failure', 4, 5, 'open', 'Workover to replace SCSSV; rate limited to 1,800 B/D meanwhile', 12],
            [$red, 'Annulus B above MAASP', 'scp', 4, 4, 'open', 'Bleed to limit and investigate tubing leak', 9],
            [$amberOverdue, 'Barrier tests overdue', 'other', 2, 3, 'open', 'Schedule SCSSV and annulus valve tests', 30],
            [3, 'Wellhead flange weep', 'wellhead_leak', 2, 2, 'closed', 'Flange re-torqued and verified', 120],
            [6, 'Packer leak suspected', 'packer_leak', 3, 3, 'closed', 'Ruled out by pressure test', 150],
        ];
        foreach ($an as [$wi, $title, $cat, $l, $c, $status, $mit, $ago]) {
            if (!isset($this->wells[$wi])) { continue; }
            WellIntegrityAnomaly::create(['oil_string_id' => $this->wells[$wi]['id'], 'title' => $title, 'category' => $cat, 'likelihood' => $l, 'consequence' => $c, 'status' => $status,
                'identified_date' => $this->today->copy()->subDays($ago), 'due_date' => $status === 'closed' ? null : $this->today->copy()->addDays($status === 'mitigated' ? 45 : mt_rand(-5, 40)),
                'closed_date' => $status === 'closed' ? $this->today->copy()->subDays((int) ($ago / 2)) : null, 'description' => $title . '. Raised from the operator check and annulus monitoring.', 'mitigation' => $mit, 'created_by' => $this->supervisor]);
        }
    }

    private function towerCode(int $towerId): string
    {
        return Tower::where('id', $towerId)->value('code') ?? '';
    }
}
