<?php

declare(strict_types=1);

namespace App\Services\Seller\Settings;

use App\Models\Seller;
use App\Models\SellerSetting;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateSellerSettingsCommand.
 */
final class UpdateSellerSettingsCommand
{
    public function execute(Seller $seller, SellerSettingsDto $dto): void
    {
        $settings = SellerSetting::whereSellerId($seller->id)->get();

        DB::beginTransaction();

        foreach ($dto->getSections() as $section) {
            $sectionKey = $section->getSection();

            foreach ($section->getSellerSettingDtos() as $sellerSettingDto) {
                $setting = $settings->where('section', $sectionKey)->where('key', $sellerSettingDto->getKey())->first();

                $setting->value = $sellerSettingDto->getValue();

                $setting->save();
            }
        }

        DB::commit();
    }
}
