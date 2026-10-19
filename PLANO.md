# Bipa Backend: plano de implementação

Backend do app Bipa (`/home/ramon/projetos/oakz/bipa`, Expo, pacote `org.oakz.bipa`) e landing pública em `bipa.oakz.org`.

## Decisões fechadas

- Login Google **nativo**, feito uma única vez; só expira se o token expirar.
- Cada "Nova contagem" envia ao servidor a quantidade da contagem anterior daquele usuário.
- Sem teto de `quantity` por contagem (só precisa ser inteiro > 0).
- Landing sem login: estatísticas (usuários cadastrados, pacotes totais) e link de download do APK.
- Repositório git próprio em `bipa_backend/` (você commita no seu GitHub depois).

## Stack

- Laravel (estável atual), PHP 8.4/8.5, MariaDB.
- Laravel Sanctum para token de API do app.
- `google/apiclient` para validar o ID token do Google.
- Blade + Tailwind para a landing; sem SPA.
- Deploy no Coolify (`coolify.oakz.org`, VM Oracle `163.176.248.206`): app via Dockerfile + MariaDB como database do Coolify.

## 1. Autenticação (Google nativo, uma vez)

1. O app usa `@react-native-google-signin/google-signin`, o usuário escolhe a conta e o app recebe um **ID token**.
2. `POST /api/auth/google` com `{ "id_token": "..." }`.
3. Backend valida com o Google: assinatura, `aud` = nosso Web client ID, `exp`, `email_verified`.
4. `User::updateOrCreate(['google_id' => sub], [name, email, avatar])`.
5. Responde com token Sanctum e dados do usuário.
6. O app guarda o token em `expo-secure-store` e manda `Authorization: Bearer ...` nas chamadas seguintes. Nunca pede login de novo enquanto o token valer.

**Validade do token:** por padrão o Sanctum não expira tokens. Para "só expira se o token expirar", definir `SANCTUM_EXPIRATION` com um prazo longo (ex.: 1 ano = 525600 min). Quando vier 401, o app abre o login de novo **sem perder a contagem local**.

Tabela `users`:

| coluna | tipo | obs |
|---|---|---|
| id | bigint | |
| google_id | string, unique | claim `sub` |
| name | string | |
| email | string, unique | |
| avatar | string, nullable | URL da foto |
| last_seen_at | timestamp, nullable | atualizado a cada contagem recebida |
| timestamps | | |

Sem `password`, sem `remember_token`, sem tabelas de reset de senha; remover do scaffold o que não for usado.

Endpoints: `POST /api/auth/google` (público, com `throttle`) e `POST /api/auth/logout` (revoga o token atual).

## 2. Contagens

Tabela `package_counts`:

| coluna | tipo | obs |
|---|---|---|
| id | bigint | |
| user_id | FK users, index | |
| quantity | unsigned int | > 0, sem teto |
| client_uuid | uuid, unique | chave de idempotência gerada no app |
| counted_at | timestamp | quando o usuário tocou em "Nova contagem" |
| timestamps | | |

`POST /api/counts` (auth Sanctum):

- Body: `{ "quantity": 54, "client_uuid": "...", "counted_at": "2026-10-20T09:00:00Z" }`.
- Form Request: `quantity` inteiro ≥ 1, `client_uuid` uuid, `counted_at` data.
- Idempotente: `client_uuid` já existente responde 200 com o registro existente, sem duplicar.
- Atualiza `users.last_seen_at`; `throttle` por usuário.

### Fluxo do botão "Nova contagem" no app: recomendo uma fila local

Sua versão (bloquear até o servidor responder) é correta, mas tem um defeito prático: se o celular estiver sem sinal no galpão, o botão trava e o usuário não consegue contar o próximo lote. A fila resolve isso mantendo o mesmo resultado para você:

1. Usuário toca em "Nova contagem" e confirma.
2. Se o total for 0, só zera.
3. Se o total for ≠ 0, numa única transação do `expo-sqlite`: grava a contagem numa tabela local `outbox` (`client_uuid`, `quantity`, `counted_at`, `sent`) **e** zera a tela.
4. O app tenta enviar na hora. Se o servidor responder 2xx, marca como enviada.
5. Se falhar (sem rede/5xx), a contagem **continua salva** na fila e o app reenvia ao abrir, ao voltar ao primeiro plano e no próximo "Nova contagem". Aparece um aviso discreto "1 contagem pendente de envio".
6. O servidor deduplica por `client_uuid`, então reenviar nunca conta em dobro.

Para o servidor o ciclo é idêntico ao seu: uma contagem por "Nova contagem", só salva uma vez. A diferença é que o usuário nunca fica travado, e nada se perde porque a quantidade fica persistida no aparelho antes de zerar a tela. Se preferir a regra estrita (só zera depois do 2xx), é só pular o passo 3 e zerar no passo 4; a API não muda.

## 3. Landing (`bipa.oakz.org`)

- `GET /` público, página Blade simples: nome/ícone do Bipa, uma frase, os números e o botão "Baixar APK".
- **Usuários:** `COUNT(users)` (todos os cadastrados).
- **Pacotes:** `SUM(package_counts.quantity)` (total de todos os usuários).
- Cache de 1 a 5 minutos (`Cache::remember`).
- `GET /download` entrega o APK (arquivo em volume persistente do Coolify ou `storage/app/public`), fora do git, atualizado a cada release.

## 4. Chave de assinatura (item 5)

Hoje o release é assinado com a chave de debug (`bipa/android/app/build.gradle`, `signingConfig signingConfigs.debug`). O `chave_assinatura/playcheap.jks` é de **outro app**; não reutilizar. Gere uma exclusiva do Bipa:

```bash
keytool -genkeypair -v -storetype PKCS12 \
  -keystore ~/projetos/oakz/chave_assinatura/bipa-release.jks \
  -alias bipa -keyalg RSA -keysize 2048 -validity 10000
```

Ele pergunta senha da keystore (anote num gerenciador de senhas) e os dados do certificado. Depois pegue o SHA-1 que vai no Google Cloud:

```bash
keytool -list -v -keystore ~/projetos/oakz/chave_assinatura/bipa-release.jks -alias bipa | grep SHA1
```

Cuidados:

- **Faça backup** do `.jks` e da senha fora do computador. Se perder, não dá para publicar atualização que instale por cima do app antigo.
- Nunca commitar o `.jks` nem a senha (o `chave_assinatura/` fica fora de qualquer repositório).
- No `build.gradle`, criar `signingConfigs.release` lendo caminho e senhas de `~/.gradle/gradle.properties` (não do repositório) e usar no `buildTypes.release`.
- Ao trocar a assinatura, o Android recusa instalar por cima do app atual (assinado com debug): é preciso **desinstalar o Bipa do celular uma vez** e instalar o novo.
- Para desenvolvimento, dá para cadastrar também o SHA-1 da debug keystore em um segundo Client ID Android do mesmo pacote.

## 5. Google Cloud (manual, você faz)

- Projeto no Google Cloud + tela de consentimento OAuth (escopos `email`, `profile`).
- Client ID tipo **Web**: vira `GOOGLE_CLIENT_ID` no backend e `webClientId` no app.
- Client ID tipo **Android**: pacote `org.oakz.bipa` + SHA-1 da `bipa-release.jks`.
- Se o app de consentimento ficar em modo "Teste", só contas listadas como testadoras entram; para qualquer pessoa logar é preciso publicar o app de consentimento (para escopos básicos não exige verificação demorada).

## 6. Infra e deploy

1. Cloudflare: `A bipa -> 163.176.248.206`, **DNS only**, até o certificado do Let's Encrypt sair.
2. Coolify: projeto "bipa" com **MariaDB** (database do Coolify) e a aplicação Laravel (Dockerfile).
3. Variáveis: `APP_KEY`, `APP_URL=https://bipa.oakz.org`, `DB_*` (MariaDB interno), `GOOGLE_CLIENT_ID`, `SANCTUM_EXPIRATION`.
4. Domínio `https://bipa.oakz.org`; `php artisan migrate --force` no deploy.
5. Volume persistente para o APK; backup agendado do MariaDB.
6. O deploy no Coolify usa o repositório do GitHub que você criar para o backend.

## 7. Ordem de execução

1. `git init` em `bipa_backend/` e criar o projeto Laravel; `.env` para MariaDB local (docker compose só para desenvolvimento).
2. Migrations (`users` ajustada, `package_counts`), models e factories.
3. `POST /api/auth/google` + Sanctum, testes com o verificador do Google mockado.
4. `POST /api/counts`: Form Request, idempotência e testes (PHPUnit).
5. Landing com cache e rota de download, com teste de feature dos números.
6. Dockerfile, DNS `bipa` e deploy no Coolify; conferir SSL.
7. Gerar `bipa-release.jks`, configurar assinatura e cadastrar SHA-1 no Google Cloud.
8. Alterar o app: Google Sign-In, token no `expo-secure-store`, cliente HTTP, `outbox` no SQLite e novo fluxo de "Nova contagem"; gerar APK assinado.
9. Teste ponta a ponta no celular: login, contar, "Nova contagem", conferir os números na landing e testar com o modo avião.

## 8. Segurança e privacidade

- Sempre validar o ID token no servidor; nunca confiar em e-mail enviado pelo app.
- `throttle` em `/api/auth/google` e `/api/counts`.
- A landing mostra só agregados, sem nome, e-mail ou foto.
- Guardamos nome, e-mail e foto do Google (dados pessoais, LGPD): coletar só o necessário e ter como apagar a conta sob pedido.
- HTTPS obrigatório; token no `expo-secure-store`, não em AsyncStorage.
