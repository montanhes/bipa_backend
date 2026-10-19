<?php

namespace Tests\Feature;

use App\Models\PackageCount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_shows_totals_without_login(): void
    {
        $users = User::factory()->count(2)->create();
        PackageCount::factory()->for($users[0])->create(['quantity' => 40]);
        PackageCount::factory()->for($users[1])->create(['quantity' => 15]);

        $this->get('/')
            ->assertOk()
            ->assertSee('55')
            ->assertSeeText('pacotes contados')
            ->assertSeeText('usuários cadastrados');
    }

    public function test_download_link_is_always_shown_and_404s_without_apk(): void
    {
        config(['bipa.apk_path' => base_path('storage/app/apk/missing.apk')]);

        $this->get('/')->assertOk()->assertSee('Baixar APP');
        $this->get('/download')->assertNotFound();
    }

    public function test_download_serves_the_apk_with_its_size(): void
    {
        $apk = tempnam(sys_get_temp_dir(), 'apk');
        file_put_contents($apk, str_repeat('a', 2 * 1024 * 1024));
        config(['bipa.apk_path' => $apk]);

        $this->get('/')->assertSee('Baixar APP (2 MB)', false);
        $this->get('/download')->assertOk()->assertDownload('bipa.apk');

        unlink($apk);
    }
}
