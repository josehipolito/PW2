<?php
require 'config.php';
$pdo = ligarBD();

/* ===============================
   BUSCAR EQUIPAS
================================ */
$equipas = $pdo->query(
    "SELECT id, nome, logo FROM equipas ORDER BY nome"
)->fetchAll();

/* ===============================
   INICIALIZAR CLASSIFICAÇÃO
================================ */
$classificacao = [];

foreach ($equipas as $e) {
    $classificacao[$e['id']] = [
        'id'   => $e['id'],
        'nome' => $e['nome'],
        'logo' => $e['logo'],
        'j' => 0, 'v' => 0, 'e' => 0, 'd' => 0,
        'gm' => 0, 'gs' => 0, 'p' => 0
    ];
}

/* ===============================
   BUSCAR RESULTADOS
================================ */
$resultados = $pdo->query("
    SELECT equipa_casa, equipa_fora, golos_casa, golos_fora
    FROM jogos j
    INNER JOIN resultados r ON r.id_jogo = j.id
")->fetchAll();

/* ===============================
   CALCULAR CLASSIFICAÇÃO
================================ */
foreach ($resultados as $r) {
    $c = $r['equipa_casa'];
    $f = $r['equipa_fora'];

    $classificacao[$c]['j']++;
    $classificacao[$f]['j']++;

    $classificacao[$c]['gm'] += $r['golos_casa'];
    $classificacao[$c]['gs'] += $r['golos_fora'];
    $classificacao[$f]['gm'] += $r['golos_fora'];
    $classificacao[$f]['gs'] += $r['golos_casa'];

    if ($r['golos_casa'] > $r['golos_fora']) {
        $classificacao[$c]['v']++;
        $classificacao[$c]['p'] += 3;
        $classificacao[$f]['d']++;
    } elseif ($r['golos_casa'] < $r['golos_fora']) {
        $classificacao[$f]['v']++;
        $classificacao[$f]['p'] += 3;
        $classificacao[$c]['d']++;
    } else {
        $classificacao[$c]['e']++;
        $classificacao[$f]['e']++;
        $classificacao[$c]['p']++;
        $classificacao[$f]['p']++;
    }
}

/* ===============================
   ORDENAR
================================ */
$classificacao = array_values($classificacao);

usort($classificacao, function ($a, $b) {
    $dgA = $a['gm'] - $a['gs'];
    $dgB = $b['gm'] - $b['gs'];

    return $b['p'] <=> $a['p']
        ?: $dgB <=> $dgA
        ?: $b['gm'] <=> $a['gm'];
});
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Classificação</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter, Arial;
    color: white;
}
.container {
    max-width: 900px;
    margin: 40px auto;
    background: #4b0055;
    border-radius: 16px;
    overflow: hidden;
}
.header {
    padding: 20px;
    text-align: center;
    font-size: 22px;
    font-weight: bold;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,.15);
}
.team {
    text-align: left;
    display: flex;
    align-items: center;
    gap: 10px;
}
.team img {
    width: 26px;
    height: 26px;
}
</style>
</head>

<body>

<div class="container">
<div class="header">📊 Classificação Premier League</div>

<table>
<tr>
    <th>#</th>
    <th>Equipa</th>
    <th>J</th>
    <th>V</th>
    <th>E</th>
    <th>D</th>
    <th>GM</th>
    <th>GS</th>
    <th>DG</th>
    <th>P</th>
</tr>

<?php $pos=1; foreach ($classificacao as $c): ?>
<tr>
    <td><?= $pos ?></td>
    <td class="team">
        <img src="imagens/<?= $c['logo'] ?>" alt="">
        <?= htmlspecialchars($c['nome']) ?>
    </td>
    <td><?= $c['j'] ?></td>
    <td><?= $c['v'] ?></td>
    <td><?= $c['e'] ?></td>
    <td><?= $c['d'] ?></td>
    <td><?= $c['gm'] ?></td>
    <td><?= $c['gs'] ?></td>
    <td><?= $c['gm'] - $c['gs'] ?></td>
    <td><strong><?= $c['p'] ?></strong></td>
</tr>
<?php $pos++; endforeach; ?>
</table>
</div>

</body>
</html>
