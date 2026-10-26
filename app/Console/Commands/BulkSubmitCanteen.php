<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Livewire\Canteen\Index;

class BulkSubmitCanteen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulksubmit:canteen';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Bulk Submit Canteen';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Log::info('Submit Canteen started.');
        $index = new Index();
        $index->submit();
        Log::info('Submit Canteen completed.');
    }
}
