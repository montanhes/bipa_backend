# Bipa Backend

API e landing do app Bipa (`org.oakz.bipa`). Plano completo em [PLANO.md](PLANO.md).

- `POST /api/auth/google`: troca o ID token do Google por um token Sanctum.
- `POST /api/counts`: registra a contagem de pacotes do usuário logado (idempotente por `client_uuid`).
- `GET /`: landing com totais. `GET /download`: APK em `storage/app/apk/bipa.apk` (ou `BIPA_APK_PATH`).

## Desenvolvimento

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test
```

App em http://localhost:8080. Defina `GOOGLE_CLIENT_ID` (Client ID tipo Web) no `.env`.
