<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class Init.
 */
class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init {--reset=false} {--seed=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): ?int
    {
        $reset = $this->option('reset') === 'true';
        $seed = $this->option('seed') === 'true';

        if ($reset) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        $this->call('bot_logics:init');

        $this->call('main_bot:create');

        if ($seed) {
            $this->call('db:seed');
        }

        if (!file_exists(storage_path('app/public/product_images'))) {
            File::makeDirectory(storage_path('app/public/product_images'));
        }

        if (!file_exists(storage_path('app/public/product_videos'))) {
            File::makeDirectory(storage_path('app/public/product_videos'));
        }

        return 0;
    }
}
