<?php

return [

    /*
    |--------------------------------------------------------------------------
    | APK path
    |--------------------------------------------------------------------------
    |
    | File served by the /download route. Kept outside git; in production it
    | lives on a persistent volume.
    |
    */

    'apk_path' => env('BIPA_APK_PATH', storage_path('app/apk/bipa.apk')),

];
