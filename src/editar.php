<?php
require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao'] ?? '');

    $stmt = $pdo->prepare("UPDATE pokemons SET descricao = ? WHERE id = ?");
    $stmt->execute([$descricao, $id]);

    header('Location: index.php');
    exit;
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

            <div class="pokemon-fixo">
                <?php if (!empty($pokemon['imagem_url'])): ?>
                    <img class="sprite-preview" src="<?= htmlspecialchars($pokemon['imagem_url']) ?>" alt="<?= htmlspecialchars($pokemon['nome']) ?>">
                <?php endif; ?>
                <p><strong>Nome:</strong> <?= htmlspecialchars($pokemon['nome']) ?></p>
                <p><strong>Tipo:</strong> <?= htmlspecialchars($pokemon['tipo']) ?></p>
                <p><strong>Cadastrado em:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($pokemon['data_cadastro']))) ?></p>
                <p class="pokemon-fixo-nota">Esses dados vieram da PokeAPI no cadastro e não podem ser alterados.</p>
            </div>

            <form method="POST" action="editar.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($pokemon['id']) ?>">

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao"><?= htmlspecialchars($pokemon['descricao']) ?></textarea>

                <button type="submit">Salvar descrição</button>
            </form>

            <a class="btn-voltar" href="index.php">&larr; Voltar para a listagem</a>
        </div>
    </div>

</body>
</html>
