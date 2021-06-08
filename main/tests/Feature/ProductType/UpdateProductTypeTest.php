<?php

namespace Tests\Feature\ProductType;

use App\Http\Requests\ProductType\CreateProductTypeRequest;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\ProductType\UpdateProductTypeCommand;
use App\VO\CommissionType;
use App\VO\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $seller = factory(Seller::class)->create();
        $productType = factory(ProductType::class)->create(['seller_id' => $seller->id]);

        $request = new CreateProductTypeRequest([], [
            'name'             => 'some name 123',
            'price'            => 155,
            'commission_value' => 10,
            'commission_type'  => CommissionType::TYPE_PERCENT,
            'packing'          => 50,
            'real_packing'     => 45,
            'unit'             => Unit::GRAM,
            'priority'         => 1,
        ]);

        $dto = $request->getDto();

        /** @var UpdateProductTypeCommand $command */
        $command = app()->make(UpdateProductTypeCommand::class);

        $productType = $command->execute($productType->number, $seller, $dto);

        $this->assertDatabaseHas('product_types', [
            'id'    => $productType->id,
            'name'  => 'some name 123',
            'price' => 15500,
        ]);
    }
}
