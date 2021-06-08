<?php

namespace App\Jobs\Product;

use App\Bot\Notifications\NotifyCreateProduct;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateProductJob.
 */
class CreateProductJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Product $product;
    private Client $client;
    private array $botNumber;

    /**
     * CreateProductJob constructor.
     *
     * @param Product $product
     * @param Client  $client
     * @param array   $botNumber
     */
    public function __construct(Product $product, Client $client, array $botNumber)
    {
        $this->product = $product;
        $this->client = $client;
        $this->botNumber = $botNumber;
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
        app()->make(NotifyCreateProduct::class)->execute($this->product, $this->client, $this->botNumber);
    }
}
