
<?php
require_once __DIR__ . '/../includes/db.php';

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto = (int) ($_POST['produto_id'] ?? 0);
    $origem = (int) ($_POST['origem_id'] ?? 0);
    $destino = (int) ($_POST['destino_id'] ?? 0);
    $transportadora = (int) ($_POST['transportadora_id'] ?? 0);
    $data = $_POST['data_frete'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'Pendente';

    if ($produto && $origem && $destino && $transportadora && $data) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO fretes
                (produto_id, origem_id, destino_id, transportadora_id, data_frete, status)
                VALUES (?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $produto,
                $origem,
                $destino,
                $transportadora,
                $data,
                $status
            ]);

            $mensagem = 'Frete cadastrado com sucesso!';
        } catch (PDOException $e) {
            $erro = 'Não foi possível cadastrar o frete. Confira os dados e tente novamente.';
        }
    } else {
        $erro = 'Preencha todos os campos obrigatórios.';
    }
}

$produtos = $pdo->query(
    'SELECT id, nome FROM produtos ORDER BY nome'
)->fetchAll(PDO::FETCH_ASSOC);

$enderecos = $pdo->query(
    'SELECT id, nome, tipo FROM enderecos ORDER BY nome'
)->fetchAll(PDO::FETCH_ASSOC);

$transportadoras = $pdo->query(
    'SELECT id, nome FROM transportadoras ORDER BY nome'
)->fetchAll(PDO::FETCH_ASSOC);

$fretes = $pdo->query(
    'SELECT f.id, f.data_frete, f.status,
            p.nome AS produto,
            o.nome AS origem,
            d.nome AS destino,
            t.nome AS transportadora
     FROM fretes f
     JOIN produtos p ON p.id = f.produto_id
     JOIN enderecos o ON o.id = f.origem_id
     JOIN enderecos d ON d.id = f.destino_id
     JOIN transportadoras t ON t.id = f.transportadora_id
     ORDER BY f.id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fretes | LogiFrete</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header class="topo">
    <div class="topo-conteudo">
        <h1>LogiFrete</h1>
        <p>Gerenciamento de fretes</p>
    </div>
</header>

<main class="container">
    <a class="botao voltar" href="../index.php">← Voltar ao painel</a>

    <section class="painel">
        <h2>Novo frete</h2>

        <?php if ($mensagem): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="post" class="formulario">
            <div class="campo">
                <label for="produto_id">Produto *</label>
                <select id="produto_id" name="produto_id" required>
                    <option value="">Selecione um produto</option>
                    <?php foreach ($produtos as $item): ?>
                        <option value="<?= (int) $item['id'] ?>">
                            <?= htmlspecialchars($item['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="origem_id">Origem *</label>
                <select id="origem_id" name="origem_id" required>
                    <option value="">Selecione a origem</option>
                    <?php foreach ($enderecos as $item): ?>
                        <?php if (strtolower(trim($item['tipo'])) === 'origem'): ?>
                            <option value="<?= (int) $item['id'] ?>">
                                <?= htmlspecialchars($item['nome']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="destino_id">Destino *</label>
                <select id="destino_id" name="destino_id" required>
                    <option value="">Selecione o destino</option>
                    <?php foreach ($enderecos as $item): ?>
                        <?php if (strtolower(trim($item['tipo'])) === 'destino'): ?>
                            <option value="<?= (int) $item['id'] ?>">
                                <?= htmlspecialchars($item['nome']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="transportadora_id">Transportadora *</label>
                <select id="transportadora_id" name="transportadora_id" required>
                    <option value="">Selecione a transportadora</option>
                    <?php foreach ($transportadoras as $item): ?>
                        <option value="<?= (int) $item['id'] ?>">
                            <?= htmlspecialchars($item['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="data_frete">Data do frete *</label>
                <input
                    type="date"
                    id="data_frete"
                    name="data_frete"
                    value="<?= date('Y-m-d') ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Pendente">Pendente</option>
                    <option value="Em andamento">Em andamento</option>
                    <option value="Concluído">Concluído</option>
                </select>
            </div>

            <button class="botao" type="submit">Cadastrar frete</button>
        </form>
    </section>

    <section class="painel">
        <h2>Fretes registrados</h2>

        <div class="tabela">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Transportadora</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fretes)): ?>
                        <tr>
                            <td colspan="7">Nenhum frete cadastrado ainda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fretes as $frete): ?>
                            <tr>
                                <td><?= (int) $frete['id'] ?></td>
                                <td><?= htmlspecialchars($frete['produto']) ?></td>
                                <td><?= htmlspecialchars($frete['origem']) ?></td>
                                <td><?= htmlspecialchars($frete['destino']) ?></td>
                                <td><?= htmlspecialchars($frete['transportadora']) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($frete['data_frete'])) ?>
                                </td>
                                <td>
                                    <span class="status">
                                        <?= htmlspecialchars($frete['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
