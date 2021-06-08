<?php

declare(strict_types=1);

namespace App\Services\TelegramClient;

use danog\MadelineProto\API;
use danog\MadelineProto\messages;
use danog\MadelineProto\updates;
use Generator;
use Hu\MadelineProto\Facades\MadelineProto;

/**
 * Class TelegramClient.
 */
class TelegramClient
{
    /**
     * @return API
     */
    public function client(): \danog\MadelineProto\API
    {
        return MadelineProto::getClient();
    }

    public function getLastMessages(string $username, int $count = 1): array
    {
        return $this->client()->messages->getHistory([
            'peer'        => $username,
            'offset_id'   => 0,
            'offset_date' => 0,
            'add_offset'  => 0,
            'limit'       => $count,
            'max_id'      => 9999999,
            'min_id'      => 0,
        ])['messages'];
    }

    /**
     * @param string $username
     * @param string $message
     *
     * @return updates
     */
    public function sendMessage(string $username, string $message): \danog\MadelineProto\updates
    {
        return $this->client()->messages->sendMessage([
            'peer'    => $username,
            'message' => $message,
        ]);
    }

    /**
     * @param array $ids
     *
     * @return messages
     */
    public function deleteMessages(array $ids): \danog\MadelineProto\messages
    {
        return $this->client()->messages->deleteMessages([
            'revoke' => true,
            'id'     => $ids,
        ]);
    }

    /**
     * @param string $username
     *
     * @return messages
     */
    public function deleteHistory(string $username): \danog\MadelineProto\messages
    {
        return $this->client()->messages->deleteHistory([
            'peer'   => $username,
            'revoke' => true,
            'max_id' => 9999999,
        ]);
    }

    /**
     * @param string $username
     *
     * @return Generator
     */
    public function getInfo(string $username): \Generator
    {
        return $this->client()->getInfo($username);
    }
}
