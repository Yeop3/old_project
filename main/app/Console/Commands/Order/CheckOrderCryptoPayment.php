<?php

namespace App\Console\Commands\Order;

use App\Models\Order;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\Crypto\CheckRotateBitcoinWalletPaymentCommand;
use App\Services\Wallet\Crypto\CheckRotateEthereumWalletPaymentCommand;
use Illuminate\Console\Command;

/**
 * Class CheckOrderCryptoPayment.
 */
class CheckOrderCryptoPayment extends Command
{
    private array $checkRotateClasses = [
        CheckRotateBitcoinWalletPaymentCommand::class,
        CheckRotateEthereumWalletPaymentCommand::class,
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check:crypto_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        foreach ($this->checkRotateClasses as $class) {
            $rotateClass = app()->make($class);
            $orders = Order::whereIn('status', [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID])
                ->get();

            foreach ($orders as $order) {
                $rotateClass->execute($order);
            }
        }
    }
}
