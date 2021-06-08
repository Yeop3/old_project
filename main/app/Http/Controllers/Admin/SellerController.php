<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CreateSellerRequest;
use App\Http\Requests\Seller\UpdateSellerRequest;
use App\Models\Seller;
use App\Services\Seller\Create\CreateSellerCommand;
use App\Services\Seller\Update\UpdateSellerCommand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class SellerController.
 */
class SellerController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Seller::paginate(20);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return Seller::findOrFail($id);
    }

    /**
     * @param CreateSellerRequest $request
     * @param CreateSellerCommand $command
     *
     * @return Seller
     */
    public function store(CreateSellerRequest $request, CreateSellerCommand $command): Seller
    {
        return $command->execute($request->getDto());
    }

    /**
     * @param UpdateSellerRequest $request
     * @param UpdateSellerCommand $command
     * @param $id
     *
     * @return Seller
     */
    public function update(UpdateSellerRequest $request, UpdateSellerCommand $command, $id): Seller
    {
        return $command->execute((int) ($id), $request->getDto());
    }

    /**
     * @param $id
     */
    public function ban($id): void
    {
        $seller = Seller::findOrFail($id);
        $seller->banned = 1;
        $seller->save();
    }

    /**
     * @param $id
     */
    public function unban($id): void
    {
        $seller = Seller::findOrFail($id);
        $seller->banned = 0;
        $seller->save();
    }
}
