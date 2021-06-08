<?php

namespace App\Http\Requests\Location;

use App\Models\Location;
use App\Models\User;
use App\Services\Location\LocationDto;
use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateLocationRequest.
 */
class UpdateLocationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        $commissionType = $this->get('commission_type');
        $locationId = $this->route('location');

        $parentLocation = Location::whereSellerId($user->seller_id)->whereNumber($this->get('parent_number'))->first();

        $rules = [
            'name' => [
                'required',
                'max:255',
                Rule::unique('locations')
                    ->ignore($locationId, 'number')
                    ->where('seller_id', $user->seller_id)
                    ->where('parent_id', $parentLocation->id ?? null),
            ],
            'commission_type'  => ['required', Rule::in(array_keys(CommissionType::TYPES))],
            'priority'         => ['required', 'integer', 'min:0'],
            'is_branch'        => ['boolean'],
            'driver_numbers'   => ['nullable', 'array'],
            'driver_numbers.*' => ['required', 'integer'],
        ];

        if ($commissionType === CommissionType::TYPE_PERCENT) {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0', 'max:100'];
        } else {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function getDto(): LocationDto
    {
        return new LocationDto(
            $this->get('name'),
            (int) $this->get('priority'),
            parseBool($this->get('is_branch')),
            new Commission(
                (int) ($this->get('commission_value')) ?: 0,
                new CommissionType((int) ($this->get('commission_type')))
            ),
            $this->get('driver_numbers', []),
            parseIntFromInput($this->get('parent_number')),
        );
    }
}
