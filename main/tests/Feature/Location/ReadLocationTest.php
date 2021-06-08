<?php

namespace Tests\Feature\Location;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class ReadLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_for_select_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $location1 = factory(Location::class)->create([
            'name'      => '1',
            'seller_id' => $sellerUser->seller_id,
            'parent_id' => null,
        ]);
        $location2 = factory(Location::class)->create([
            'name'      => '2',
            'seller_id' => $sellerUser->seller_id,
            'parent_id' => $location1->id,
        ]);
        $location3 = factory(Location::class)->create([
            'name'      => '3',
            'seller_id' => $sellerUser->seller_id,
            'parent_id' => $location2->id,
        ]);
        $location4 = factory(Location::class)->create([
            'name'      => '4',
            'seller_id' => $sellerUser->seller_id,
            'parent_id' => $location3->id,
        ]);
        $location5 = factory(Location::class)->create([
            'name'      => '5',
            'seller_id' => $sellerUser->seller_id,
            'parent_id' => $location4->id,
        ]);

        $this->getJson('/api/locations/for_select')
            ->assertOk()
            ->assertJsonStructure([
                ['value', 'text'],
            ])
            ->assertJsonPath('4.text', implode(' -> ', ['1', '2', '3', '4', '5']));
    }
}
