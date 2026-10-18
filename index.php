
<?php
require_once __DIR__ . '/includes/db.php';

$pagina = 'LogiFrete';

$quantidadeProdutos = (int) $pdo->query(
    'SELECT COUNT(*) FROM produtos'
)->fetchColumn();

$quantidadeTransportadoras = (int) $pdo->query(
    'SELECT COUNT(*) FROM transportadoras'
)->fetchColumn();

$quantidadeFretes = (int) $pdo->query(
    'SELECT COUNT(*) FROM fretes'
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pagina) ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        main { padding: 35px; }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin: 24px 0;
        }
        .card {
            display: block;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            color: #f8fafc;
            text-decoration: none;
            transition: transform .15s, border-color .15s;
        }
        .card:hover {
            transform: translateY(-3px);
            border-color: #38bdf8;
        }
        .card h2 { color: #38bdf8; margin-top: 0; }
        .number { font-size: 32px; font-weight: bold; }
        .atalhos {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
<header>
    <h1>LogiFrete</h1>
    <p>Gerenciador Logístico</p>
</header>

<main>
    <h2>Painel inicial</h2>
    <p>Bem-vindo ao sistema de gerenciamento de fretes.</p>

    <div class="cards">
        <a class="card" href="pages/produtos.php">
            <h2>Produtos</h2>
            <p class="number"><?= $quantidadeProdutos ?></p>
            <p>Produtos cadastrados</p>
        </a>

        <a class="card" href="pages/transportadoras.php">
            <h2>Transportadoras</h2>
            <p class="number"><?= $quantidadeTransportadoras ?></p>
            <p>Transportadoras cadastradas</p>
        </a>

        <a class="card" href="pages/fretes.php">
            <h2>Fretes</h2>
            <p class="number"><?= $quantidadeFretes ?></p>
            <p>Fretes registrados</p>
        </a>

        <a class="card" href="pages/enderecos.php">
            <h2>Endereços</h2>
            <p>Cadastre origens e destinos.</p>
        </a>

        <a class="card" href="pages/relatorios.php">
            <h2>Relatórios</h2>
            <p>Consulte fretes por período e imprima os resultados.</p>
        </a>
    </div>

    <h2>Acesso rápido</h2>
    <div class="atalhos">
        <a class="botao" href="pages/produtos.php">Cadastrar produto</a>
        <a class="botao" href="pages/enderecos.php">Cadastrar endereço</a>
        <a class="botao" href="pages/transportadoras.php">Cadastrar transportadora</a>
        <a class="botao" href="pages/fretes.php">Cadastrar frete</a>
        <a class="botao" href="pages/relatorios.php">Ver relatórios</a>
    </div>
</main>
</body>
</html>
