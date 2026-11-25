<?php

namespace Tests\Feature;

use App\Models\Prize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrizeManagementAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_change_prizes_or_clear_winner_history(): void
    {
        $this->postJson('/prizes', ['name' => 'Hadiah', 'stock' => 1])
            ->assertUnauthorized();

        $this->postJson('/spin')->assertUnauthorized();
        $this->deleteJson('/winners')->assertUnauthorized();

        $this->assertDatabaseCount('prizes', 0);
    }

    public function test_guest_must_open_the_standalone_login_page_before_the_wheel(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/login')
            ->assertOk()
            ->assertViewIs('login')
            ->assertSee('name="password"', false);
    }

    public function test_seeded_manager_can_log_in_and_manage_prizes(): void
    {
        $this->seed();

        $this->post('/login', [
            'username' => 'marcomm',
            'password' => 'marcomm26*',
        ])->assertRedirect(route('wheel'));

        $this->assertAuthenticatedAs(User::where('username', 'marcomm')->firstOrFail());

        $this->get('/')
            ->assertOk()
            ->assertSee('Masuk sebagai marcomm')
            ->assertDontSee('name="password"', false);

        $this->postJson('/prizes', ['name' => 'Hadiah', 'stock' => 1])
            ->assertCreated();

        $this->assertDatabaseHas('prizes', ['name' => 'Hadiah']);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->get('/')->assertRedirect(route('login'));
    }
}
