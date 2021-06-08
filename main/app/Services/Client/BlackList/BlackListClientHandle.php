<?php

namespace App\Services\Client\BlackList;

use App\Models\Client;

/**
 * Class BlackListClientHandle.
 */
class BlackListClientHandle
{
    /**
     * @param Client $client
     * @param bool   $act
     */
    public function execute(Client $client, bool $act): void
    {
        $client->in_black_list = $act;
        $client->save();
    }
}
