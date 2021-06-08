<?php

declare(strict_types=1);

namespace App\Services\Proxy\Delete;

use App\Models\Proxy;

/**
 * Class DeleteProxyCommand.
 */
class DeleteProxyCommand
{
    public function execute(Proxy $proxy): void
    {
        $proxy->delete();
    }
}
