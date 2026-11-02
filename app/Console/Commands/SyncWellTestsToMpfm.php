<?php

namespace App\Console\Commands;

use App\Models\WellTest;
use App\Services\WellTestMpfmSync;
use Illuminate\Console\Command;

class SyncWellTestsToMpfm extends Command
{
    protected $signature = 'mpfm:sync-well-tests {--dry-run : only show what would be created}';

    protected $description = 'Create the missing MPFM test (draft) for archived well tests that have none';

    public function handle(WellTestMpfmSync $sync): int
    {
        $made = 0;
        foreach (WellTest::orderBy('id')->get() as $wt) {
            if (\App\Models\MPFMTest::where('well_test_id', $wt->id)->exists()) {
                continue;
            }
            if ($this->option('dry-run')) {
                $this->line("would create for well test #{$wt->id} ({$wt->well} / {$wt->wellhead_string})");
                continue;
            }
            $t = $sync->create($wt);
            if ($t) {
                $made++;
                $this->info("well test #{$wt->id} -> MPFM test #{$t->id}");
            } else {
                $this->warn("well test #{$wt->id} skipped (oil string '{$wt->wellhead_string}' not found)");
            }
        }
        $this->info("done, {$made} created");

        return self::SUCCESS;
    }
}
