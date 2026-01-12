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

/* INICIALIZAR CLASSIFICAÇÃO */
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
    margin: 0;
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(180deg, #37003c, #24002a);
    color: white;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: #4b0055;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.4);
}

.header {
    padding: 20px;
    font-size: 1.4rem;
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
    padding: 10px;
    background: #2a002e;
    font-weight: 600;
    color: #ddd;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

td.team {
    text-align: left;
    font-weight: 600;
}

/* ZONAS */
tr.champions { background: rgba(0, 123, 255, 0.15); }
tr.europa { background: rgba(0, 255, 133, 0.15); }
tr.relegation { background: rgba(255, 0, 0, 0.15); }

.position {
    font-weight: bold;
    opacity: 0.9;
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

        <?php $pos = 1; foreach ($classificacao as $c):
            $class = '';
            if ($pos <= 4) $class = 'champions';
            elseif ($pos <= 6) $class = 'europa';
            elseif ($pos > count($classificacao) - 3) $class = 'relegation';
        ?>
        <tr class="<?= $class ?>">
            <td class="position"><?= $pos ?></td>
            <td class="team"><?= htmlspecialchars($c['nome']) ?></td>
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
