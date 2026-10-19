<?php

namespace App\Http\Controllers;

use App\Models\PackageCount;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $stats = Cache::remember('landing.stats', 120, fn () => [
            'users' => User::count(),
            'packages' => (int) PackageCount::sum('quantity'),
        ]);

        return view('landing', [
            ...$stats,
            'apkAvailable' => is_file(config('bipa.apk_path')),
        ]);
    }
}
