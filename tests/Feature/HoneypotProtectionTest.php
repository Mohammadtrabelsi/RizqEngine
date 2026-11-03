<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Honeypot\Honeypot;
use Tests\TestCase;

class HoneypotProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fixed field name so the test can address it directly.
        config(['honeypot.enabled' => true, 'honeypot.randomize_name_field_name' => false]);
    }

    private function user(): User
    {
        return User::factory()->create([
            'email' => 'cashier@example.com',
            'password' => bcrypt('secret-password'),
            'is_active' => 1,
        ]);
    }

    public function test_the_sign_in_form_renders_the_honeypot_fields(): void
    {
        $this->get(route('redesign.login'))
            ->assertOk()
            ->assertSee('extraFields.my_name', false)
            ->assertSee('extraFields.valid_from', false);
    }

    public function test_a_human_can_sign_in(): void
    {
        $this->user();

        $component = Livewire::test(Login::class);

        $this->travel(5)->seconds();

        $component
            ->set('email', 'cashier@example.com')
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_a_bot_filling_the_honeypot_is_rejected(): void
    {
        $this->user();

        $component = Livewire::test(Login::class);
        $this->travel(5)->seconds();

        $component
            ->set('extraFields.my_name', 'I am a bot')
            ->set('email', 'cashier@example.com')
            ->set('password', 'secret-password')
            ->call('login')
            ->assertForbidden();

        $this->assertGuest();
    }

    public function test_a_submission_faster_than_a_human_is_rejected(): void
    {
        $this->user();

        Livewire::test(Login::class)
            ->set('email', 'cashier@example.com')
            ->set('password', 'secret-password')
            ->call('login')
            ->assertForbidden();

        $this->assertGuest();
    }

    public function test_the_honeypot_can_be_disabled(): void
    {
        config(['honeypot.enabled' => false]);
        $this->user();

        Livewire::test(Login::class)
            ->set('extraFields.my_name', 'anything')
            ->set('email', 'cashier@example.com')
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('dashboard'));
    }

    public function test_the_password_reset_form_is_protected(): void
    {
        Notification::fake();
        $user = $this->user();

        $this->get(route('password.request'))->assertOk()->assertSee('name="my_name"', false);

        // Bot: honeypot filled -> blank response, no e-mail.
        $this->post(route('password.email'), [
            'email' => $user->email,
            'my_name' => 'bot',
            'valid_from' => app(Honeypot::class)->encryptedValidFrom(),
        ])->assertOk()->assertContent('');

        Notification::assertNothingSent();

        // Human: honeypot empty and the form was open long enough.
        $validFrom = app(Honeypot::class)->encryptedValidFrom();
        $this->travel(5)->seconds();

        $this->post(route('password.email'), [
            'email' => $user->email,
            'my_name' => '',
            'valid_from' => $validFrom,
        ])->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
