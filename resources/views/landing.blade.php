<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bipa</title>
    <meta name="description" content="Bipa conta as encomendas do Mercado Livre por QR sem duplicar.">
    <style>
        :root { --bg: #111; --fg: #f4f4f4; --muted: #9a9a9a; --card: #1b1b1b; --green: #1fa152; }
        @media (prefers-color-scheme: light) {
            :root { --bg: #fafafa; --fg: #151515; --muted: #666; --card: #fff; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: var(--bg); color: var(--fg); font: 16px/1.5 system-ui, sans-serif; }
        main { width: 100%; max-width: 520px; text-align: center; }
        h1 { font-size: 3rem; margin: 0; letter-spacing: -0.03em; }
        p.lead { color: var(--muted); margin: 8px 0 32px; }
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 32px; }
        .stat { background: var(--card); border-radius: 14px; padding: 20px 12px; }
        .stat strong { display: block; font-size: 2rem; font-variant-numeric: tabular-nums; }
        .stat span { color: var(--muted); font-size: 0.875rem; }
        a.btn { display: inline-block; background: var(--green); color: #fff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 12px; }
        small { display: block; margin-top: 12px; color: var(--muted); }
    </style>
</head>
<body>
    <main>
        <h1>Bipa</h1>
        <p class="lead">Conte as encomendas do Mercado Livre por QR, sem contar a mesma duas vezes.</p>

        <div class="stats">
            <div class="stat"><strong>{{ number_format($packages, 0, ',', '.') }}</strong><span>pacotes contados</span></div>
            <div class="stat"><strong>{{ number_format($users, 0, ',', '.') }}</strong><span>usuários</span></div>
        </div>

        @if ($apkAvailable)
            <a class="btn" href="/download">Baixar APK</a>
            <small>Android. Permita a instalação de fontes desconhecidas.</small>
        @endif
    </main>
</body>
</html>
