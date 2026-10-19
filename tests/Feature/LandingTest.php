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
            ->assertSeeText('usuários');
    }

    public function test_download_button_only_when_apk_exists(): void
    {
        config(['bipa.apk_path' => base_path('storage/app/apk/missing.apk')]);
        $this->get('/')->assertOk()->assertDontSee('Baixar APK');
        $this->get('/download')->assertNotFound();

        $apk = tempnam(sys_get_temp_dir(), 'apk');
        config(['bipa.apk_path' => $apk]);
        Cache::flush();

        $this->get('/')->assertSee('Baixar APK');
        $this->get('/download')->assertOk()->assertDownload('bipa.apk');

        unlink($apk);
    }
}
