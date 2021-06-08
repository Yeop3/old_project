<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Seller;
use App\Services\Order\VO\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class ClientActualExport.
 */
class ClientActualExport implements FromCollection, WithHeadings
{
    private Seller $seller;

    /**
     * ClientActualExport constructor.
     *
     * @param Seller $seller
     */
    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Client::whereSellerId($this->seller->id)
            ->with('orders')
            ->whereHas('orders', function (Builder $builder) {
                return $builder->whereIn('status', [
                    OrderStatus::STATUS_PAID,
                    OrderStatus::STATUS_GIVEN,
                ])->whereBetween('created_at', [
                    now()->subMonths(3)->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ]);
            })
            ->whereNotNull('username')
            ->whereNull('ban_expires_at')
            ->where('in_black_list', 0)
            ->get(['username'])
            ->each(static function (Client $client) {
                $client->username = '@'.$client->username;

                return $client;
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return ['username'];
    }
}
