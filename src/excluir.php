<?php
require 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php?excluido=1');
exit;