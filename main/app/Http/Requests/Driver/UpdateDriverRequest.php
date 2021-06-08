<?php

namespace App\Http\Requests\Driver;

use App\Models\User;
use App\Services\Driver\DriverDto;
use App\Services\Driver\VO\PermissionTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Class UpdateDriverRequest.
 */
class UpdateDriverRequest extends FormRequest
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

        $driverId = $this->route('driver');

        return [
            'name'               => [
                'required',
                'max:255',
                Rule::unique('drivers')->ignore($driverId, 'number')->where('seller_id', $user->seller_id),
            ],
            'client_number'      => [
                'required',
                'integer',
                Rule::exists('clients', 'number')->where('seller_id', $user->seller_id),
            ],
            'permissions'        => ['nullable', 'array'],
            'permissions.*'      => [
                'required',
                'integer',
                Rule::in(array_keys(PermissionTypes::TYPES))
            ],
            'location_numbers'   => ['nullable', 'array'],
            'location_numbers.*' => [
                'required',
                'integer',
                Rule::exists('locations', 'number')->where('seller_id', $user->seller_id),
            ],
        ];
    }

    /**
     * @return DriverDto
     */
    public function getDto(): DriverDto
    {
        /** @var Collection $permissionTypes */
        $permissionTypes = collect($this->get('permissions'))
            ->map(fn ($permissionTypeValue) => new PermissionTypes((int) $permissionTypeValue));

        return new DriverDto(
            $this->get('name'),
            parseIntFromInput($this->get('client_number')),
            $this->get('location_numbers'),
            $permissionTypes->toArray()
        );
    }
}
