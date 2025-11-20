<?php
// ----------------------------
// FUNÇÃO DE CONEXÃO PDO
// ----------------------------
function conectarBD() {
    $host = 'localhost';
    $db   = 'premier_league';
    $user = 'pw2';
    $pass = '1234';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        die("Erro ao conectar: " . $e->getMessage());
    }
}

// ----------------------------
// FUNÇÃO: BUSCAR EQUIPAS
// ----------------------------
function getEquipas($pdo) {
    $stmt = $pdo->query("SELECT id, nome, estadio, treinador FROM equipas ORDER BY nome ASC");
    return $stmt->fetchAll();
}

// ----------------------------
// FUNÇÃO: BUSCAR JORNADAS
// ----------------------------
function getJornadas($pdo) {
    $stmt = $pdo->query("SELECT id, numero, data_jornada FROM jornadas ORDER BY numero ASC");
    return $stmt->fetchAll();
}

// ----------------------------
// USO DAS FUNÇÕES
// ----------------------------
$pdo = conectarBD();

// Mostrar equipas
echo "<h2>Equipas da Premier League:</h2>";
$equipas = getEquipas($pdo);
echo "<ul>";
foreach ($equipas as $equipa) {
    echo "<li>ID: {$equipa['id']} | Nome: {$equipa['nome']} | Estádio: {$equipa['estadio']} | Treinador: {$equipa['treinador']}</li>";
}
echo "</ul>";

// Mostrar jornadas
echo "<h2>Jornadas:</h2>";
$jornadas = getJornadas($pdo);
echo "<ul>";
foreach ($jornadas as $jornada) {
    echo "<li>ID: {$jornada['id']} | Jornada: {$jornada['numero']} | Data: " . ($jornada['data_jornada'] ?? 'Não definida') . "</li>";
}
echo "</ul>";
?>
