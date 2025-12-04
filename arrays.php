<?php
// ----------------------------
// FUNÇÃO DE CONEXÃO PDO
// ----------------------------
function conectarBD() {
   /* $host = 'localhost';
    $db   = 'premier_league';
    $user = 'pw2';
    $pass = '1234';
    $charset = 'utf8mb4';*/

    $host = 'localhost';
    $db   = 'u506280443_josjoaDB';
    $user = 'u506280443_josjoadbUser';
    $pass = '7$&9N~8XpT';
    $charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Erro ao conectar: " . $e->getMessage());
    }
}

$pdo = conectarBD();

// --------------------------------------------
// 2. Buscar todas as equipas
// --------------------------------------------
$sqlEquipas = "SELECT id, nome, estadio, treinador FROM equipas ORDER BY id ASC";
$equipas = $pdo->query($sqlEquipas)->fetchAll();

// --------------------------------------------
// 3. Buscar todas as jornadas
// --------------------------------------------
$sqlJornadas = "SELECT id, numero, data_jornada FROM jornadas ORDER BY numero ASC";
$jornadas = $pdo->query($sqlJornadas)->fetchAll();

// --------------------------------------------
// 4. Buscar todos os jogos organizados por jornada
// --------------------------------------------
$sqlJogos = "
    SELECT 
        j.id AS id_jogo,
        j.id_jornada,
        j.data_jogo,
        ec.nome AS equipa_casa,
        ef.nome AS equipa_fora,
        r.golos_casa,
        r.golos_fora
    FROM jogos j
    INNER JOIN equipas ec ON j.equipa_casa = ec.id
    INNER JOIN equipas ef ON j.equipa_fora = ef.id
    LEFT JOIN resultados r ON j.id = r.id_jogo
    ORDER BY j.id_jornada ASC, j.id ASC
";

$stmt = $pdo->query($sqlJogos);
$jogos_raw = $stmt->fetchAll();

// Organizar jogos por jornada (1 → 38)
$jogos_por_jornada = [];
foreach ($jogos_raw as $j) {
    $jornada = $j["id_jornada"];
    if (!isset($jogos_por_jornada[$jornada])) {
        $jogos_por_jornada[$jornada] = [];
    }
    $jogos_por_jornada[$jornada][] = $j;
}

// --------------------------------------------
// 5. Criar ARRAY FINAL
// --------------------------------------------
$dados = [
    "equipas" => $equipas,
    "jornadas" => $jornadas,
    "jogos" => $jogos_por_jornada
];

// --------------------------------------------
// 6. Mostrar tudo em JSON (legível)
// --------------------------------------------
header("Content-Type: application/json; charset=utf-8");
echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);