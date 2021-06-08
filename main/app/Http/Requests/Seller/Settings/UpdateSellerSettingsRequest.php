<?php

namespace App\Http\Requests\Seller\Settings;

use App\Services\Seller\Settings\SellerSettingDto;
use App\Services\Seller\Settings\SellerSettingsDto;
use App\Services\Seller\Settings\SellerSettingsSectionDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSellerSettingsRequest.
 */
class UpdateSellerSettingsRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        $rules = [
            'sections' => ['required', 'array', 'min:1'],
        ];

        foreach ($this->get('sections', []) as $sectionName => $settings) {
            $sectionConfig = config('seller_settings.'.$sectionName);

            foreach ($settings as $key => $value) {
                $settingConfig = $sectionConfig[$key];
                $rules["sections.$sectionName.$key"] = $settingConfig['rules'];
            }
        }

        return $rules;
    }

    public function getDto(): SellerSettingsDto
    {
        return new SellerSettingsDto(
            collect($this->get('sections'))
                ->map(
                    fn (array $settings, string $section) => new SellerSettingsSectionDto(
                        $section,
                        collect($settings)
                            ->map(
                                fn (?string $value, string $key) => new SellerSettingDto($key, $value)
                            )
                            ->toArray()
                    )
                )
                ->toArray()
        );
    }
}
