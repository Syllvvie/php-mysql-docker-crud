<?php
// criar.php - Formulário de cadastro (Create) de um novo Pokémon
require 'db.php';

$erro = '';

// Se o formulário foi enviado via POST, processa o cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_cadastro = $_POST['data_cadastro'] ?? '';

    if ($nome === '' || $tipo === '' || $data_cadastro === '') {
        $erro = 'Preencha ao menos Nome, Tipo e Data de cadastro.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO pokemons (nome, tipo, descricao, data_cadastro) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$nome, $tipo, $descricao, $data_cadastro]);

        // Após salvar, volta para a listagem
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Pokémon</title>
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
            <h2>Novo Pokémon</h2>

            <?php if ($erro): ?>
                <div class="alerta"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="criar.php">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Ex: Pikachu"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">

                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" name="tipo" placeholder="Ex: Elétrico"
                       value="<?= htmlspecialchars($_POST['tipo'] ?? '') ?>">

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Uma breve descrição do Pokémon"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>

                <label for="data_cadastro">Data de cadastro</label>
                <input type="date" id="data_cadastro" name="data_cadastro"
                       value="<?= htmlspecialchars($_POST['data_cadastro'] ?? date('Y-m-d')) ?>">

                <button type="submit">Salvar na Pokedex</button>
            </form>

            <a class="btn-voltar" href="index.php">&larr; Voltar para a listagem</a>
        </div>
    </div>

</body>
</html>