<?php

namespace App\Console\Commands;

use App\Jobs\ImportUserJob;
use Illuminate\Console\Command;

class ImportUserCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:User';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users through file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Dispatches the ImportUserJob for processing.
        ImportUserJob::dispatch();
    }
}
