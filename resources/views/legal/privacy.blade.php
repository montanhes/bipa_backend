@extends('legal.layout')

@section('title', 'Política de Privacidade')

@section('content')
    <p>O Bipa é um aplicativo que conta encomendas por QR code. Esta página explica quais dados ele guarda e para quê.</p>

    <h2>O que coletamos</h2>
    <ul>
        <li>Ao entrar com sua conta Google: seu nome, e-mail e foto de perfil. Não recebemos sua senha do Google.</li>
        <li>A quantidade de pacotes de cada contagem que você envia e a data em que ela foi feita.</li>
    </ul>
    <p>Não coletamos sua localização, contatos, câmera ou qualquer dado das encomendas lidas. A câmera é usada só no seu aparelho, para ler o QR code, e as imagens não saem dele.</p>

    <h2>Para que usamos</h2>
    <ul>
        <li>Identificar você no aplicativo e associar suas contagens à sua conta.</li>
        <li>Mostrar, na página inicial, números gerais (total de pacotes contados e de usuários cadastrados). Nenhum nome, e-mail ou foto é exibido publicamente.</li>
    </ul>

    <h2>Compartilhamento</h2>
    <p>Não vendemos nem compartilhamos seus dados com terceiros. Eles ficam em um servidor próprio e são acessados apenas pelo responsável pelo aplicativo.</p>

    <h2>Seus direitos</h2>
    <p>Você pode pedir a qualquer momento acesso, correção ou exclusão da sua conta e das suas contagens, escrevendo para <a href="mailto:ramon@oakz.org">ramon@oakz.org</a>.</p>

    <h2>Contato</h2>
    <p>Dúvidas sobre esta política: <a href="mailto:ramon@oakz.org">ramon@oakz.org</a>.</p>
@endsection
