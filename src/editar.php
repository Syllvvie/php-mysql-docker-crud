<?php
require 'db.php';

$erro = '';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_cadastro = $_POST['data_cadastro'] ?? '';

    if ($nome === '' || $tipo === '' || $data_cadastro === '') {
        $erro = 'Preencha ao menos Nome, Tipo e Data de cadastro.';
        $pokemon = [
            'id' => $id,
            'nome' => $nome,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'data_cadastro' => $data_cadastro,
        ];
    } else {
        $stmt = $pdo->prepare(
            "UPDATE pokemons SET nome = ?, tipo = ?, descricao = ?, data_cadastro = ? WHERE id = ?"
        );
        $stmt->execute([$nome, $tipo, $descricao, $data_cadastro, $id]);

        header('Location: index.php');
        exit;
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
    $pokemon = $stmt->fetch();

    if (!$pokemon) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pokémon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="pokedex">
        <div class="pokedex-header">
            <div class="lente-azul"></div>
            <div class="luzinhas">
                <span class="luz-vermelha"></span>
                <span class="luz-amarela"></span>
                <span class="luz-verde"></span>
            </div>
            <span class="titulo-pokedex">POKEDEX</span>
        </div>

        <div class="tela">
            <h2>Editar Pokémon</h2>

            <?php if ($erro): ?>
                <div class="alerta"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="editar.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($pokemon['id']) ?>">

                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($pokemon['nome']) ?>">

                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" name="tipo" value="<?= htmlspecialchars($pokemon['tipo']) ?>">

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao"><?= htmlspecialchars($pokemon['descricao']) ?></textarea>

                <label for="data_cadastro">Data de cadastro</label>
                <input type="date" id="data_cadastro" name="data_cadastro"
                       value="<?= htmlspecialchars($pokemon['data_cadastro']) ?>">

                <button type="submit">Atualizar Pokémon</button>
            </form>

            <a class="btn-voltar" href="index.php">&larr; Voltar para a listagem</a>
        </div>
    </div>

</body>
</html>