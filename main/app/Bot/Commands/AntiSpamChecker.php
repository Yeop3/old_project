<?php

namespace App\Bot\Commands;

use App\Models\Bot;
use App\Models\BotLogic\BotLogicAntispam;
use App\Models\Client;
use App\Services\Order\CancelOrderCommand;
use App\Services\Order\Exceptions\CantCancelOrderException;
use App\Services\Order\VO\CancelInitiator;
use App\VO\SourceType;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class AntiSpamChecker.
 */
final class AntiSpamChecker
{
    private CancelOrderCommand $cancelOrderCommand;

    public function __construct(CancelOrderCommand $cancelOrderCommand)
    {
        $this->cancelOrderCommand = $cancelOrderCommand;
    }

    /**
     * @param Client $client
     *
     * @throws Exception
     */
    public function handleFrequentMessages(Client $client): void
    {
        if ($client->source_type !== SourceType::TYPE_BOT) {
            throw new RuntimeException('implemented only bot client source');
        }

        /** @var Bot $botModel */
        $botModel = Bot::where('id', $client->source_id)->first();

        $bot = new \App\Bot\Bot($botModel);

        $antiSpam = BotLogicAntispam::whereBotLogicId($botModel->logic_id)
            ->where('key', 'frequent_requests')
            ->first();

        if (!$antiSpam) {
            return;
        }

        $cacheCount = cache()->remember("client_{$client->id}_{$botModel->id}_frequent_requests", 3600, fn () => 1);
        $options = collect($antiSpam->options);

        if ($this->isBanned($options, $cacheCount, $bot, $client, $botModel)) {
            return;
        }

        $optionSendNotice = $options->first(fn ($value, $key) => $value['key'] === 'send_notice');
        if ($optionSendNotice['value']) {
            $notice = explode(',', $optionSendNotice['value']);
            $optionLimit = $options->first(fn ($value, $key) => $value['key'] === 'limit');
            if (in_array($cacheCount, $notice, true)) {
                $optionText = $options->first(fn ($value, $key) => $value['key'] === 'notice_text');

                $bot->say(
                    str_replace(
                        ['{antispam-limit-requests}', '{client-left-requests}'],
                        [$optionLimit['value'], $optionLimit['value'] - $cacheCount],
                        $optionText['value']
                    ),
                    [$client->telegram_id],
                    $botModel->getBotDriver()
                );
            }
        }

        cache()->increment("client_{$client->id}_{$botModel->id}_frequent_requests");
    }

    /**
     * @param Collection   $options
     * @param int          $cacheCount
     * @param \App\Bot\Bot $bot
     * @param Client       $client
     * @param Bot          $botModel
     *
     * @throws Exception
     *
     * @return bool
     */
    private function isBanned(
        Collection $options,
        int $cacheCount,
        \App\Bot\Bot $bot,
        Client $client,
        Bot $botModel
    ): bool {
        $optionLimit = $options->first(fn ($value, $key) => $value['key'] === 'limit');

        if ($optionLimit['value'] === 0) {
            return true;
        }

        if ($optionLimit['value'] <= $cacheCount) {
            $optionTextBan = $options->first(
                fn ($value, $key) => $value['key'] === 'ban_text'
            );
            $optionBanDuration = $options->first(
                fn ($value, $key) => $value['key'] === 'ban_duration'
            );

            $bot->say(
                str_replace(
                    ['{ban-duration}'],
                    [
                        $optionBanDuration['value'],
                    ],
                    $optionTextBan['value']
                ),
                [$client->telegram_id],
                $botModel->getBotDriver()
            );

            $client->ban_expires_at = now()->addMinutes($optionBanDuration['value']);
            $client->save();
            $order = $client->order;
            if ($order) {
                try {
                    $this->cancelOrderCommand->execute($order, CancelInitiator::system(), true);
                } catch (CantCancelOrderException $e) {
                }
            }

            $this->cacheClear($client, $botModel);

            return true;
        }

        return false;
    }

    /**
     * @param Client $client
     * @param Bot    $botModel
     *
     * @throws Exception
     */
    public function cacheClear(Client $client, Bot $botModel): void
    {
        cache(["client_{$client->id}_{$botModel->id}_frequent_orders_without_payment" => 1]);
        cache(["client_{$client->id}_{$botModel->id}_frequent_requests" => 1]);
        cache(["client_{$client->id}_{$botModel->id}_wrong_codes" => 1]);
    }

    /**
     * @param Client $client
     *
     * @throws Exception
     */
    public function handelFrequentCanceledOrders(Client $client): void
    {
        if ($client->source_type !== SourceType::TYPE_BOT) {
            throw new RuntimeException('implemented only bot client source');
        }

        /** @var Bot $botModel */
        $botModel = Bot::where('id', $client->source_id)->first();

        $bot = new \App\Bot\Bot($botModel);

        $antiSpam = BotLogicAntispam::whereBotLogicId($botModel->logic_id)
            ->where('key', 'frequent_orders_without_payment')
            ->first();

        if (!$antiSpam) {
            return;
        }

        $cacheCount = cache()->remember(
            "client_{$client->id}_{$botModel->id}_frequent_orders_without_payment",
            3600,
            fn () => 1
        );

        $options = collect($antiSpam->options);

        if ($this->isBanned($options, $cacheCount, $bot, $client, $botModel)) {
            return;
        }

        $optionSendNotice = $options->first(fn ($value, $key) => $value['key'] === 'send_notice');
        if ($optionSendNotice['value']) {
            $optionText = $options->first(fn ($value, $key) => $value['key'] === 'notice_text');
            $optionLimit = $options->first(fn ($value, $key) => $value['key'] === 'limit');
            $bot->say(
                str_replace(
                    ['{antispam-limit-cancels}', '{client-left-cancels}'],
                    [$optionLimit['value'], $optionLimit['value'] - $cacheCount],
                    $optionText['value']
                ),
                [$client->telegram_id],
                $botModel->getBotDriver()
            );
        }

        cache()->increment("client_{$client->id}_{$botModel->id}_frequent_orders_without_payment");
    }

    /**
     * @param Client $client
     *
     * @throws Exception
     */
    public function handleFrequentWrongCodes(Client $client): void
    {
        if ($client->source_type !== SourceType::TYPE_BOT) {
            throw new RuntimeException('implemented only bot client source');
        }

        /** @var Bot $botModel */
        $botModel = Bot::where('id', $client->source_id)->first();

        $bot = new \App\Bot\Bot($botModel);

        $antiSpam = BotLogicAntispam::whereBotLogicId($botModel->logic_id)
            ->where('key', 'wrong_payment_codes')
            ->first();

        $cacheCount = cache()->remember(
            "client_{$client->id}_{$botModel->id}_wrong_codes",
            3600,
            fn () => 1
        );

        if (!$antiSpam) {
            return;
        }

        $options = collect($antiSpam->options);

        if ($this->isBanned($options, $cacheCount, $bot, $client, $botModel)) {
            return;
        }

        $optionSendNotice = $options->first(fn ($value, $key) => $value['key'] === 'send_notice');

        if ($optionSendNotice['value']) {
            $optionText = $options->first(fn ($value, $key) => $value['key'] === 'notice_text');
            $optionLimit = $options->first(fn ($value, $key) => $value['key'] === 'limit');
            $bot->say(
                str_replace(
                    ['{client-left-paycoupons}'],
                    [$optionLimit['value'] - $cacheCount],
                    $optionText['value']
                ),
                [$client->telegram_id],
                $botModel->getBotDriver()
            );
        }

        cache()->increment("client_{$client->id}_{$botModel->id}_wrong_codes");
    }
}
