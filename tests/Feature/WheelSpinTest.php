<?php

namespace Tests\Feature;

use App\Models\Prize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WheelSpinTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_remove_prize_option_does_not_remove_remaining_stock(): void
    {
        $this->actingAs(User::factory()->create());

        $prize = Prize::create([
            'name' => 'Hadiah',
            'stock' => 2,
            'sort_order' => 0,
        ]);

        $this->postJson('/spin', ['remove_prize' => true])
            ->assertOk();

        $this->assertDatabaseHas('prizes', [
            'id' => $prize->id,
            'stock' => 1,
        ]);
        $this->assertDatabaseCount('winners', 1);
    }

    public function test_prize_is_removed_when_its_stock_runs_out(): void
    {
        $this->actingAs(User::factory()->create());

        $prize = Prize::create([
            'name' => 'Hadiah',
            'stock' => 1,
            'sort_order' => 0,
        ]);

        $this->postJson('/spin')->assertOk();

        $this->assertDatabaseMissing('prizes', ['id' => $prize->id]);
        $this->assertDatabaseHas('winners', ['prize_name' => 'Hadiah']);
    }
}
