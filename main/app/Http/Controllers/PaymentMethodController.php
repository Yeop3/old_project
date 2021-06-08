<?php

namespace App\Http\Controllers;

use App\Services\Wallet\VO\PaymentMethod;
use Illuminate\Routing\Controller;
use Tightenco\Collect\Support\Collection;

/**
 * Class PaymentMethodController.
 */
class PaymentMethodController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function index()
    {
        return collect(PaymentMethod::TYPES)
            ->map(fn (string $label, int $key) => ['value' => $key, 'text' => $label])
            ->values();
    }
}
