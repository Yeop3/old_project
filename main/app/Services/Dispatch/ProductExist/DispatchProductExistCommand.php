<?php

namespace App\Services\Dispatch\ProductExist;

use App\Bot\TextGenerators\DistributeTextGenerator;
use App\Models\Bot;
use App\Models\User;

/**
 * Class DispatchProductExistCommand.
 */
class DispatchProductExistCommand
{
    private DistributeTextGenerator $distributeTextGenerator;

    /**
     * DispatchProductExistCommand constructor.
     *
     * @param DistributeTextGenerator $distributeTextGenerator
     */
    public function __construct(DistributeTextGenerator $distributeTextGenerator)
    {
        $this->distributeTextGenerator = $distributeTextGenerator;
    }

    public function execute(int $botId): string
    {
        /* @var User $user */
        $user = auth()->user();
        $bot = Bot::whereSellerId($user->seller_id)->where('number', $botId)->firstOrFail();
        $botLogic = $bot->logic;

        return $this->distributeTextGenerator->getProductExistText($botLogic);
    }
}
