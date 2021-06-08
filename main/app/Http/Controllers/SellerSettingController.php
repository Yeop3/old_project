<?php

namespace App\Http\Controllers;

use App\Http\Requests\Seller\Settings\UpdateSellerSettingsRequest;
use App\Models\SellerSetting;
use App\Models\User;
use App\Services\Seller\Settings\UpdateSellerSettingsCommand;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

/**
 * Class SellerSettingController.
 */
class SellerSettingController extends Controller
{
    /**
     * @param Request $request
     *
     * @return SellerSetting[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    public function index(Request $request)
    {
        $sections = $request->get('sections');

        /** @var User $user */
        $user = auth()->user();

        return SellerSetting::whereSellerId($user->seller_id)
            ->when($sections, fn (Builder $builder) => $builder->whereIn('section', $sections))
            ->get()
            ->groupBy('section')
            ->map(fn (Collection $sections) => $sections->pluck('value', 'key'));
    }

    /**
     * @param UpdateSellerSettingsRequest $request
     * @param UpdateSellerSettingsCommand $command
     */
    public function update(UpdateSellerSettingsRequest $request, UpdateSellerSettingsCommand $command): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute($user->seller, $request->getDto());
    }
}
