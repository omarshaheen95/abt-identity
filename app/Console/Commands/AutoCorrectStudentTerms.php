<?php

namespace App\Console\Commands;

use App\Models\StudentTerm;
use App\Models\Subject;
use App\Services\CorrectionService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCorrectStudentTerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student-terms:auto-correct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-correct all uncorrected student terms that don\'t have article questions';

    /**
     * @var CorrectionService
     */
    protected $correctionService;

    /**
     * Create a new command instance.
     *
     * @param CorrectionService $correctionService
     * @return void
     */
    public function __construct(CorrectionService $correctionService)
    {
        parent::__construct();

        $this->correctionService = $correctionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting auto-correction of student terms...');

        $studentTerms = StudentTerm::query()
            ->where('corrected', false)
            ->get();

        $count = $studentTerms->count();
        $this->info("Found {$count} uncorrected student terms to process.");

        if ($count == 0) {
            $this->info('No uncorrected student terms to process. Exiting.');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $successCount = 0;
        $failCount = 0;


        foreach ($studentTerms as $studentTerm) {
            try {
                DB::transaction(function() use (&$failCount, $studentTerm, &$successCount) {
                    $correctionData = $this->correctionService->correctStudentTerm($studentTerm);

                    if (!isset($correctionData['error'])) {
                        $studentTerm->update($correctionData);
                        $successCount++;
                    } else {
                        Log::error('Failed to auto-correct term in scheduled job', [
                            'student_term_id' => $studentTerm->id,
                            'error' => $correctionData['message']
                        ]);
                        $failCount++;
                    }
                });
            } catch (\Exception $e) {
                Log::error('Exception during scheduled auto-correction', [
                    'student_term_id' => $studentTerm->id,
                    'error' => $e->getMessage()
                ]);
                $failCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Auto-correction completed: {$successCount} terms corrected successfully, {$failCount} failed.");

        Log::info('Auto-correction job completed', [
            'total' => $count,
            'success' => $successCount,
            'failed' => $failCount
        ]);

        return 0;
    }
}
