<?php

namespace Tests\Feature\Operator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class ReadOperatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this->getJson('/api/operators')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['number', 'name']],
            ]);
    }
}
