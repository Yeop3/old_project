<?php

namespace App\Console\Commands\Client;

use App\Services\Client\UnbanedClientCommand;
use Illuminate\Console\Command;

/**
 * Class UnbanedClient.
 */
class UnbanedClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:unban';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param UnbanedClientCommand $command
     *
     * @return int
     */
    public function handle(UnbanedClientCommand $command): int
    {
        $command->execute();

        return 0;
    }
}
