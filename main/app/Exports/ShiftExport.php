<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\ProductType;
use App\Models\Seller;
use App\Models\Shift;
use App\Services\Order\VO\OrderStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Class ShiftExport.
 */
class ShiftExport implements FromView, ShouldAutoSize
{
    use Exportable;

    private Seller $seller;
    private int $number;

    /**
     * ShiftExport constructor.
     *
     * @param Seller $seller
     * @param int    $number
     */
    public function __construct(Seller $seller, int $number)
    {
        $this->seller = $seller;
        $this->number = $number;
    }

    public function view(): View
    {
        $products = ProductType::whereSellerId($this->seller->id)
            ->with(['orders' => fn (HasManyThrough $query) => $query
                ->with('shift')
                ->where('shift_id', $this->number),
            ])
            ->whereHas(
                'products',
                fn (Builder $q) => $q
                ->whereHas('orders', fn (Builder $q) => $q
                    ->where('shift_id', $this->number))
            )
            ->withCount(['orders as paid' => fn (Builder $query) => $query
                ->where('orders.status', '=', OrderStatus::STATUS_PAID),
                'orders as relocation' => fn (Builder $query) => $query,
            ])
            ->get();
        $sum = $products
            ->map(
                fn (ProductType $productType) => $productType
                ->orders
                ->map(
                    fn (Order $order) => $order
                    ->total_price
                    ->subtract($order->unpaid_amount)
                    ->getAmount()
                )
                ->sum()
            )
            ->sum();

        return view('export.shift', [
            'shift' => Shift::whereSellerId($this->seller->id)
                ->with([
                    'orders',
                    'orders.product',
                    'orders.product.productType',
                ])
                ->where('number', $this->number)
                ->first(),
            'products' => $products,
            'sum'      => $sum,
        ]);
    }
}
