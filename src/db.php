<?php
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'rootpassword';
$dbname = getenv('DB_NAME') ?: 'pokedex';

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pokemons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            tipo VARCHAR(50) NOT NULL,
            descricao TEXT,
            data_cadastro DATE NOT NULL,
            imagem_url VARCHAR(255)
        )
    ");

} catch (PDOException $e) {
    die("
        <div style='font-family:sans-serif;text-align:center;margin-top:50px;'>
            <h2>Não foi possível conectar ao banco de dados</h2>
            <p>Verifique se o container do banco está de pé e tente recarregar a página em alguns segundos.</p>
            <p style='color:#888;'>Detalhe técnico: " . htmlspecialchars($e->getMessage()) . "</p>
        </div>
    ");
}
