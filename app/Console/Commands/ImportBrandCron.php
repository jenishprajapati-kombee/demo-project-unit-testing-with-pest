<?php

namespace App\Console\Commands;

use App\Jobs\ImportBrandJob;
use Illuminate\Console\Command;

class ImportBrandCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:Brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import brands through file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Dispatches the ImportBrandJob for processing.
        ImportBrandJob::dispatch();
    }
}
