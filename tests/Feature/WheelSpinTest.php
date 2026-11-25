<?php

namespace Tests\Feature;

use App\Models\Prize;
use App\Models\User;
use Carbon\CarbonImmutable;
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

    public function test_grand_prize_odds_are_reduced_until_the_configured_deadline(): void
    {
        config(['wheel.grand_prize_reduction_until' => '2026-11-29T00:00:00+07:00']);
        $this->travelTo(CarbonImmutable::parse('2026-11-28T23:59:59+07:00'));

        $this->assertTrue(Prize::areGrandPrizeOddsReduced());

        $grandPrize = new Prize(['is_grand' => true]);
        $regularPrize = new Prize(['is_grand' => false]);

        $this->assertSame(1, $grandPrize->drawWeight(true));
        $this->assertSame(10, $regularPrize->drawWeight(true));

        $this->travelTo(CarbonImmutable::parse('2026-11-29T00:00:00+07:00'));

        $this->assertFalse(Prize::areGrandPrizeOddsReduced());
        $this->assertSame(1, $grandPrize->drawWeight(false));
        $this->assertSame(1, $regularPrize->drawWeight(false));
    }
}
