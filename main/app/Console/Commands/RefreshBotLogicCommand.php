<?php

namespace App\Console\Commands;

use App\Services\BotLogic\RefreshBotLogic;
use Illuminate\Console\Command;
use Throwable;

/**
 * Class RefreshBotLogicCommand.
 */
class RefreshBotLogicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot-logic:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param RefreshBotLogic $command
     *
     * @throws Throwable
     *
     * @return int
     */
    public function handle(RefreshBotLogic $command): int
    {
        $command->execute();

        $this->info('Standard bot logic refresh');

        return 0;
    }
}
