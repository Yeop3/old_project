<?php

namespace App\Console\Commands;

use App\Services\Order\GetCryptoPricingCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Class CryptoCache.
 */
class CryptoCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param GetCryptoPricingCommand $command
     *
     * @return void
     */
    public function handle(GetCryptoPricingCommand $command): void
    {
        Cache::forever('crypto_price', $command->execute());
    }
}
