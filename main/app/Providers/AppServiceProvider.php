<?php

namespace App\Providers;

use App\Models\Bot;
use App\Models\Site;
use App\Services\Bot\BotMessageLogger\BotMessageLogger;
use App\Services\Bot\BotMessageLogger\DatabaseBotMessageLogger;
use App\Services\Client\ClientResolver;
use App\Services\Coordinates\Parsers\CoordinatesParser;
use App\Services\Coordinates\Parsers\MixedCoordinatesParser;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    public array $singletons = [
        ClientResolver::class,
    ];

    public array $bindings = [
        BotMessageLogger::class  => DatabaseBotMessageLogger::class,
        CoordinatesParser::class => MixedCoordinatesParser::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Relation::morphMap([
            'site' => Site::class,
            'bot'  => Bot::class,
        ] + WalletType::getModelsMorphArray());
    }
}
