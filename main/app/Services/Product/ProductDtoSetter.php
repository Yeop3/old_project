<?php
/**
 * Created by PhpStorm.
 * User: Aios
 * https://t.me/aiosslike.
 */

namespace App\Services\Product;

use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use Illuminate\Support\Str;

/**
 * Class ProductDtoSetter.
 */
final class ProductDtoSetter implements DtoMagic
{
    /**
     * @var int|null
     */
    private ?int $driverNumber = null;
    /**
     * @var int|null
     */
    private ?int $productTypeNumber = null;
    /**
     * @var int|null
     */
    private ?int $locationNumber = null;
    /**
     * @var Commission|null
     */
    private ?Commission $commission = null;
    /**
     * @var string|null
     */
    private ?string $address = null;

    /**
     * @var ProductStatus|null
     */
    private ?ProductStatus $status = null;
    /**
     * @var array|null
     */
    private ?array $images = null;

    /**
     * @var string|null
     */
    private ?string $video = null;
    /**
     * @var string|null
     */
    private ?string $coordinates = null;
    /**
     * @var int|null
     */
    private ?int $telegramId = null;

    /**
     * @param $name
     *
     * @return mixed|void
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * @param $name
     * @param $value
     *
     * @return DtoMagic
     */
    public function __set($name, $value): DtoMagic
    {
        $__callFunc = implode('', ['set', Str::studly($name)]);
        $this->{$__callFunc}($value);

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->{$name});
    }

    /**
     * @return int
     */
    public function getDriverNumber(): int
    {
        return $this->driverNumber;
    }

    /**
     * @return int
     */
    public function getProductTypeNumber(): int
    {
        return $this->productTypeNumber;
    }

    /**
     * @return int
     */
    public function getLocationNumber(): int
    {
        return $this->locationNumber;
    }

    /**
     * @return Commission
     */
    public function getCommission(): Commission
    {
        return $this->commission;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return ProductStatus
     */
    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    /**
     * @return array|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @return string|null
     */
    public function getVideo(): ?string
    {
        return $this->video;
    }

    /**
     * @return string|null
     */
    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    /**
     * @return int|null
     */
    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }

    /**
     * @param int $driverNumber
     *
     * @return ProductDtoSetter
     */
    public function setDriverNumber(int $driverNumber): ProductDtoSetter
    {
        $this->driverNumber = $driverNumber;

        return $this;
    }

    /**
     * @param int $productTypeNumber
     *
     * @return ProductDtoSetter
     */
    public function setProductTypeNumber(int $productTypeNumber): ProductDtoSetter
    {
        $this->productTypeNumber = $productTypeNumber;

        return $this;
    }

    /**
     * @param int $telegramId
     *
     * @return ProductDtoSetter
     */
    public function setTelegramId(int $telegramId): ProductDtoSetter
    {
        $this->telegramId = $telegramId;

        return $this;
    }

    /**
     * @param int $locationNumber
     *
     * @return ProductDtoSetter
     */
    public function setLocationNumber(int $locationNumber): ProductDtoSetter
    {
        $this->locationNumber = $locationNumber;

        return $this;
    }

    /**
     * @param Commission $commission
     *
     * @return ProductDtoSetter
     */
    public function setCommission(Commission $commission): ProductDtoSetter
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @param string|null $address
     *
     * @return ProductDtoSetter
     */
    public function setAddress(?string $address): ProductDtoSetter
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @param ProductStatus $status
     *
     * @return ProductDtoSetter
     */
    public function setStatus(ProductStatus $status): ProductDtoSetter
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array|null $images
     *
     * @return ProductDtoSetter
     */
    public function setImages(?array $images): ProductDtoSetter
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @param string|null $video
     *
     * @return ProductDtoSetter
     */
    public function setVideo(?string $video): ProductDtoSetter
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @param string|null $coordinates
     *
     * @return ProductDtoSetter
     */
    public function setCoordinates(?string $coordinates): ProductDtoSetter
    {
        $this->coordinates = $coordinates;

        return $this;
    }
}
