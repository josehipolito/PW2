<?php
function conectarBD() {
    return new PDO(
        "mysql:host=localhost;dbname=u506280443_josjoaDB;charset=utf8mb4",
        "u506280443_josjoadbUser",
        "7$&9N~8XpT",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

$pdo = conectarBD();
$id = (int)$_GET['id_jornada'];

$jornada = $pdo->prepare("SELECT numero FROM jornadas WHERE id=?");
$jornada->execute([$id]);
$jornada = $jornada->fetch();

$jogos = $pdo->prepare("
SELECT ec.nome casa, ef.nome fora, r.golos_casa, r.golos_fora
FROM jogos j
JOIN equipas ec ON ec.id=j.equipa_casa
JOIN equipas ef ON ef.id=j.equipa_fora
LEFT JOIN resultados r ON r.id_jogo=j.id
WHERE j.id_jornada=?
");
$jogos->execute([$id]);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Jogos</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter;
    color: white;
}

.container {
    max-width: 800px;
    margin: 40px auto;
    background: #4b0055;
    padding: 30px;
    border-radius: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,.15);
}
</style>
</head>

<body>

<div class="container">
<h1>Jornada <?= $jornada['numero'] ?></h1>

<table>
<tr>
    <th>Casa</th>
    <th>Resultado</th>
    <th>Fora</th>
</tr>

<?php foreach ($jogos as $j): ?>
<tr>
    <td><?= $j['casa'] ?></td>
    <td><?= $j['golos_casa'] ?? '-' ?> : <?= $j['golos_fora'] ?? '-' ?></td>
    <td><?= $j['fora'] ?></td>
</tr>
<?php endforeach; ?>

</table>
</div>

</body>
</html>
