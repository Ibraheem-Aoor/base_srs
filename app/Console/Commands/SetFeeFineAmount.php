<?php

namespace App\Console\Commands;

use App\Models\Fee;
use Illuminate\Console\Command;

class SetFeeFineAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fee:set-fine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily "Pending" Fees Fine Amount Set For Late Payments';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Fee::query()->whereNotIn('status', ['1', '2'])->chunkById(10, function ($fees) {
            foreach ($fees as $fee) {
                $fee->insertFineAmount();
            }
        });
        info('FEES FINE AMOUNT UPDATED SUCCESSFULLY');
        return Command::SUCCESS;
    }
}
