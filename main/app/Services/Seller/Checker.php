<?php

declare(strict_types=1);

namespace App\Services\Seller;

use App\Models\Seller;
use App\Services\Seller\Exception\DomainDuplicateException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkDomain(string $domain, ?int $exceptId = null): void
    {
        $domainExists = Seller::whereDomain($domain)
            ->when($exceptId, fn (Builder $query) => $query->where('id', '!=', $exceptId))
            ->exists();

        if ($domainExists) {
            throw new DomainDuplicateException("Domain $domain already exists");
        }
    }
}
