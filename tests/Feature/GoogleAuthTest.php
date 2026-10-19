<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleTokenVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function fakeVerifier(?array $claims): void
    {
        $this->app->instance(GoogleTokenVerifier::class, new class($claims) implements GoogleTokenVerifier
        {
            public function __construct(private readonly ?array $claims) {}

            public function verify(string $idToken): ?array
            {
                return $this->claims;
            }
        });
    }

    public function test_login_creates_user_and_returns_token(): void
    {
        $this->fakeVerifier(['sub' => '123', 'email' => 'a@b.com', 'name' => 'Ana', 'picture' => 'https://x/p.png']);

        $this->postJson('/api/auth/google', ['id_token' => 'abc'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'avatar']]);

        $this->assertDatabaseHas('users', ['google_id' => '123', 'email' => 'a@b.com', 'name' => 'Ana']);
    }

    public function test_login_twice_does_not_duplicate_user(): void
    {
        $this->fakeVerifier(['sub' => '123', 'email' => 'a@b.com', 'name' => 'Ana', 'picture' => null]);

        $this->postJson('/api/auth/google', ['id_token' => 'abc'])->assertOk();
        $this->postJson('/api/auth/google', ['id_token' => 'abc'])->assertOk();

        $this->assertDatabaseCount('users', 1);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $this->fakeVerifier(null);

        $this->postJson('/api/auth/google', ['id_token' => 'bad'])->assertUnauthorized();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_id_token_is_required(): void
    {
        $this->postJson('/api/auth/google')->assertUnprocessable();
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('bipa')->plainTextToken;

        $this->withToken($token)->postJson('/api/auth/logout')->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
