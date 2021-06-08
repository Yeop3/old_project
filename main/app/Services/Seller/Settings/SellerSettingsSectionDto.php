<?php

declare(strict_types=1);

namespace App\Services\Seller\Settings;

use Webmozart\Assert\Assert;

/**
 * Class SellerSettingsSectionDto.
 */
final class SellerSettingsSectionDto
{
    private string $section;
    /**
     * @var SellerSettingDto[]
     */
    private array $sellerSettingDtos;

    public function __construct(string $section, array $sellerSettingDtos)
    {
        $this->section = $section;
        $this->sellerSettingDtos = $sellerSettingDtos;

        Assert::allIsInstanceOf($sellerSettingDtos, SellerSettingDto::class);
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getSellerSettingDtos(): array
    {
        return $this->sellerSettingDtos;
    }
}
