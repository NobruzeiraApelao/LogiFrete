
<?php
require_once __DIR__ . '/../includes/db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $peso = (float) ($_POST['peso'] ?? 0);

    if ($nome !== '') {
        $stmt = $pdo->prepare(
            'INSERT INTO produtos (nome, descricao, peso) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nome, $descricao, $peso]);
        $mensagem = 'Produto cadastrado com sucesso!';
    } else {
        $mensagem = 'Informe o nome do produto.';
    }
}

$produtos = $pdo->query(
    'SELECT * FROM produtos ORDER BY id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos | LogiFrete</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }
        header {
            background: #1e293b;
            padding: 24px 5%;
            border-bottom: 3px solid #38bdf8;
        }
        h1 { margin: 0; color: #38bdf8; }
        main { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .painel {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        label { display: block; margin: 14px 0 6px; }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #475569;
            border-radius: 6px;
            background: #0f172a;
            color: white;
        }
        button, .voltar {
            display: inline-block;
            margin-top: 18px;
            padding: 12px 18px;
            background: #0284c7;
            color: white;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .mensagem { color: #7dd3fc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #334155; }
        .tabela { overflow-x: auto; }
    </style>
</head>
<body>
<header>
    <h1>LogiFrete</h1>
    <p>Cadastro de produtos</p>
</header>

<main>
    <a class="voltar" href="../index.php">← Voltar ao painel</a>

    <section class="painel">
        <h2>Novo produto</h2>

        <?php if ($mensagem !== ''): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome do produto *</label>
            <input id="nome" name="nome" required maxlength="150">

            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="3"></textarea>

            <label for="peso">Peso (kg)</label>
            <input id="peso" name="peso" type="number" min="0" step="0.01" value="0">

            <button type="submit">Salvar produto</button>
        </form>
    </section>

    <section class="painel">
        <h2>Produtos cadastrados</h2>
        <div class="tabela">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Peso (kg)</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= (int) $produto['id'] ?></td>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= htmlspecialchars($produto['descricao'] ?? '') ?></td>
                        <td><?= htmlspecialchars((string) $produto['peso']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
