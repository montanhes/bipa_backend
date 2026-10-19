<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $path = config('bipa.apk_path');

        abort_unless(is_file($path), 404);

        return response()->download($path, 'bipa.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}
