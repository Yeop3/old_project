<?php

namespace App\Models;

use App\Casts\ProxyTypeCast;
use App\Services\Proxy\VO\ProxyType;
use App\Services\Proxy\VO\ProxyVO;
use App\VO\Ip;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Proxy.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property string      $ip
 * @property int|null    $port
 * @property string|null $username
 * @property string|null $password
 * @property ProxyType   $proxy_type
 * @property string|null $note
 * @property int         $is_working
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Seller $seller
 *
 * @method static Builder|Proxy newModelQuery()
 * @method static Builder|Proxy newQuery()
 * @method static Builder|Proxy ofSeller(User $user, $sellerId = null)
 * @method static Builder|Proxy query()
 * @method static Builder|Proxy whereCreatedAt($value)
 * @method static Builder|Proxy whereId($value)
 * @method static Builder|Proxy whereIp($value)
 * @method static Builder|Proxy whereIsWorking($value)
 * @method static Builder|Proxy whereNote($value)
 * @method static Builder|Proxy whereNumber($value)
 * @method static Builder|Proxy wherePassword($value)
 * @method static Builder|Proxy wherePort($value)
 * @method static Builder|Proxy whereProxyType($value)
 * @method static Builder|Proxy whereSellerId($value)
 * @method static Builder|Proxy whereUpdatedAt($value)
 * @method static Builder|Proxy whereUsername($value)
 * @mixin Eloquent
 */
class Proxy extends Model
{
    use SellerIncrementId;

    /**
     * @var string[]
     */
    protected $casts = [
        'proxy_type' => ProxyTypeCast::class,
    ];

    /**
     * @return array|string[]
     */
    public function getForRequests(): array
    {
        if (!$this->username || !$this->password) {
            return [
                $this->ip.':'.$this->port,
            ];
        }

        return [
            $this->ip.':'.$this->port,
            $this->username,
            $this->password,
        ];
    }

    /**
     * @return ProxyVO
     */
    public function toVO(): ProxyVO
    {
        return new ProxyVO(
            new Ip($this->ip),
            $this->port,
            $this->proxy_type,
            $this->username,
            $this->password
        );
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toVO()->toString();
    }
}
