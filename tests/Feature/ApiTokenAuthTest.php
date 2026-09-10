<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_valid_bearer_token_authenticates_the_api_guard(): void
    {
        $user = User::factory()->create();
        $token = $user->generateApiToken();

        $this->getJson('/api/user', ['Authorization' => 'Bearer '.$token])
            ->assertOk()
            ->assertJsonFragment(['email' => $user->email]);
    }

    /** @test */
    public function an_invalid_token_is_rejected(): void
    {
        User::factory()->create()->generateApiToken();

        $this->getJson('/api/user', ['Authorization' => 'Bearer not-a-real-token'])
            ->assertUnauthorized();
    }

    /** @test */
    public function the_raw_token_is_stored_only_as_a_hash(): void
    {
        $user = User::factory()->create();
        $token = $user->generateApiToken();

        $this->assertNotSame($token, $user->fresh()->api_token);
        $this->assertSame(hash('sha256', $token), $user->fresh()->api_token);
    }

    /** @test */
    public function revoking_the_token_blocks_further_access(): void
    {
        $user = User::factory()->create();
        $token = $user->generateApiToken();

        $user->revokeApiToken();

        $this->getJson('/api/user', ['Authorization' => 'Bearer '.$token])
            ->assertUnauthorized();
    }
}
