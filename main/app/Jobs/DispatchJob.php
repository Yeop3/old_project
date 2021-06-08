<?php

namespace App\Jobs;

use App\Bot\Notifications\NotifyDispatch;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class DispatchJob.
 */
class DispatchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Dispatch $dispatch;
    private Client $client;
    private Seller $seller;

    /**
     * DispatchJob constructor.
     *
     * @param Dispatch $dispatch
     * @param Client   $client
     * @param Seller   $seller
     */
    public function __construct(Dispatch $dispatch, Client $client, Seller $seller)
    {
        $this->dispatch = $dispatch;
        $this->client = $client;
        $this->seller = $seller;
    }

    /**
     * Execute the job.
     *
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     *
     * @return void
     */
    public function handle(): void
    {
        app()->make(NotifyDispatch::class)->execute($this->dispatch, $this->client, $this->seller);
    }
}
