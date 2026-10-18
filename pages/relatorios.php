
<?php
require_once __DIR__ . '/../includes/db.php';

$data_inicial = $_GET['data_inicial'] ?? date('Y-m-d');
$data_final = $_GET['data_final'] ?? date('Y-m-d');

$stmt = $pdo->prepare(
    'SELECT f.id, p.nome AS produto, o.nome AS origem,
            d.nome AS destino, t.nome AS transportadora,
            f.data_frete, f.status
     FROM fretes f
     JOIN produtos p ON p.id = f.produto_id
     JOIN enderecos o ON o.id = f.origem_id
     JOIN enderecos d ON d.id = f.destino_id
     JOIN transportadoras t ON t.id = f.transportadora_id
     WHERE f.data_frete BETWEEN ? AND ?
     ORDER BY f.data_frete DESC, f.id DESC'
);
$stmt->execute([$data_inicial, $data_final]);
$fretes = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatórios | LogiFrete</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <h1>LogiFrete</h1>
    <p>Relatórios de fretes</p>
</header>

<main>
    <p><a class="botao" href="../index.php">← Voltar ao painel</a></p>

    <section class="painel">
        <h2>Relatório de fretes por período</h2>

        <form method="get">
            <div>
                <label for="data_inicial">Data inicial</label>
                <input type="date" id="data_inicial" name="data_inicial"
                       value="<?= h($data_inicial) ?>" required>
            </div>

            <div>
                <label for="data_final">Data final</label>
                <input type="date" id="data_final" name="data_final"
                       value="<?= h($data_final) ?>" required>
            </div>

            <button type="submit">Gerar relatório</button>
        </form>

        <p>Quantidade de fretes encontrados: <strong><?= count($fretes) ?></strong></p>

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
                <?php if (!$fretes): ?>
                    <tr>
                        <td colspan="7">Nenhum frete encontrado nesse período.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fretes as $frete): ?>
                        <tr>
                            <td><?= (int) $frete['id'] ?></td>
                            <td><?= h($frete['produto']) ?></td>
                            <td><?= h($frete['origem']) ?></td>
                            <td><?= h($frete['destino']) ?></td>
                            <td><?= h($frete['transportadora']) ?></td>
                            <td><?= date('d/m/Y', strtotime($frete['data_frete'])) ?></td>
                            <td><?= h($frete['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" onclick="window.print()">Imprimir / Salvar em PDF</button>
    </section>
</main>
</body>
</html>
