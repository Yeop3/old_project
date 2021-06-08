<?php

namespace App\Http\Controllers;

use App\Services\Wallet\Bitaps\BitapsCallbackHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Class BitapsCallbackController.
 */
class BitapsCallbackController extends Controller
{
    /**
     * @param Request               $request
     * @param BitapsCallbackHandler $bitapsCallbackHandler
     *
     * @return mixed
     */
    public function __invoke(Request $request, BitapsCallbackHandler $bitapsCallbackHandler)
    {
        Log::channel('bitaps_callbacks')
            ->info(\GuzzleHttp\json_encode($request->toArray(), JSON_PRETTY_PRINT));

        $bitapsCallbackHandler->handle($request);

        return $request->get('invoice');
    }
}
