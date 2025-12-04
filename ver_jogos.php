<?php
// ver_jogos.php

// ----------------------------
// Função de conexão PDO
// ----------------------------
function conectarBD() {
    /*$host = 'localhost';
    $db   = 'premier_league';
    $user = 'pw2';
    $pass = '1234';
    $charset = 'utf8mb4';*/

//ola
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

// ----------------------------
// Pegar o ID da jornada do GET
// ----------------------------
if (!isset($_GET['id_jornada']) || !is_numeric($_GET['id_jornada'])) {
    die("Jornada inválida.");
}
$id_jornada = (int)$_GET['id_jornada'];

// ----------------------------
// Buscar jogos da jornada selecionada
// ----------------------------
$sqlJogos = "
    SELECT 
        j.id AS id_jogo,
        ec.nome AS equipa_casa,
        ef.nome AS equipa_fora,
        r.golos_casa,
        r.golos_fora,
        j.data_jogo
    FROM jogos j
    INNER JOIN equipas ec ON j.equipa_casa = ec.id
    INNER JOIN equipas ef ON j.equipa_fora = ef.id
    LEFT JOIN resultados r ON j.id = r.id_jogo
    WHERE j.id_jornada = :id_jornada
    ORDER BY j.id ASC
";

$stmt = $pdo->prepare($sqlJogos);
$stmt->execute(['id_jornada' => $id_jornada]);
$jogos = $stmt->fetchAll();

// ----------------------------
// Buscar informações da jornada
// ----------------------------
$stmtJ = $pdo->prepare("SELECT numero, data_jornada FROM jornadas WHERE id = :id_jornada");
$stmtJ->execute(['id_jornada' => $id_jornada]);
$jornada_info = $stmtJ->fetch();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Jogos da Jornada <?= $jornada_info['numero'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f7f7f7; }
        h1 { text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
<h1>Jogos da Jornada <?= $jornada_info['numero'] ?> 📅 <?= $jornada_info['data_jornada'] ?></h1>

<table>
    <tr>
        <th>Equipa Casa</th>
        <th>Golos</th>
        <th>Equipa Fora</th>
        <th>Data do Jogo</th>
    </tr>
    <?php foreach ($jogos as $j): ?>
        <tr>
            <td><?= $j['equipa_casa'] ?></td>
            <td>
                <?= isset($j['golos_casa']) ? $j['golos_casa'] : '-' ?> :
                <?= isset($j['golos_fora']) ? $j['golos_fora'] : '-' ?>
            </td>
            <td><?= $j['equipa_fora'] ?></td>
            <td><?= $j['data_jogo'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
