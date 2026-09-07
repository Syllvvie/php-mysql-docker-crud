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
    $imagem_url = trim($_POST['imagem_url'] ?? '');

    if ($nome === '' || $tipo === '' || $data_cadastro === '') {
        $erro = 'Preencha ao menos Nome, Tipo e Data de cadastro.';
        $pokemon = [
            'id' => $id,
            'nome' => $nome,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'data_cadastro' => $data_cadastro,
            'imagem_url' => $imagem_url,
        ];
    } else {
        $stmt = $pdo->prepare(
            "UPDATE pokemons SET nome = ?, tipo = ?, descricao = ?, data_cadastro = ?, imagem_url = ? WHERE id = ?"
        );
        $stmt->execute([$nome, $tipo, $descricao, $data_cadastro, $imagem_url ?: null, $id]);

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

            <div class="busca-api">
                <label for="busca_pokeapi">Buscar na PokeAPI (opcional, sobrescreve os campos abaixo)</label>
                <div class="busca-api-linha">
                    <input type="text" id="busca_pokeapi" placeholder="Ex: pikachu ou 25">
                    <button type="button" id="btn_buscar_pokeapi">Buscar</button>
                </div>
                <p id="busca_status" class="busca-status"></p>
            </div>

            <img id="sprite_preview" class="sprite-preview"
                 style="<?= empty($pokemon['imagem_url']) ? 'display:none;' : '' ?>"
                 src="<?= htmlspecialchars($pokemon['imagem_url'] ?? '') ?>" alt="Sprite do Pokémon">

            <form method="POST" action="editar.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($pokemon['id']) ?>">
                <input type="hidden" id="imagem_url" name="imagem_url" value="<?= htmlspecialchars($pokemon['imagem_url'] ?? '') ?>">

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

    <script>
        document.getElementById('btn_buscar_pokeapi').addEventListener('click', async () => {
            const termo = document.getElementById('busca_pokeapi').value.trim().toLowerCase();
            const status = document.getElementById('busca_status');
            const preview = document.getElementById('sprite_preview');

            if (!termo) {
                status.textContent = 'Digite um nome ou número (ex: pikachu, 25).';
                return;
            }

            status.textContent = 'Buscando...';

            try {
                const resposta = await fetch(`https://pokeapi.co/api/v2/pokemon/${termo}`);

                if (!resposta.ok) {
                    status.textContent = 'Pokémon não encontrado na PokeAPI.';
                    return;
                }

                const dados = await resposta.json();

                document.getElementById('nome').value =
                    dados.name.charAt(0).toUpperCase() + dados.name.slice(1);

                const tipos = dados.types.map(t => t.type.name);
                document.getElementById('tipo').value = tipos.join(', ');

                const sprite = dados.sprites.other['official-artwork'].front_default
                            || dados.sprites.front_default
                            || '';
                document.getElementById('imagem_url').value = sprite;

                if (sprite) {
                    preview.src = sprite;
                    preview.style.display = 'block';
                }

                status.textContent = `${dados.name} encontrado! Revise os dados e atualize.`;
            } catch (erro) {
                status.textContent = 'Erro ao conectar com a PokeAPI. Verifique sua internet.';
            }
        });
    </script>

</body>
</html>