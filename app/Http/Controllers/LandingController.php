<?php

namespace App\Http\Controllers;

use App\Models\PackageCount;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $stats = Cache::remember('landing.stats', 120, fn () => [
            'users' => User::count(),
            'packages' => (int) PackageCount::sum('quantity'),
        ]);

        $apk = config('bipa.apk_path');

        return view('landing', [
            ...$stats,
            'apkSize' => is_file($apk) ? Number::fileSize(filesize($apk), precision: 0) : null,
        ]);
    }
}
