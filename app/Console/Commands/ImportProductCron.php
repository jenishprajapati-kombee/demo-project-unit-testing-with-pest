<?php

namespace App\Console\Commands;

use App\Jobs\ImportProductJob;
use Illuminate\Console\Command;

class ImportProductCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:Product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products through file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Dispatches the ImportProductJob for processing.
        ImportProductJob::dispatch();
    }
}
