<?php

namespace Tests\Feature\ProductType;

use App\Models\ProductType;
use App\Models\Seller;
use App\Services\ProductType\DeleteProductTypeCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProductTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $seller = factory(Seller::class)->create();
        $productType = factory(ProductType::class)->create(['seller_id' => $seller->id]);

        /** @var DeleteProductTypeCommand $command */
        $command = app()->make(DeleteProductTypeCommand::class);

        $command->execute($productType->number, $seller);

        $this->assertDatabaseMissing('product_types', ['id' => $productType->id, 'deleted_at' => null]);
    }
}
