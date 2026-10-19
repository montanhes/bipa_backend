<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PackageCountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'quantity' => 54,
            'client_uuid' => (string) Str::uuid(),
            'counted_at' => '2026-10-20T09:00:00Z',
            ...$overrides,
        ];
    }

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/counts', $this->payload())->assertUnauthorized();
    }

    public function test_stores_count_for_logged_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/counts', $this->payload())
            ->assertCreated()
            ->assertJsonPath('quantity', 54);

        $this->assertDatabaseHas('package_counts', ['user_id' => $user->id, 'quantity' => 54]);
        $this->assertNotNull($user->fresh()->last_seen_at);
    }

    public function test_retry_with_same_uuid_is_idempotent(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();

        $this->actingAs($user, 'sanctum')->postJson('/api/counts', $payload)->assertCreated();
        $this->actingAs($user, 'sanctum')->postJson('/api/counts', $payload)->assertOk();

        $this->assertDatabaseCount('package_counts', 1);
    }

    public function test_has_no_upper_limit(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/counts', $this->payload(['quantity' => 1_000_000]))
            ->assertCreated();
    }

    public function test_rejects_invalid_quantity(): void
    {
        $user = User::factory()->create();

        foreach ([0, -3, 'abc', null] as $quantity) {
            $this->actingAs($user, 'sanctum')
                ->postJson('/api/counts', $this->payload(['quantity' => $quantity]))
                ->assertUnprocessable();
        }
    }

    public function test_requires_valid_uuid(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/counts', $this->payload(['client_uuid' => 'nope']))
            ->assertUnprocessable();
    }

    public function test_same_uuid_for_different_users_is_allowed(): void
    {
        $payload = $this->payload();

        $this->actingAs(User::factory()->create(), 'sanctum')->postJson('/api/counts', $payload)->assertCreated();
        $this->actingAs(User::factory()->create(), 'sanctum')->postJson('/api/counts', $payload)->assertCreated();

        $this->assertDatabaseCount('package_counts', 2);
    }
}
