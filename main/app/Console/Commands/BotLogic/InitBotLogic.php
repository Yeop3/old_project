<?php

namespace App\Console\Commands\BotLogic;

use App\Services\BotLogic\CreateBotLogicFromConfigCommand;
use App\Services\BotLogic\RefreshBotLogic;
use Illuminate\Console\Command;
use Throwable;

/**
 * Class InitBotLogic.
 */
class InitBotLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot_logics:init {--update=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create standard bot logics';

    private RefreshBotLogic $refreshCommand;

    /**
     * Create a new command instance.
     *
     * @param RefreshBotLogic $refreshBotLogic
     */
    public function __construct(RefreshBotLogic $refreshBotLogic)
    {
        $this->refreshCommand = $refreshBotLogic;
        parent::__construct();
    }

    /**
     * @param CreateBotLogicFromConfigCommand $command
     *
     * @throws Throwable
     */
    public function handle(CreateBotLogicFromConfigCommand $command): void
    {
        $update = $this->option('update');

        if ($update) {
            $this->refreshCommand->execute($update);
            $this->info("$update bo logic update");

            return;
        }

//        $needLogics = ['standard'];
//
//        foreach ($needLogics as $logic) {
//            $command->execute($logic);
//        }

        $this->info('Standard bot logic created');
    }
}
