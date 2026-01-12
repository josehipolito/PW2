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
$jornadas = $pdo->query("SELECT id, numero, data_jornada FROM jornadas ORDER BY numero")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Jornadas</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter;
    color: white;
}

.container {
    max-width: 700px;
    margin: 40px auto;
}

.jornada {
    background: #4b0055;
    padding: 20px;
    border-radius: 14px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

a {
    background: #00ff85;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: black;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">
<h1 style="text-align:center;">📅 Jornadas</h1>

<?php foreach ($jornadas as $j): ?>
<div class="jornada">
    <div>
        <strong>Jornada <?= $j['numero'] ?></strong><br>
        <?= $j['data_jornada'] ?>
    </div>
    <a href="ver_jogos.php?id_jornada=<?= $j['id'] ?>">Ver jogos</a>
</div>
<?php endforeach; ?>

</div>
</body>
</html>
