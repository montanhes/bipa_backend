<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Bipa</title>
    <meta name="description" content="Bipa conta as encomendas do Mercado Livre por QR, sem contar a mesma duas vezes.">
    <meta name="theme-color" content="#111111">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/icon.png">
    <style>
        :root {
            --bg: #111111;
            --card: #1a1a1a;
            --line: #2a2a2a;
            --fg: #ffffff;
            --muted: #a3a3a3;
            --green: #1fa152;
            --green-hi: #27b861;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: max(24px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(24px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left));
            background:
                radial-gradient(60rem 30rem at 50% -10%, rgba(31, 161, 82, 0.22), transparent 70%),
                var(--bg);
            color: var(--fg);
            font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        main { width: 100%; max-width: 520px; text-align: center; }
        .logo {
            width: 96px; height: 96px;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(31, 161, 82, 0.35);
        }
        h1 { margin: 20px 0 0; font-size: 2.75rem; line-height: 1.1; letter-spacing: -0.03em; }
        .lead { margin: 10px auto 32px; max-width: 28rem; color: var(--muted); }
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px; }
        .stat {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px 12px;
        }
        .stat svg { width: 28px; height: 28px; color: var(--green-hi); margin-bottom: 8px; }
        .stat strong { display: block; font-size: clamp(1.125rem, 5.8vw, 2rem); overflow-wrap: anywhere; line-height: 1.15; font-variant-numeric: tabular-nums; }
        .stat span { color: var(--muted); font-size: 0.875rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--green);
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            justify-content: center;
            min-height: 52px;
            padding: 15px 30px;
            border-radius: 14px;
            transition: background 0.15s;
        }
        .btn:hover, .btn:focus-visible { background: var(--green-hi); }
        .btn svg { width: 20px; height: 20px; }
        .btn { -webkit-tap-highlight-color: transparent; }
        .hint { margin: 14px 0 0; color: var(--muted); font-size: 0.8125rem; }
        .legal { margin-top: 32px; font-size: 0.8125rem; color: var(--muted); }
        .legal a { color: var(--muted); }
    </style>
</head>
<body>
    <main>
        <img class="logo" src="/icon.png" alt="Bipa" width="96" height="96">
        <h1>Bipa</h1>
        <p class="lead">Conte as encomendas do Mercado Livre por QR, sem contar a mesma duas vezes.</p>

        <div class="stats">
            <div class="stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m16 16 2 2 4-4"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15"/><path d="M3.29 7 12 12l8.71-5"/><path d="M12 22V12"/></svg>
                <strong>{{ number_format($packages, 0, ',', '.') }}</strong>
                <span>pacotes contados</span>
            </div>
            <div class="stat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <strong>{{ number_format($users, 0, ',', '.') }}</strong>
                <span>usuários cadastrados</span>
            </div>
        </div>

        <a class="btn" href="/download" download>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg>
            Baixar APP{{ $apkSize ? " ({$apkSize})" : '' }}
        </a>
        <p class="hint">Para Android. Ao abrir o arquivo baixado, o celular pode pedir permissão para instalar: é só tocar em Permitir.</p>
        <nav class="legal"><a href="/privacidade">Privacidade</a> · <a href="/termos">Termos</a></nav>
    </main>
</body>
</html>
