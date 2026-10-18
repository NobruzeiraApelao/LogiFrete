
<?php
require_once __DIR__ . '/../includes/db.php';

$mensagem = '';
$enderecoEdicao = null;

// Atualizar endereço
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $rua = trim($_POST['rua'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));
    $tipo = $_POST['tipo'] ?? '';

    if ($id > 0 && $nome !== '' && in_array($tipo, ['Origem', 'Destino'], true)) {
        $stmt = $pdo->prepare(
            'UPDATE enderecos
             SET nome = ?, cep = ?, rua = ?, numero = ?, cidade = ?, uf = ?, tipo = ?
             WHERE id = ?'
        );
        $stmt->execute([$nome, $cep, $rua, $numero, $cidade, $uf, $tipo, $id]);
        header('Location: enderecos.php?mensagem=atualizado');
        exit;
    } else {
        $mensagem = 'Preencha o nome e selecione o tipo de endereço.';
    }
}

// Cadastrar endereço
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['atualizar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $rua = trim($_POST['rua'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));
    $tipo = $_POST['tipo'] ?? '';

    if ($nome !== '' && in_array($tipo, ['Origem', 'Destino'], true)) {
        $stmt = $pdo->prepare(
            'INSERT INTO enderecos (nome, cep, rua, numero, cidade, uf, tipo)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nome, $cep, $rua, $numero, $cidade, $uf, $tipo]);
        header('Location: enderecos.php?mensagem=cadastrado');
        exit;
    } else {
        $mensagem = 'Preencha o nome e selecione o tipo de endereço.';
    }
}

// Mensagens após salvar
if (isset($_GET['mensagem'])) {
    if ($_GET['mensagem'] === 'atualizado') {
        $mensagem = 'Endereço atualizado com sucesso!';
    } elseif ($_GET['mensagem'] === 'cadastrado') {
        $mensagem = 'Endereço cadastrado com sucesso!';
    }
}

// Buscar endereço para editar
if (isset($_GET['editar'])) {
    $idEdicao = (int) $_GET['editar'];
    $stmt = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
    $stmt->execute([$idEdicao]);
    $enderecoEdicao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$enderecoEdicao) {
        $mensagem = 'Endereço não encontrado.';
    }
}

$enderecos = $pdo->query(
    'SELECT * FROM enderecos ORDER BY id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Endereços | LogiFrete</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Arial, sans-serif; background: #0f172a; color: #f8fafc; }
header { background: #1e293b; padding: 24px 5%; border-bottom: 3px solid #38bdf8; }
h1 { margin: 0; color: #38bdf8; }
main { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
.painel { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
label { display: block; margin: 14px 0 6px; }
input, select { width: 100%; padding: 12px; border: 1px solid #475569; border-radius: 6px; background: #0f172a; color: white; }
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
    <p>Cadastro de endereços</p>
</header>
<main>
    <a class="voltar" href="../index.php">← Voltar ao painel</a>

    <section class="painel">
        <h2><?= $enderecoEdicao ? 'Editar endereço' : 'Novo endereço' ?></h2>

        <?php if ($mensagem !== ''): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="post">
            <?php if ($enderecoEdicao): ?>
                <input type="hidden" name="id" value="<?= (int) $enderecoEdicao['id'] ?>">
            <?php endif; ?>

            <label for="nome">Nome do endereço *</label>
            <input id="nome" name="nome" required maxlength="150"
                value="<?= htmlspecialchars($enderecoEdicao['nome'] ?? '') ?>">

            <label for="cep">CEP</label>
            <input id="cep" name="cep" maxlength="10"
                value="<?= htmlspecialchars($enderecoEdicao['cep'] ?? '') ?>">

            <label for="rua">Rua</label>
            <input id="rua" name="rua" maxlength="150"
                value="<?= htmlspecialchars($enderecoEdicao['rua'] ?? '') ?>">

            <label for="numero">Número</label>
            <input id="numero" name="numero" maxlength="20"
                value="<?= htmlspecialchars($enderecoEdicao['numero'] ?? '') ?>">

            <label for="cidade">Cidade</label>
            <input id="cidade" name="cidade" maxlength="100"
                value="<?= htmlspecialchars($enderecoEdicao['cidade'] ?? '') ?>">

            <label for="uf">Estado (UF)</label>
            <input id="uf" name="uf" maxlength="2" placeholder="PR"
                value="<?= htmlspecialchars($enderecoEdicao['uf'] ?? '') ?>">

            <label for="tipo">Tipo de endereço *</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecione...</option>
                <option value="Origem" <?= ($enderecoEdicao['tipo'] ?? '') === 'Origem' ? 'selected' : '' ?>>Origem</option>
                <option value="Destino" <?= ($enderecoEdicao['tipo'] ?? '') === 'Destino' ? 'selected' : '' ?>>Destino</option>
            </select>

            <?php if ($enderecoEdicao): ?>
                <button type="submit" name="atualizar" value="1">Atualizar endereço</button>
                <a class="cancelar" href="enderecos.php">Cancelar edição</a>
            <?php else: ?>
                <button type="submit">Salvar endereço</button>
            <?php endif; ?>
        </form>
    </section>

    <section class="painel">
        <h2>Endereços cadastrados</h2>
        <div class="tabela">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>CEP</th><th>Rua</th>
                        <th>Número</th><th>Cidade</th><th>UF</th><th>Tipo</th><th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($enderecos as $endereco): ?>
                    <tr>
                        <td><?= (int) $endereco['id'] ?></td>
                        <td><?= htmlspecialchars($endereco['nome']) ?></td>
                        <td><?= htmlspecialchars($endereco['cep'] ?? '') ?></td>
                        <td><?= htmlspecialchars($endereco['rua'] ?? '') ?></td>
                        <td><?= htmlspecialchars($endereco['numero'] ?? '') ?></td>
                        <td><?= htmlspecialchars($endereco['cidade'] ?? '') ?></td>
                        <td><?= htmlspecialchars($endereco['uf'] ?? '') ?></td>
                        <td><?= htmlspecialchars($endereco['tipo']) ?></td>
                        <td><a class="editar" href="enderecos.php?editar=<?= (int) $endereco['id'] ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
