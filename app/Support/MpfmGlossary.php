<?php

namespace App\Support;

/**
 * Plain-language explanations for every number shown on the well test page:
 * calculated metrics, test details, meter/log metadata and the raw VX log columns.
 * Each entry is [title, explanation, unit].
 */
class MpfmGlossary
{
    /** Calculated metrics (WellTest::calculated_metrics) */
    public static function metric(string $key): array
    {
        $m = [
            'oil_rate' => ['Oil rate', 'Average oil production over the whole test, at standard (stock-tank) conditions. It is the average of the qo_sc column of the log.', 'STB/d = stock-tank barrels per day'],
            'water_rate' => ['Water rate', 'Average water production over the test at standard conditions (the qw_sc column).', 'STB/d'],
            'gas_rate' => ['Gas rate', 'Average gas production over the test at standard conditions (the qg_sc column).', 'MMScf/d = million standard cubic feet per day'],
            'gor' => ['Gas-Oil Ratio (GOR)', 'How much gas comes with each barrel of oil: gas rate ÷ oil rate. A higher GOR means a gassier well. This is the meter value averaged over the test.', 'Scf/STB'],
            'bsw' => ['Basic Sediment & Water (BSW)', 'Share of water (and solids) in the produced liquid. 0 % means dry oil; 50 % means half of the liquid is water.', '% of liquid volume'],
            'water_cut' => ['Water cut', 'Same as BSW: the percentage of the liquid that is water.', '%'],
            'wlr' => ['Water-Liquid Ratio (WLR)', 'Water volume divided by total liquid (oil + water) at line conditions. It matches BSW when there are no solids.', '%'],
            'gvf' => ['Gas Volume Fraction (GVF)', 'Share of the total flow that is gas, measured at line (pipe) conditions. Above about 90 % the meter is in a "wet gas" regime, where liquid rates are harder to measure accurately.', '%'],
            'avg_pressure' => ['Average pressure', 'Mean inlet pressure at the meter during the test.', 'pressure unit of the log'],
            'max_pressure' => ['Maximum pressure', 'Highest inlet pressure seen during the test.', 'pressure unit of the log'],
            'min_pressure' => ['Minimum pressure', 'Lowest inlet pressure seen during the test.', 'pressure unit of the log'],
            'pressure' => ['Pressure', 'Average line pressure of the test (alias of the average pressure).', ''],
            'avg_temperature' => ['Average temperature', 'Mean line temperature at the meter during the test.', 'temperature unit of the log'],
            'max_temperature' => ['Maximum temperature', 'Highest line temperature during the test.', ''],
            'min_temperature' => ['Minimum temperature', 'Lowest line temperature during the test.', ''],
            'temperature' => ['Temperature', 'Average line temperature of the test (alias of the average temperature).', ''],
            'cumulative_oil_volume' => ['Cumulative oil', 'Total oil produced from the start to the end of the test (the last value of the cumulative-volume column).', 'bbl = barrels'],
            'cumulative_water_volume' => ['Cumulative water', 'Total water produced during the test.', 'bbl'],
            'cumulative_gas_volume' => ['Cumulative gas', 'Total gas produced during the test.', 'Scf = standard cubic feet'],
            'h2s_avg' => ['H₂S average', 'Average hydrogen-sulfide concentration. H₂S is toxic and corrosive, so it is tracked for safety. "N/A" means the meter did not record it.', 'ppm = parts per million'],
            'h2s_max' => ['H₂S maximum', 'Highest hydrogen-sulfide reading during the test.', 'ppm'],
            'h2s_min' => ['H₂S minimum', 'Lowest hydrogen-sulfide reading during the test.', 'ppm'],
            'bo' => ['Oil formation volume factor (Bo)', 'How much the oil shrinks from line conditions to stock-tank conditions: volume at line conditions per unit volume at standard conditions. Used to convert line rates to standard rates.', 'Sm³/m³'],
            'bw' => ['Water formation volume factor (Bw)', 'Same conversion factor as Bo, for water.', 'Sm³/m³'],
            'bg' => ['Gas formation volume factor (Bg)', 'How much the gas expands from line conditions to standard conditions. Gas at high pressure takes far less room than at the surface, so Bg is large.', 'Sm³/m³'],
            'z_factor' => ['Gas compressibility factor (Z)', 'How far the gas behaves from an ideal gas at the test pressure and temperature. 1.0 would be an ideal gas; real reservoir gas is usually lower.', 'dimensionless'],
            'rst' => ['Solution GOR (Rs)', 'Gas that stays dissolved in the oil at the test conditions. Any gas beyond Rs is free gas.', 'Scf/STB'],
            'po' => ['Oil density (ρo)', 'Density of the oil at line conditions.', 'kg/m³'],
            'pw' => ['Water density (ρw)', 'Density of the water at line conditions.', 'kg/m³'],
            'pg' => ['Gas density (ρg)', 'Density of the gas at line conditions. It rises with pressure.', 'kg/m³'],
            'gor_formula' => ['GOR (recalculated)', 'GOR recomputed from the oil and gas rates with a formula. Compare it with the meter GOR as a cross-check: a big gap means one of the rates deserves a second look.', ''],
            'bsw_formula' => ['BSW (recalculated)', 'BSW recomputed from the water and oil rates with a formula, as a cross-check of the meter value.', '%'],
            'wlr_formula' => ['WLR (recalculated)', 'WLR recomputed from the water and oil rates with a formula, as a cross-check of the meter value.', '%'],
            'gvf_formula' => ['GVF (recalculated)', 'GVF recomputed from the phase rates with a formula, as a cross-check of the meter value.', '%'],
            'safety_issues' => ['Flagged readings', 'Readings the checker marked as outside the expected range (for example voltage, pressure or temperature limits). They are listed so you can judge whether the data can be trusted.', ''],
        ];

        return $m[$key] ?? [ucfirst(str_replace('_', ' ', $key)), 'A value calculated from the log for this test.', ''];
    }

    /** Test details & meter/log header fields */
    public static function field(string $key): array
    {
        $f = [
            'client' => ['Client', 'The company that owns the well or the service being delivered.', ''],
            'field' => ['Oil field', 'The field the well belongs to.', ''],
            'well' => ['Well', 'The well that was tested.', ''],
            'wellhead_tower' => ['Wellhead tower', 'The tower (wellhead platform) the well is connected to.', ''],
            'wellhead_string' => ['Wellhead string', 'The production string (tubing) that was flowed during the test.', ''],
            'choke_size' => ['Choke size', 'Opening of the choke (bean) that restricts the flow, in 64ths of an inch. 32 means 32/64 = ½ inch. A bigger choke lets more flow through.', '1/64 inch'],
            'upstream_choke' => ['Upstream choke', 'Choke setting on the well side of the flow path.', ''],
            'downstream_choke' => ['Downstream choke', 'Choke setting on the facility side of the flow path.', ''],
            'operator' => ['Operator', 'The person who ran the test and uploaded the log.', ''],
            'test_date' => ['Test date', 'The date the test belongs to (entered at upload).', ''],
            'test_type' => ['Test type', 'Flow: a steady flow measurement. Clean-up: flowing the well to clear drilling or completion fluids first. Build-up: shutting the well in to watch the pressure recover.', ''],
            'start_time' => ['Start time', 'Timestamp of the first reading in the log.', ''],
            'end_time' => ['End time', 'Timestamp of the last reading in the log.', ''],
            'duration' => ['Duration', 'Time between the first and the last reading.', ''],
            'filename' => ['Log file', 'The VX log file this test was built from.', ''],
            'rows' => ['Readings', 'Number of rows in the log. The meter writes one row per sampling interval.', ''],
            'columns' => ['Logged parameters', 'Number of columns in the log — every quantity the meter recorded.', ''],
            'created_at' => ['Uploaded', 'When the log was uploaded and saved to the archive.', ''],
            'Log File Version' => ['Log file version', 'Format version of the VX log. It tells the system how to read the columns.', ''],
            'Vx DAFC version' => ['Meter software version', 'Version of the software on the meter electronics (DAFC) that wrote the log.', ''],
            'File created:' => ['File created', 'When the meter created this log file.', ''],
            'DAFC Serial Number' => ['DAFC serial number', 'DAFC = Data Acquisition and Flow Computer, the electronics unit of the meter. This is its serial number.', ''],
            'DAFC HW Version' => ['DAFC hardware version', 'Hardware revision of the meter electronics.', ''],
            'DAFC SW Version' => ['DAFC software version', 'Software revision of the meter electronics.', ''],
            'DAFC FW Version' => ['DAFC firmware version', 'Firmware revision of the meter electronics.', ''],
            'GD Serial Number' => ['Gamma detector serial number', 'GD = the gamma-ray detector that measures how much radiation passes through the fluid. This is its serial number.', ''],
            'Configuration name' => ['Configuration name', 'Name of the meter configuration (fluid properties and calibration set) that was active for the test.', ''],
            'Mode' => ['Measurement mode', 'The operating mode the meter used. "Full Range Mode" covers the whole range from liquid to gas.', ''],
        ];

        return $f[$key] ?? [ucfirst(str_replace('_', ' ', $key)), 'A header value written by the meter into the log.', ''];
    }

    /** Raw VX log column, e.g. "qo_sc[STB/d]" */
    public static function column(string $name): array
    {
        $unit = '';
        $base = $name;
        if (preg_match('/^(.*?)\[(.*)\]$/u', $name, $mm)) {
            $base = $mm[1];
            $unit = $mm[2];
        }
        $unit = str_replace(['Â', 'Ã‚'], '', $unit);

        $win = ['32' => '32', '81' => '81', '356' => '356'];
        $title = $base;
        $text = null;

        $exact = [
            'Date & Time' => ['Date & time', 'Timestamp of the reading. The meter adds one row for every sampling interval.'],
            'NTotal' => ['Total gamma count rate', 'Gamma-ray count rate summed over all energy windows.'],
            'WLR' => ['Water-Liquid Ratio', 'Water volume ÷ (oil + water) at line conditions.'],
            'BSW' => ['Basic Sediment & Water', 'Share of water (and solids) in the liquid.'],
            'GVF' => ['Gas Volume Fraction', 'Share of the flow that is gas at line conditions.'],
            'GLR' => ['Gas-Liquid Ratio', 'Gas volume per liquid volume, both at line conditions.'],
            'GOR' => ['Gas-Oil Ratio', 'Standard gas volume per stock-tank barrel of oil.'],
            'GOR1' => ['Gas-Oil Ratio (alternative)', 'A second GOR calculation made by the meter, useful as a cross-check against GOR.'],
            'CGRAvg' => ['Average condensate-gas ratio', 'Average liquid (condensate) yield per unit of gas.'],
            'bo' => ['Oil formation volume factor', 'Shrinkage of oil from line to standard conditions.'],
            'bw' => ['Water formation volume factor', 'Volume change of water from line to standard conditions.'],
            'bg' => ['Gas formation volume factor', 'Expansion of gas from line to standard conditions.'],
            'Z' => ['Gas compressibility factor', 'Deviation of the gas from ideal-gas behaviour (1.0 = ideal).'],
            'Rst' => ['Solution GOR', 'Gas dissolved in the oil at line conditions.'],
            'Rwst' => ['Water solution ratio', 'Meter PVT parameter for gas dissolved in the water phase.'],
            'rgmp' => ['Meter PVT parameter', 'An internal fluid-property value the meter uses for its phase calculations.'],
            'Dyn_DP' => ['Dynamic differential pressure', 'Pressure drop across the venturi after dynamic correction. The venturi differential pressure drives the total-flow calculation.'],
            'DPV' => ['Venturi differential pressure', 'Pressure difference between the venturi inlet and its throat. The faster the flow, the larger it is.'],
            'PIN' => ['Inlet pressure', 'Line pressure at the meter inlet.'],
            'TL' => ['Line temperature', 'Fluid temperature in the line.'],
            'Pthroat' => ['Throat pressure', 'Pressure at the narrowest point (throat) of the venturi.'],
            'DAFC_InputVolt' => ['Supply voltage', 'Voltage feeding the meter electronics. Values outside the normal band can disturb readings.'],
            'DAFC_InputCurr' => ['Supply current', 'Current drawn by the meter electronics.'],
            'DAFC_5Volt' => ['5 V internal rail', 'Internal 5-volt supply of the electronics; it should stay close to 5 V.'],
            'DAFC_24Volt' => ['24 V internal rail', 'Internal 24-volt supply of the electronics; it should stay close to 24 V.'],
            'DAFC_Temp' => ['Electronics temperature', 'Temperature inside the meter electronics enclosure.'],
            'DAFC_Hum' => ['Electronics humidity', 'Relative humidity inside the electronics enclosure. High values can point to moisture ingress.'],
            'HVGain' => ['Detector high-voltage gain', 'High voltage applied to the gamma detector. The meter adjusts it to keep the detector stable, so large swings are worth checking.'],
            'DetectorTemp' => ['Detector temperature', 'Temperature of the gamma detector. Detector response drifts with temperature, so the meter tracks it.'],
            'm_lc' => ['Total mass rate (line)', 'Total mass flow of all phases at line conditions.'],
            'EVOChiSquare' => ['Spectrum fit quality (χ²)', 'How well the EVO spectral model fits the measured gamma spectrum. Lower is better.'],
            'EVOPHR' => ['Peak-height ratio', 'Spectral peak-height ratio used as a quality indicator of the EVO analysis.'],
            'EVOHECenter' => ['High-energy peak centre', 'Position of the high-energy peak in the spectrum; it shows whether the detector is correctly stabilised.'],
            'cmt_lc' => ['Cumulative total mass', 'Total mass of all phases produced since the start of the test.'],
        ];

        if (isset($exact[$base])) {
            [$title, $text] = $exact[$base];
        } elseif (preg_match('/^N(32|81|356)$/', $base, $x)) {
            $title = 'Gamma count rate, '.$x[1].' keV window';
            $text = 'Counts per second in the '.$x[1].' keV energy window of the gamma detector. Oil, water and gas absorb the gamma rays differently in each window, which is how the meter works out the phase fractions.';
        } elseif (preg_match('/^q([owg])_(lc|sc|scnp)$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas'][$x[1]];
            $title = $ph.' flow rate ('.['lc' => 'line conditions', 'sc' => 'standard conditions', 'scnp' => 'standard, alternative'][$x[2]].')';
            $text = ['lc' => 'Actual flow of '.strtolower($ph).' through the pipe at the real pressure and temperature.',
                'sc' => 'Flow of '.strtolower($ph).' converted to standard (stock-tank) conditions — the figure used for reporting.',
                'scnp' => 'Standard-condition '.strtolower($ph).' flow from an alternative (non-PVT-corrected, "np") calculation, kept for reference.'][$x[2]];
        } elseif (preg_match('/^F([owg])_lc$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas'][$x[1]];
            $title = $ph.' fraction';
            $text = 'Share of the flowing volume that is '.strtolower($ph).' at line conditions. The three fractions add up to 100 %.';
        } elseif (preg_match('/^m([owg])_(lc|sc)$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas'][$x[1]];
            $title = $ph.' mass rate ('.($x[2] === 'lc' ? 'line' : 'standard').')';
            $text = 'Mass of '.strtolower($ph).' flowing per day, at '.($x[2] === 'lc' ? 'line' : 'standard').' conditions.';
        } elseif (preg_match('/^D([owgml])_lc$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas', 'm' => 'Mixture', 'l' => 'Liquid (oil + water)'][$x[1]];
            $title = $ph.' density';
            $text = 'Density of the '.strtolower($ph).' at line conditions.';
        } elseif ($base === 'Mu_o_lc') {
            $title = 'Oil viscosity';
            $text = 'How thick the oil is at line conditions. Higher centipoise (cP) means thicker oil.';
        } elseif ($base === 'Mu_l_lc') {
            $title = 'Liquid viscosity';
            $text = 'Viscosity of the combined liquid (oil + water) at line conditions.';
        } elseif (preg_match('/^cv([owg])_(lc|sc|scnp)$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas'][$x[1]];
            $title = 'Cumulative '.strtolower($ph).' volume ('.['lc' => 'line', 'sc' => 'standard', 'scnp' => 'standard, alt.'][$x[2]].')';
            $text = 'Total '.strtolower($ph).' volume produced since the start of the test. It only ever grows during the test.';
        } elseif (preg_match('/^cm([owg])_lc$/', $base, $x)) {
            $ph = ['o' => 'Oil', 'w' => 'Water', 'g' => 'Gas'][$x[1]];
            $title = 'Cumulative '.strtolower($ph).' mass';
            $text = 'Total mass of '.strtolower($ph).' produced since the start of the test.';
        } elseif (preg_match('/^(Operating|Oil|Water|Gas)Point(LE|HE)$/', $base, $x)) {
            $what = ['Operating' => 'measured', 'Oil' => 'pure-oil reference', 'Water' => 'pure-water reference', 'Gas' => 'pure-gas reference'][$x[1]];
            $en = $x[2] === 'LE' ? 'low' : 'high';
            $title = ucfirst($what).' attenuation, '.$en.' energy';
            $text = 'Gamma-ray attenuation at '.$en.' energy. The meter places the '.($x[1] === 'Operating' ? 'measured operating point' : 'calibration point of '.explode('-', $what)[0]).' on a map between pure oil, water and gas to find the phase fractions.';
        } elseif (preg_match('/^EVODeconvN(32|81|356)$/', $base, $x)) {
            $title = 'Corrected count rate, '.$x[1].' keV';
            $text = 'Count rate in the '.$x[1].' keV window after the EVO spectral processing removes overlap between energy windows.';
        } elseif (str_starts_with($base, 'EVOLP_')) {
            $title = 'Spectral fit parameter ('.str_replace('_', '–', substr($base, 6)).')';
            $text = 'Internal parameter of the EVO spectral fit between two energy bands (VHE = very high, HE = high, LE = low energy). It is meter diagnostics rather than a production number.';
        }

        if ($text === null) {
            $title = $base;
            $text = 'A value recorded by the meter in the VX log'.($unit !== '' ? ' (unit '.$unit.')' : '').'.';
        }
        if ($unit !== '' && stripos($title, $unit) === false) {
            $text .= '';
        }

        return [$title, $text, $unit];
    }
}
