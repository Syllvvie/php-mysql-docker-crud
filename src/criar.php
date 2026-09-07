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
    $imagem_url = trim($_POST['imagem_url'] ?? '');

    if ($nome === '' || $tipo === '' || $data_cadastro === '') {
        $erro = 'Preencha ao menos Nome, Tipo e Data de cadastro.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO pokemons (nome, tipo, descricao, data_cadastro, imagem_url) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nome, $tipo, $descricao, $data_cadastro, $imagem_url ?: null]);

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

            <div class="busca-api">
                <label for="busca_pokeapi">Buscar na PokeAPI</label>
                <div class="busca-api-linha">
                    <input type="text" id="busca_pokeapi" placeholder="Ex: pikachu ou 25">
                    <button type="button" id="btn_buscar_pokeapi">Buscar</button>
                </div>
                <p id="busca_status" class="busca-status"></p>
            </div>

            <img id="sprite_preview" class="sprite-preview" style="display:none;" alt="Sprite do Pokémon">

            <form method="POST" action="criar.php">
                <input type="hidden" id="imagem_url" name="imagem_url" value="<?= htmlspecialchars($_POST['imagem_url'] ?? '') ?>">

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

    <script>
        // Busca dados de um Pokémon na PokeAPI (https://pokeapi.co) e
        // preenche automaticamente nome, tipo e imagem do formulário.
        document.getElementById('btn_buscar_pokeapi').addEventListener('click', async () => {
            const termo = document.getElementById('busca_pokeapi').value.trim().toLowerCase();
            const status = document.getElementById('busca_status');
            const preview = document.getElementById('sprite_preview');

            if (!termo) {
                status.textContent = 'Digite um nome ou número (ex: pikachu, 25).';
                return;
            }

            status.textContent = 'Buscando...';
            preview.style.display = 'none';

            try {
                const resposta = await fetch(`https://pokeapi.co/api/v2/pokemon/${termo}`);

                if (!resposta.ok) {
                    status.textContent = 'Pokémon não encontrado na PokeAPI.';
                    return;
                }

                const dados = await resposta.json();

                // Nome (com a primeira letra maiúscula)
                document.getElementById('nome').value =
                    dados.name.charAt(0).toUpperCase() + dados.name.slice(1);

                // Tipos (a PokeAPI retorna uma lista, ex: ["electric"])
                const tipos = dados.types.map(t => t.type.name);
                document.getElementById('tipo').value = tipos.join(', ');

                // Sprite (imagem oficial, com fallback para o sprite padrão)
                const sprite = dados.sprites.other['official-artwork'].front_default
                            || dados.sprites.front_default
                            || '';
                document.getElementById('imagem_url').value = sprite;

                if (sprite) {
                    preview.src = sprite;
                    preview.style.display = 'block';
                }

                status.textContent = `${dados.name} encontrado! Revise os dados e salve.`;
            } catch (erro) {
                status.textContent = 'Erro ao conectar com a PokeAPI. Verifique sua internet.';
            }
        });
    </script>

</body>
</html>