<?php
function ligarBD() {
    return new PDO(
        "mysql:host=localhost;dbname=u506280443_josjoaDB;charset=utf8mb4",
        "u506280443_josjoadbUser",
        "7$&9N~8XpT",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}

$pdo = ligarBD();

/* EQUIPAS */
$equipas = $pdo->query("SELECT id, nome, logo FROM equipas")->fetchAll();

$classificacao = [];
foreach ($equipas as $e) {
    $classificacao[$e['id']] = [
        'nome' => $e['nome'],
        'logo' => $e['logo'],
        'j'=>0,'v'=>0,'e'=>0,'d'=>0,'gm'=>0,'gs'=>0,'p'=>0
    ];
}

/* RESULTADOS */
$resultados = $pdo->query("
SELECT j.equipa_casa, j.equipa_fora, r.golos_casa, r.golos_fora
FROM jogos j
JOIN resultados r ON r.id_jogo=j.id
")->fetchAll();

foreach ($resultados as $r) {
    $c=$r['equipa_casa']; $f=$r['equipa_fora'];

    $classificacao[$c]['j']++;
    $classificacao[$f]['j']++;

    $classificacao[$c]['gm']+=$r['golos_casa'];
    $classificacao[$c]['gs']+=$r['golos_fora'];
    $classificacao[$f]['gm']+=$r['golos_fora'];
    $classificacao[$f]['gs']+=$r['golos_casa'];

    if ($r['golos_casa']>$r['golos_fora']) {
        $classificacao[$c]['v']++; $classificacao[$c]['p']+=3;
        $classificacao[$f]['d']++;
    } elseif ($r['golos_casa']<$r['golos_fora']) {
        $classificacao[$f]['v']++; $classificacao[$f]['p']+=3;
        $classificacao[$c]['d']++;
    } else {
        $classificacao[$c]['e']++; $classificacao[$f]['e']++;
        $classificacao[$c]['p']++; $classificacao[$f]['p']++;
    }
}

usort($classificacao, fn($a,$b)=>
    $b['p']<=>$a['p'] ?: 
    ($b['gm']-$b['gs'])<=>($a['gm']-$a['gs'])
);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Classificação</title>

<style>
body {
    font-family: Inter, Arial;
    background: linear-gradient(180deg,#37003c,#24002a);
    color:white;
}
.container {
    max-width:900px;
    margin:40px auto;
    background:#4b0055;
    border-radius:16px;
    overflow:hidden;
}
table { width:100%; border-collapse:collapse; }
th,td { padding:10px; text-align:center; }
th { background:#2a002e; }
.team {
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:600;
}
.team img { width:22px; }
.champions { background:rgba(0,123,255,.15); }
.relegation { background:rgba(255,0,0,.15); }
</style>
</head>

<body>
<div class="container">
<table>
<tr>
<th>#</th><th>Equipa</th><th>J</th><th>V</th><th>E</th><th>D</th><th>DG</th><th>P</th>
</tr>

<?php $pos=1; foreach ($classificacao as $c):
$class=$pos<=4?'champions':($pos>count($classificacao)-3?'relegation':''); ?>
<tr class="<?= $class ?>">
<td><?= $pos ?></td>
<td class="team"><img src="<?= $c['logo'] ?>"><?= $c['nome'] ?></td>
<td><?= $c['j'] ?></td>
<td><?= $c['v'] ?></td>
<td><?= $c['e'] ?></td>
<td><?= $c['d'] ?></td>
<td><?= $c['gm']-$c['gs'] ?></td>
<td><b><?= $c['p'] ?></b></td>
</tr>
<?php $pos++; endforeach; ?>
</table>
</div>
</body>
</html>
