<?php
function ligarBD($host, $db, $user, $pass, $charset) {
    return new PDO(
        "mysql:host=$host;dbname=$db;charset=$charset",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}

/* BASE DE DADOS */
$pdo = ligarBD(
    'localhost',
    'u506280443_josjoaDB',
    'u506280443_josjoadbUser',
    '7$&9N~8XpT',
    'utf8mb4'
);
/*$pdo = ligarBD(
    $host = 'localhost';
    $db   = 'premier_league';
    $user = 'pw2';
    $pass = '1234';
    $charset = 'utf8mb4';
);*/

/* EQUIPAS */
$equipas = $pdo->query("SELECT id, nome FROM equipas")->fetchAll();

/* INICIALIZAR */
$classificacao = [];
foreach ($equipas as $e) {
    $classificacao[$e['id']] = [
        'nome' => $e['nome'],
        'j' => 0, 'v' => 0, 'e' => 0, 'd' => 0,
        'gm' => 0, 'gs' => 0, 'p' => 0
    ];
}

/* RESULTADOS */
$sql = "
SELECT j.equipa_casa, j.equipa_fora, r.golos_casa, r.golos_fora
FROM jogos j
INNER JOIN resultados r ON j.id = r.id_jogo
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
    font-family: Arial, sans-serif;
    background: #eef0f3;
    padding: 30px;
}
h1 {
    text-align: center;
    margin-bottom: 20px;
}
table {
    width: 100%;
    max-width: 1000px;
    margin: auto;
    border-collapse: collapse;
    background: #fff;
    font-size: 14px;
}
th {
    background: #222;
    color: #fff;
    padding: 8px;
    font-weight: normal;
}
td {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
td.team {
    text-align: left;
    font-weight: bold;
}
tr.champions { background: #e6f2ff; }
tr.europa { background: #f2f7e6; }
tr.relegation { background: #ffe6e6; }
</style>
</head>

<body>

<h1>📊 Classificação</h1>

<table>
<tr>
    <th>#</th>
    <th>Equipa</th>
    <th>J</th>
    <th>V</th>
    <th>E</th>
    <th>D</th>
    <th>GM:GS</th>
    <th>DG</th>
    <th>P</th>
</tr>

<?php $pos = 1; foreach ($classificacao as $c): 
    $class = '';
    if ($pos <= 4) $class = 'champions';
    elseif ($pos <= 6) $class = 'europa';
    elseif ($pos > count($classificacao) - 3) $class = 'relegation';
?>
<tr class="<?= $class ?>">
    <td><?= $pos ?></td>
    <td class="team"><?= htmlspecialchars($c['nome']) ?></td>
    <td><?= $c['j'] ?></td>
    <td><?= $c['v'] ?></td>
    <td><?= $c['e'] ?></td>
    <td><?= $c['d'] ?></td>
    <td><?= $c['gm'] ?>:<?= $c['gs'] ?></td>
    <td><?= $c['gm'] - $c['gs'] ?></td>
    <td><strong><?= $c['p'] ?></strong></td>
</tr>
<?php $pos++; endforeach; ?>

</table>

</body>
</html>
