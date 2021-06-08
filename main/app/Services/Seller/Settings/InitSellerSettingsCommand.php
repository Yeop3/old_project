<?php

declare(strict_types=1);

namespace App\Services\Seller\Settings;

use App\Models\Seller;
use App\Models\SellerSetting;

/**
 * Class InitSellerSettingsCommand.
 */
final class InitSellerSettingsCommand
{
    public function execute(Seller $seller): void
    {
        $allSettings = config('seller_settings');

        foreach ($allSettings as $section => $settings) {
            foreach ($settings as $key => $setting) {
                $settingModel = SellerSetting::whereSellerId($seller->id)
                    ->whereSection($section)
                    ->where('key', $key)
                    ->first();

                if (!$settingModel) {
                    $settingModel = new SellerSetting();

                    $settingModel->seller_id = $seller->id;
                    $settingModel->section = $section;
                    $settingModel->key = $key;
                    $settingModel->value = $setting['value'];
                    $settingModel->save();
                }
            }
        }
    }
}
