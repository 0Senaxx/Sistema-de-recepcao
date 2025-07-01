<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Firewall/login.php");
    exit;
}

$perfil = $_SESSION['perfil'] ?? '';

$path = dirname($_SERVER['PHP_SELF']);
$pastas = explode('/', trim($path, '/'));
$pastaAtual = $pastas[1] ?? ''; // Pega a segunda pasta da URL

//PERMISSÕES PARA CADA PERFIL

$permissoes = [
    'ADM' => ['Firewall', '02-Inicio', '03-Registrar', '04-Visitantes', '05-Visitas', '06-Ramais', '07-Relatorios', '08-Servidores', '09-Setores', '10-Administrador', 'Modulo-ADM'],
    'GCP' => ['Modulo-GCP'],
    'GEPES' => ['Modulo-GEPES'],
    'Recepcionista' => ['Modulo-RECEP']
];

//CASO O USUÁRIO NÃO TENHA PERMIÇÃO SERÁ EXIBIDA ESSA TELA:

if (!in_array($pastaAtual, $permissoes[$perfil] ?? [])) {
    header("HTTP/1.1 403 Forbidden");
?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <title>403 - Acesso Negado</title>
        <style>
            /* Reset básico */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Tipografia e fundo */
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                background: rgb(248, 248, 248);
                color: #ffff;
                line-height: 1.4;
                display: flex;
                flex-direction: column;
                background: linear-gradient(93deg, #293264 19%, rgba(10, 133, 61, 1) 100%);

                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }

            .cabecalho {
                /* Posicionamento fixo no topo */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1000;

                /* Layout flexível */
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;

                /* Estilo visual */
                color: #fff;
                padding: 10px 30px;
            }


            .rodape {
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #fff;
                padding: 10px 30px;
                position: fixed;
                bottom: 0;
                width: 100%;
                justify-content: center;
                text-align: center;
            }

            main {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 55px;
                padding-bottom: 55px;
            }

            .card {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 50px;
                width: 100%;
                max-width: 500px;
                /* Limita a largura em telas grandes */
                margin: 60px;
                text-align: center;
                font-size: 20px;
                color: rgba(41, 50, 100, 1);
            }


            .mensagem {
                background-color: rgba(255, 255, 255, 0.76);
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            }

            .mensagem h1 {
                color: rgba(41, 50, 100, 1);
                font-size: 30px;

            }

            p {
                font-size: 1.25rem;
                margin-bottom: 20px;
                color: rgba(41, 50, 100, 1);
            }

            a {
                color: rgb(255, 255, 255);
                font-size: 1.1rem;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 5px;
                border: 2px solid rgb(255, 255, 255);
                transition: background 0.3s;
            }

            a:hover {
                background: rgb(255, 255, 255);
                color: #293264;
            }
        </style>
    </head>

    <body>

        <header class="cabecalho">
            <h1>Recepção SEAD</h1>
        </header>

        <main>

            <section class="mensagem">
                <h1>403 - Acesso Negado</h1><br>

                <p>Você não tem permissão para acessar esta área do sistema.</p><br>

                <a href="../Firewall/login.php">Voltar para o Início</a>
            </section>

        </main>


    </html>
<?php
    exit;
}
