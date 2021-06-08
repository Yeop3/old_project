<?php

namespace App\Console\Commands\Order;

use App\Services\Order\CheckOrdersTimeoutCommand;
use Illuminate\Console\Command;

/**
 * Class CheckOrderTimeout.
 */
class CheckOrderTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check:timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param CheckOrdersTimeoutCommand $command
     */
    public function handle(CheckOrdersTimeoutCommand $command): void
    {
        $command->execute();
    }
}
