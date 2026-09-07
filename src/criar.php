<?php
require 'db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $imagem_url = trim($_POST['imagem_url'] ?? '');
    $data_cadastro = date('Y-m-d'); // sempre a data de hoje, definida pelo servidor

    if ($nome === '' || $tipo === '') {
        $erro = 'Busque um Pokémon na PokeAPI antes de salvar.';
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
                <label for="busca_pokeapi">Buscar Pokémon na PokeAPI</label>
                <div class="busca-api-linha">
                    <input type="text" id="busca_pokeapi" placeholder="Ex: pikachu ou 25">
                    <button type="button" id="btn_buscar_pokeapi">Buscar</button>
                </div>
                <p id="busca_status" class="busca-status">Busque um Pokémon para poder salvar.</p>
            </div>

            <div id="resultado_busca" class="resultado-busca" style="display:none;">
                <img id="sprite_preview" class="sprite-preview" alt="Sprite do Pokémon">
                <p><strong>Nome:</strong> <span id="resultado_nome"></span></p>
                <p><strong>Tipo:</strong> <span id="resultado_tipo"></span></p>
            </div>

            <form method="POST" action="criar.php">
                <input type="hidden" id="nome" name="nome" value="">
                <input type="hidden" id="tipo" name="tipo" value="">
                <input type="hidden" id="imagem_url" name="imagem_url" value="">

                <label for="descricao">Descrição (opcional)</label>
                <textarea id="descricao" name="descricao" placeholder="Escreva uma anotação sua sobre esse Pokémon"></textarea>

                <button type="submit" id="btn_salvar" disabled>Salvar na Pokedex</button>
            </form>

            <a class="btn-voltar" href="index.php">&larr; Voltar para a listagem</a>
        </div>
    </div>

    <script>
        document.getElementById('btn_buscar_pokeapi').addEventListener('click', async () => {
            const termo = document.getElementById('busca_pokeapi').value.trim().toLowerCase();
            const status = document.getElementById('busca_status');
            const resultado = document.getElementById('resultado_busca');
            const preview = document.getElementById('sprite_preview');
            const btnSalvar = document.getElementById('btn_salvar');

            if (!termo) {
                status.textContent = 'Digite um nome ou número (ex: pikachu, 25).';
                return;
            }

            status.textContent = 'Buscando...';
            resultado.style.display = 'none';
            btnSalvar.disabled = true;

            try {
                const resposta = await fetch(`https://pokeapi.co/api/v2/pokemon/${termo}`);

                if (!resposta.ok) {
                    status.textContent = 'Pokémon não encontrado na PokeAPI.';
                    return;
                }

                const dados = await resposta.json();

                const nomeFormatado = dados.name.charAt(0).toUpperCase() + dados.name.slice(1);
                const tipos = dados.types.map(t => t.type.name).join(', ');
                const sprite = dados.sprites.other['official-artwork'].front_default
                            || dados.sprites.front_default
                            || '';

                // Preenche os campos escondidos que serão realmente enviados no POST
                document.getElementById('nome').value = nomeFormatado;
                document.getElementById('tipo').value = tipos;
                document.getElementById('imagem_url').value = sprite;

                // Preenche o preview visível (somente leitura)
                document.getElementById('resultado_nome').textContent = nomeFormatado;
                document.getElementById('resultado_tipo').textContent = tipos;
                if (sprite) {
                    preview.src = sprite;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }

                resultado.style.display = 'block';
                btnSalvar.disabled = false;
                status.textContent = `${nomeFormatado} encontrado! Adicione uma descrição (se quiser) e salve.`;
            } catch (erro) {
                status.textContent = 'Erro ao conectar com a PokeAPI. Verifique sua internet.';
            }
        });
    </script>

</body>
</html>