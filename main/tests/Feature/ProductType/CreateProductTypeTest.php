<?php

namespace Tests\Feature\ProductType;

use App\Http\Requests\ProductType\CreateProductTypeRequest;
use App\Models\Seller;
use App\Services\ProductType\CreateProductTypeCommand;
use App\VO\CommissionType;
use App\VO\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProductTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $seller = factory(Seller::class)->create();

        $request = new CreateProductTypeRequest([], [
            'name'             => 'some name',
            'price'            => 150,
            'commission_value' => 10,
            'commission_type'  => CommissionType::TYPE_PERCENT,
            'packing'          => 50,
            'real_packing'     => 45,
            'unit'             => Unit::GRAM,
            'priority'         => 1,
        ]);

        $dto = $request->getDto();

        /** @var CreateProductTypeCommand $command */
        $command = app()->make(CreateProductTypeCommand::class);

        $productType = $command->execute($seller, $dto);

        $this->assertDatabaseHas('product_types', ['id' => $productType->id]);
    }
}
