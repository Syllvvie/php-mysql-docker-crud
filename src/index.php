<?php
// index.php - Página de listagem (Read) de todos os Pokémons cadastrados
require 'db.php';

$stmt = $pdo->query("SELECT * FROM pokemons ORDER BY id ASC");
$pokemons = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pokedex CRUD</title>
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
            <h2>Pokémons Cadastrados</h2>

            <?php if (isset($_GET['excluido'])): ?>
                <div class="alerta">Pokémon removido da Pokedex.</div>
            <?php endif; ?>

            <?php if (count($pokemons) === 0): ?>
                <p class="vazio">Nenhum Pokémon cadastrado ainda. Que tal adicionar o primeiro?</p>
            <?php else: ?>
                <?php foreach ($pokemons as $p): ?>
                    <div class="pokemon-card">
                        <div class="pokemon-info">
                            <span class="indice">#<?= str_pad($p['id'], 3, '0', STR_PAD_LEFT) ?></span>
                            <strong><?= htmlspecialchars($p['nome']) ?></strong>
                            <span class="tipo-badge"><?= htmlspecialchars($p['tipo']) ?></span>
                            <p class="pokemon-desc"><?= htmlspecialchars($p['descricao']) ?></p>
                            <p class="pokemon-data">Cadastrado em: <?= htmlspecialchars(date('d/m/Y', strtotime($p['data_cadastro']))) ?></p>
                        </div>
                        <div class="acoes">
                            <a class="btn btn-editar" href="editar.php?id=<?= $p['id'] ?>">Editar</a>
                            <a class="btn btn-excluir" href="excluir.php?id=<?= $p['id'] ?>"
                               onclick="return confirm('Remover <?= htmlspecialchars($p['nome']) ?> da Pokedex?');">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a class="btn btn-add" href="criar.php" title="Adicionar Pokémon">+</a>

            <div class="dpad">
                <span class="botao-cinza"></span>
                <span class="botao-verde"></span>
                <span class="botao-vermelho"></span>
            </div>
        </div>
    </div>

</body>
</html>