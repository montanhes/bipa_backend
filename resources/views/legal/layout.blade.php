<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title') - Bipa</title>
    <meta name="theme-color" content="#111111">
    <link rel="icon" type="image/png" href="/favicon.png">
    <style>
        :root { --bg: #111111; --fg: #ffffff; --muted: #a3a3a3; --green: #1fa152; --green-hi: #27b861; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: max(24px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(40px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left));
            background: var(--bg);
            color: var(--fg);
            font: 16px/1.65 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        main { max-width: 640px; margin: 0 auto; }
        header a { display: inline-flex; align-items: center; gap: 10px; color: var(--fg); text-decoration: none; font-weight: 700; }
        header img { width: 36px; height: 36px; border-radius: 9px; }
        h1 { margin: 28px 0 4px; font-size: 1.75rem; line-height: 1.2; letter-spacing: -0.02em; }
        h2 { margin: 28px 0 6px; font-size: 1.1rem; color: var(--green-hi); }
        p, li { color: #d4d4d4; }
        .updated { color: var(--muted); font-size: 0.875rem; margin: 0 0 8px; }
        a { color: var(--green-hi); overflow-wrap: anywhere; }
        ul { padding-left: 1.25rem; }
    </style>
</head>
<body>
    <main>
        <header><a href="/"><img src="/icon.png" alt="" width="36" height="36">Bipa</a></header>
        <h1>@yield('title')</h1>
        <p class="updated">Atualizado em 19 de outubro de 2026</p>
        @yield('content')
    </main>
</body>
</html>
