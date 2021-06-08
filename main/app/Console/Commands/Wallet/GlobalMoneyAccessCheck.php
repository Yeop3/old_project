<?php

namespace App\Console\Commands\Wallet;

use App\Models\Wallet\GlobalMoneyWallet;
use App\Services\Wallet\GlobalMoney\GlobalMoneyApi;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Illuminate\Console\Command;

/**
 * Class GlobalMoneyAccessCheck.
 */
class GlobalMoneyAccessCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'global_money:access:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param GlobalMoneyApi $api
     *
     * @return void
     */
    public function handle(GlobalMoneyApi $api): void
    {
        $wallets = GlobalMoneyWallet::whereWrongCredentials(1)->get();

        foreach ($wallets as $wallet) {
            $api->setLoginData(new GlobalMoneyLoginData($wallet->login, $wallet->password, $wallet->type));
            $api->login();
            $wallet->wrong_credentials = 0;
            $wallet->save();
        }
    }
}
