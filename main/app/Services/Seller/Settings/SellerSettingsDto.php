<?php

declare(strict_types=1);

namespace App\Services\Seller\Settings;

use Webmozart\Assert\Assert;

/**
 * Class SellerSettingsDto.
 */
final class SellerSettingsDto
{
    /**
     * @var SellerSettingsSectionDto[]
     */
    private array $sections;

    public function __construct(array $sections)
    {
        Assert::allIsInstanceOf($sections, SellerSettingsSectionDto::class);

        $this->sections = $sections;
    }

    public function getSections(): array
    {
        return $this->sections;
    }
}
