<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait SellerIncrementId.
 */
trait SellerIncrementId
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function bootSellerIncrementId(): void
    {
        static::creating(function (Model $model) {
            if (!$model->randomNumberGeneration) {
                $lastNumber = $model->where('seller_id', $model->seller_id)->max('number') ?? 0;
                $model->number = $lastNumber + 1;

                return;
            }

            $model->number = self::generateNumber($model);
        });
    }

    public function scopeOfSeller(Builder $builder, User $user, ?int $sellerId = null): void
    {
        if ($user->role === User::ROLE_SELLER) {
            $builder->where('seller_id', $user->seller_id);

            return;
        }

        if ($user->role !== User::ROLE_ADMIN) {
            return;
        }

        if ($sellerId) {
            $builder->with('seller')->where('seller_id', $sellerId);
        }
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    private static function generateNumber(Model $model): int
    {
        $randomInt = random_int(10000000, 99999999);

        $duplicateExist = $model->where('seller_id', $model->seller_id)
            ->where('number', $randomInt)
            ->exists();

        if ($duplicateExist) {
            return static::generateNumber($model);
        }

        return $randomInt;
    }
}
