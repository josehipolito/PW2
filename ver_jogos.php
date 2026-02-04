<?php
require 'config.php';
$pdo = ligarBD();

$id = (int)$_GET['id_jornada'];

/* Jornada */
$stmt = $pdo->prepare("SELECT numero FROM jornadas WHERE id=?");
$stmt->execute([$id]);
$jornada = $stmt->fetch();

/* Jogos com logos */
$jogos = $pdo->prepare("
    SELECT 
        ec.nome  AS casa,
        ec.logo  AS logo_casa,
        ef.nome  AS fora,
        ef.logo  AS logo_fora,
        r.golos_casa,
        r.golos_fora
    FROM jogos j
    JOIN equipas ec ON ec.id = j.equipa_casa
    JOIN equipas ef ON ef.id = j.equipa_fora
    LEFT JOIN resultados r ON r.id_jogo = j.id
    WHERE j.id_jornada = ?
");
$jogos->execute([$id]);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Jogos da Jornada</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter, sans-serif;
    color: white;
}

.container {
    max-width: 850px;
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
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,.15);
}

.team {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
}

.team img {
    width: 28px;
    height: 28px;
    object-fit: contain;
}
.result {
    font-weight: bold;
    font-size: 1.1rem;
}
</style>
</head>

<body>

<div class="container">
    <h1 style="text-align:center;">Jornada <?= $jornada['numero'] ?></h1>

    <table>
        <tr>
            <th>Casa</th>
            <th>Resultado</th>
            <th>Fora</th>
        </tr>

        <?php foreach ($jogos as $j): ?>
        <tr>
            <td>
                <div class="team">
                    <img src="imagens/<?= htmlspecialchars($j['logo_casa']) ?>" alt="<?= htmlspecialchars($j['casa']) ?>">
                    <?= htmlspecialchars($j['casa']) ?>
                </div>
            </td>

            <td class="result">
                <?= $j['golos_casa'] ?? '-' ?> : <?= $j['golos_fora'] ?? '-' ?>
            </td>

            <td>
                <div class="team">
                    <img src="imagens/<?= htmlspecialchars($j['logo_fora']) ?>" alt="<?= htmlspecialchars($j['fora']) ?>">
                    <?= htmlspecialchars($j['fora']) ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>

