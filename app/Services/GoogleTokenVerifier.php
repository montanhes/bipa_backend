<?php

namespace App\Services;

interface GoogleTokenVerifier
{
    /**
     * Validate a Google ID token and return its claims, or null when invalid.
     *
     * @return array{sub: string, email: string, name: string, picture: string|null}|null
     */
    public function verify(string $idToken): ?array;
}
