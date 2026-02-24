<?php

namespace App\Console\Commands;

use App\Models\MPFMTest;
use App\Services\MPFMTestService;
use Illuminate\Console\Command;

class RecalculateMPFMKPIs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpfm:recalculate-kpis {test_id? : The ID of the test to recalculate (optional, all if not provided)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Recalculate and update KPIs for MPFM tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new MPFMTestService();
        $testId = $this->argument('test_id');

        if ($testId) {
            $test = MPFMTest::find($testId);
            if (!$test) {
                $this->error("❌ MPFM Test with ID {$testId} not found");
                return 1;
            }

            $this->recalculateAndUpdate($test, $service);
            $this->info("✅ KPIs recalculated for test {$testId}");
            return 0;
        }

        // Recalculate for all tests
        $tests = MPFMTest::all();

        if ($tests->isEmpty()) {
            $this->warn("⚠️ No MPFM tests found");
            return 0;
        }

        $bar = $this->output->createProgressBar($tests->count());
        $bar->start();

        foreach ($tests as $test) {
            $this->recalculateAndUpdate($test, $service);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Recalculated KPIs for {$tests->count()} tests");
        return 0;
    }

    /**
     * Recalculate and update KPIs for a single test
     */
    private function recalculateAndUpdate(MPFMTest $test, MPFMTestService $service)
    {
        try {
            $kpis = $service->calculateKPIs($test);
            $test->update(['calculated_kpis' => $kpis]);
        } catch (\Exception $e) {
            $this->error("Error processing test {$test->id}: " . $e->getMessage());
        }
    }
}
