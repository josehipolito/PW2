<?php
require 'config.php';
$pdo = ligarBD();

/* BUSCAR EQUIPAS */
$equipas = $pdo->query("SELECT id, nome, logo FROM equipas")->fetchAll();

/* INICIALIZAR CLASSIFICAÇÃO */
$classificacao = [];
foreach ($equipas as $e) {
    $classificacao[$e['id']] = [
        'nome' => $e['nome'],
        'logo' => $e['logo'],
        'j' => 0, 'v' => 0, 'e' => 0, 'd' => 0,
        'gm' => 0, 'gs' => 0, 'p' => 0
    ];
}

/* RESULTADOS */
$sql = "
SELECT j.equipa_casa, j.equipa_fora, r.golos_casa, r.golos_fora
FROM jogos j
JOIN resultados r ON r.id_jogo = j.id
";
$resultados = $pdo->query($sql)->fetchAll();

/* CALCULAR */
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
        $classificacao[$f]['d']++;
        $classificacao[$c]['p'] += 3;
    } elseif ($r['golos_casa'] < $r['golos_fora']) {
        $classificacao[$f]['v']++;
        $classificacao[$c]['d']++;
        $classificacao[$f]['p'] += 3;
    } else {
        $classificacao[$c]['e']++;
        $classificacao[$f]['e']++;
        $classificacao[$c]['p']++;
        $classificacao[$f]['p']++;
    }
}

/* ORDENAR */
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
    margin: 0;
    font-family: Inter, Arial, sans-serif;
    background: linear-gradient(180deg, #37003c, #24002a);
    color: white;
}

.container {
    max-width: 950px;
    margin: 40px auto;
    background: #4b0055;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.4);
}

.header {
    padding: 20px;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    background: #37003c;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th {
    padding: 12px;
    background: #2a002e;
    font-weight: 600;
    color: #ddd;
}

td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}

td.team {
    text-align: left;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* LOGOS */
td.team img {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: white;
    padding: 2px;
    object-fit: contain;
}

/* ZONAS UEFA */
tr.champions { background: rgba(0,123,255,0.25); }
tr.europa { background: rgba(0,255,133,0.25); }
tr.conference { background: rgba(0,255,133,0.15); }
tr.relegation { background: rgba(255,0,0,0.25); }

.position {
    font-weight: bold;
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

<?php
$pos = 1;
$total = count($classificacao);

foreach ($classificacao as $c):
    $class = '';
    if ($pos <= 4) $class = 'champions';
    elseif ($pos <= 6) $class = 'europa';
    elseif ($pos == 7) $class = 'conference';
    elseif ($pos > $total - 3) $class = 'relegation';
?>
<tr class="<?= $class ?>">
    <td class="position"><?= $pos ?></td>

    <td class="team">
        <img src="imagens/<?= htmlspecialchars($c['logo']) ?>" alt="">
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
