
<?php
require_once __DIR__ . '/../includes/db.php';

$mensagem = '';
$transportadoraEdicao = null;

// Atualizar transportadora
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($id > 0 && $nome !== '') {
        $stmt = $pdo->prepare(
            'UPDATE transportadoras SET nome = ?, cnpj = ?, telefone = ? WHERE id = ?'
        );
        $stmt->execute([$nome, $cnpj, $telefone, $id]);
        header('Location: transportadoras.php?mensagem=atualizada');
        exit;
    } else {
        $mensagem = 'Informe o nome da transportadora.';
    }
}

// Cadastrar transportadora
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['atualizar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome !== '') {
        $stmt = $pdo->prepare(
            'INSERT INTO transportadoras (nome, cnpj, telefone) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nome, $cnpj, $telefone]);
        header('Location: transportadoras.php?mensagem=cadastrada');
        exit;
    } else {
        $mensagem = 'Informe o nome da transportadora.';
    }
}

// Mensagens
if (isset($_GET['mensagem'])) {
    if ($_GET['mensagem'] === 'atualizada') {
        $mensagem = 'Transportadora atualizada com sucesso!';
    } elseif ($_GET['mensagem'] === 'cadastrada') {
        $mensagem = 'Transportadora cadastrada com sucesso!';
    }
}

// Buscar transportadora para editar
if (isset($_GET['editar'])) {
    $idEdicao = (int) $_GET['editar'];
    $stmt = $pdo->prepare('SELECT * FROM transportadoras WHERE id = ?');
    $stmt->execute([$idEdicao]);
    $transportadoraEdicao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transportadoraEdicao) {
        $mensagem = 'Transportadora não encontrada.';
    }
}

$transportadoras = $pdo->query(
    'SELECT * FROM transportadoras ORDER BY id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transportadoras | LogiFrete</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Arial, sans-serif; background: #0f172a; color: #f8fafc; }
header { background: #1e293b; padding: 24px 5%; border-bottom: 3px solid #38bdf8; }
h1 { margin: 0; color: #38bdf8; }
main { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
.painel { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
label { display: block; margin: 14px 0 6px; }
input { width: 100%; padding: 12px; border: 1px solid #475569; border-radius: 6px; background: #0f172a; color: white; }
button, .voltar, .editar, .cancelar { display: inline-block; margin-top: 18px; padding: 12px 18px; background: #0284c7; color: white; border: 0; border-radius: 6px; cursor: pointer; text-decoration: none; }
.cancelar { background: #475569; }
.mensagem { color: #7dd3fc; }
.tabela { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #334155; }
</style>
</head>
<body>
<header>
    <h1>LogiFrete</h1>
    <p>Cadastro de transportadoras</p>
</header>
<main>
    <a class="voltar" href="../index.php">← Voltar ao painel</a>

    <section class="painel">
        <h2><?= $transportadoraEdicao ? 'Editar transportadora' : 'Nova transportadora' ?></h2>

        <?php if ($mensagem !== ''): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="post">
            <?php if ($transportadoraEdicao): ?>
                <input type="hidden" name="id" value="<?= (int) $transportadoraEdicao['id'] ?>">
            <?php endif; ?>

            <label for="nome">Nome da transportadora *</label>
            <input id="nome" name="nome" required maxlength="150"
                value="<?= htmlspecialchars($transportadoraEdicao['nome'] ?? '') ?>">

            <label for="cnpj">CNPJ</label>
            <input id="cnpj" name="cnpj" maxlength="18" placeholder="00.000.000/0000-00"
                value="<?= htmlspecialchars($transportadoraEdicao['cnpj'] ?? '') ?>">

            <label for="telefone">Telefone</label>
            <input id="telefone" name="telefone" maxlength="30" placeholder="(44) 99999-9999"
                value="<?= htmlspecialchars($transportadoraEdicao['telefone'] ?? '') ?>">

            <?php if ($transportadoraEdicao): ?>
                <button type="submit" name="atualizar" value="1">Atualizar transportadora</button>
                <a class="cancelar" href="transportadoras.php">Cancelar edição</a>
            <?php else: ?>
                <button type="submit">Salvar transportadora</button>
            <?php endif; ?>
        </form>
    </section>

    <section class="painel">
        <h2>Transportadoras cadastradas</h2>
        <div class="tabela">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>CNPJ</th><th>Telefone</th><th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transportadoras as $transportadora): ?>
                    <tr>
                        <td><?= (int) $transportadora['id'] ?></td>
                        <td><?= htmlspecialchars($transportadora['nome']) ?></td>
                        <td><?= htmlspecialchars($transportadora['cnpj'] ?? '') ?></td>
                        <td><?= htmlspecialchars($transportadora['telefone'] ?? '') ?></td>
                        <td>
                            <a class="editar" href="transportadoras.php?editar=<?= (int) $transportadora['id'] ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
