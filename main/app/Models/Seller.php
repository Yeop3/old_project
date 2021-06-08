<?php

namespace App\Models;

use App\Models\Wallet\CryptoWallet;
use App\Models\Wallet\EasyPayWallet;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Models\Wallet\KunaAccount;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Seller\Settings\InitSellerSettingsCommand;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\Services\Wallet\VO\CryptoWalletResolvingType;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Seller.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $domain
 * @property int         $banned
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Bot[] $bots
 * @property-read int|null $bots_count
 * @property-read Collection|Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read \Kalnoy\Nestedset\Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @property-read Collection|Operator[] $operators
 * @property-read int|null $operators_count
 * @property-read Collection|ProductType[] $productTypes
 * @property-read int|null $product_types_count
 * @property-read Collection|SellerSetting[] $settings
 * @property-read int|null $settings_count
 * @property-read Collection|Shift[] $shifts
 * @property-read int|null $shifts_count
 *
 * @method static Builder|Seller newModelQuery()
 * @method static Builder|Seller newQuery()
 * @method static Builder|Seller query()
 * @method static Builder|Seller whereBanned($value)
 * @method static Builder|Seller whereCreatedAt($value)
 * @method static Builder|Seller whereDomain($value)
 * @method static Builder|Seller whereId($value)
 * @method static Builder|Seller whereName($value)
 * @method static Builder|Seller whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Seller extends Model
{

    use SoftDeletes;

    protected static function booted()
    {
        static::created(function (self $model) {
            app()->make(InitSellerSettingsCommand::class)->execute($model);
        });
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function operators(): HasMany
    {
        return $this->hasMany(Operator::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SellerSetting::class);
    }

    public function bots(): HasMany
    {
        return $this->hasMany(Bot::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return WalletType[]
     */
    public function getSupportedWalletTypes(): array
    {
        $walletTypes = [];

        if (QiwiManualWallet::whereSellerId($this->id)->where('active', 1)->exists()) {
            $walletTypes[] = new WalletType(WalletType::TYPE_QIWI_MANUAL);
        }

        if (KunaAccount::whereSellerId($this->id)->where('active', 1)->exists()) {
            $walletTypes[] = new WalletType(WalletType::TYPE_KUNA_CODE);
        }

        if (GlobalMoneyWallet::whereSellerId($this->id)->where('active', 1)->exists()) {
            $walletTypes[] = new WalletType(WalletType::TYPE_GLOBAL_MONEY);
        }

        if (EasyPayWallet::whereSellerId($this->id)
            ->where('active', 1)
            ->where('wrong_creadentials', 0)
            ->get()
            ->filter(function (EasyPayWallet $easyPayWallet) {
                return !$easyPayWallet->is_limit;
            })
            ->isNotEmpty()
        ) {
            $walletTypes[] = new WalletType(WalletType::TYPE_EASY_PAY);
        }

        $cryptoWallets = $this->getCryptoWallets();

        $walletTypes = [...$walletTypes, ...$cryptoWallets];

        return $walletTypes;
    }

    /**
     * @return PaymentMethod[]
     */
    public function getSupportedPaymentMethods(): array
    {
        return collect($this->getSupportedWalletTypes())
            ->map(fn (WalletType $walletType) => $walletType->getPaymentMethods())
            ->collapse()
            ->toArray();
    }

    private function getCryptoWallets(): array
    {
        $resolvingType = SellerSetting::whereSellerId($this->id)->where('key', 'wallets_resolving_type')->first()->value;

        return CryptoWallet::whereSellerId($this->id)
            ->with('ordersWaitingPayment')
            ->when(
                $resolvingType === CryptoWalletResolvingType::ONLY_ROTATE,
                fn (Builder $builder) => $builder
                    ->where('payment_type', CryptoWalletPaymentType::ROTATE)
            )
            ->when(
                $resolvingType === CryptoWalletResolvingType::ONLY_BITAPS,
                fn (Builder $builder) => $builder
                    ->where('payment_type', CryptoWalletPaymentType::BITAPS)
            )
            ->get()
            ->filter(
                fn (CryptoWallet $wallet) => $wallet->payment_type->getValue() === CryptoWalletPaymentType::BITAPS
                    || $wallet->ordersWaitingPayment->count() === 0
            )
            ->map(fn (CryptoWallet $wallet)      => $wallet->getWalletType())
            ->unique(fn (WalletType $walletType) => $walletType->getValue())
            ->values()
            ->toArray();
    }
}
