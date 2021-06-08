<?php

namespace Tests\Feature\BotLogic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\SellerHelper;
use Tests\TestCase;

class BotLogicUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $this->artisan('bot_logics:init');
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $number = $this->postJson('/api/bot_logics/standard/1/clone')
            ->assertStatus(201)
            ->json('number');

        $logic = $this->getJson('/api/bot_logics/client/'.$number)
            ->assertStatus(200)
            ->assertJsonStructure(BotLogicHelper::LOGIC_JSON_STRUCTURE)
            ->json();

        $this->changeLogic($logic);

        $this->putJson('/api/bot_logics/client/'.$number, $logic)
            ->assertOk();

        $updatedLogic = $this->getJson('/api/bot_logics/client/'.$number)
            ->assertStatus(200)
            ->assertJsonStructure(BotLogicHelper::LOGIC_JSON_STRUCTURE)
            ->json();

        $this->assertEquals(
            clearArray($logic, ['updated_at']),
            clearArray($updatedLogic, ['updated_at'])
        );
    }

    private function changeLogic(array &$logic): void
    {
        $logic['name'] = 'test logic';
        $logic['description'] = 'test logic description';

        foreach ($logic['commands'] as &$command) {
            foreach ($command['templates'] as &$template) {
                $template['content'] = Str::random(20);
            }
        }

        foreach ($logic['events'] as &$event) {
            $event['content'] = Str::random(20);
        }

        foreach ($logic['operator_notifications'] as &$notification) {
            $notification['content'] = Str::random(20);
        }

        foreach ($logic['antispams'] as &$antispam) {
            foreach ($antispam['options'] as &$option) {
                $option['value'] = Str::random(20);
            }
        }

        foreach ($logic['reminders'] as &$reminder) {
            foreach ($reminder['options'] as &$option) {
                $option['value'] = Str::random(20);
            }
        }

        foreach ($logic['distributions'] as &$distribution) {
            $distribution['content'] = Str::random(20);
        }
    }
}
