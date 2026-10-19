<?php

namespace App\Services;

use Google\Client;

class GoogleClientTokenVerifier implements GoogleTokenVerifier
{
    public function __construct(private readonly string $clientId) {}

    public function verify(string $idToken): ?array
    {
        $payload = (new Client(['client_id' => $this->clientId]))->verifyIdToken($idToken);

        if (! $payload || empty($payload['sub']) || empty($payload['email']) || empty($payload['email_verified'])) {
            return null;
        }

        return [
            'sub' => (string) $payload['sub'],
            'email' => $payload['email'],
            'name' => $payload['name'] ?? $payload['email'],
            'picture' => $payload['picture'] ?? null,
        ];
    }
}
