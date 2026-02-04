<?php
require 'config.php';
$pdo = ligarBD();

// 1. Média de Golos da Liga
$sqlMedia = "SELECT AVG(golos_casa + golos_fora) as media FROM resultados";
$mediaGolos = $pdo->query($sqlMedia)->fetch()['media'];

// 2. Ataque e Defesa (Usando a lógica da classificação)
$equipas = $pdo->query("SELECT id, nome, logo FROM equipas")->fetchAll();
$stats = [];
foreach ($equipas as $e) {
    $stats[$e['id']] = ['nome' => $e['nome'], 'logo' => $e['logo'], 'gm' => 0, 'gs' => 0, 'jogadores' => 0];
}

$resultados = $pdo->query("SELECT j.equipa_casa, j.equipa_fora, r.golos_casa, r.golos_fora FROM jogos j JOIN resultados r ON r.id_jogo = j.id")->fetchAll();
foreach ($resultados as $r) {
    $stats[$r['equipa_casa']]['gm'] += $r['golos_casa'];
    $stats[$r['equipa_casa']]['gs'] += $r['golos_fora'];
    $stats[$r['equipa_fora']]['gm'] += $r['golos_fora'];
    $stats[$r['equipa_fora']]['gs'] += $r['golos_casa'];
}

// 3. Contagem de Jogadores por Equipa
$jogadoresCount = $pdo->query("SELECT id_equipa, COUNT(*) as total FROM jogadores GROUP BY id_equipa")->fetchAll();
foreach ($jogadoresCount as $jc) {
    if (isset($stats[$jc['id_equipa']])) {
        $stats[$jc['id_equipa']]['jogadores'] = $jc['total'];
    }
}

// Ordenar para obter os melhores
$melhorAtaque = $stats;
usort($melhorAtaque, fn($a, $b) => $b['gm'] <=> $a['gm']);

$melhorDefesa = $stats;
usort($melhorDefesa, fn($a, $b) => $a['gs'] <=> $b['gs']);

$plantelMaisNumeroso = $stats;
usort($plantelMaisNumeroso, fn($a, $b) => $b['jogadores'] <=> $a['jogadores']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Estatísticas da Liga</title>
    <style>
        body { background: linear-gradient(180deg, #37003c, #24002a); font-family: 'Inter', sans-serif; color: white; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        .card { background: #4b0055; padding: 20px; border-radius: 15px; text-align: center; border-bottom: 4px solid #00ff85; }
        .card h3 { color: #00ff85; margin-bottom: 15px; }
        .stat-value { font-size: 2rem; font-weight: bold; margin: 10px 0; }
        .team-info { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .team-info img { width: 40px; height: 40px; object-fit: contain; background: white; border-radius: 5px; padding: 2px; }
        .back-link { display: inline-block; margin-top: 20px; color: #00ff85; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h1>📊 Estatísticas da Premier League</h1>
    
    <div class="card" style="max-width: 400px; margin: auto;">
        <h3>Média de Golos/Jogo</h3>
        <div class="stat-value"><?= number_format($mediaGolos, 2) ?></div>
        <p>Total de espetáculo na liga!</p>
    </div>

    <div class="grid">
        <div class="card">
            <h3>🔥 Melhor Ataque</h3>
            <div class="team-info">
                <img src="imagens/<?= $melhorAtaque[0]['logo'] ?>">
                <span><?= $melhorAtaque[0]['nome'] ?></span>
            </div>
            <div class="stat-value"><?= $melhorAtaque[0]['gm'] ?></div>
            <p>Golos Marcados</p>
        </div>

        <div class="card">
            <h3>🛡️ Melhor Defesa</h3>
            <div class="team-info">
                <img src="imagens/<?= $melhorDefesa[0]['logo'] ?>">
                <span><?= $melhorDefesa[0]['nome'] ?></span>
            </div>
            <div class="stat-value"><?= $melhorDefesa[0]['gs'] ?></div>
            <p>Golos Sofridos</p>
        </div>

        <div class="card">
            <h3>👥 Plantel Mais Extenso</h3>
            <div class="team-info">
                <img src="imagens/<?= $plantelMaisNumeroso[0]['logo'] ?>">
                <span><?= $plantelMaisNumeroso[0]['nome'] ?></span>
            </div>
            <div class="stat-value"><?= $plantelMaisNumeroso[0]['jogadores'] ?></div>
            <p>Jogadores Inscritos</p>
        </div>
    </div>

    <a href="index.html" class="back-link">← Voltar à Home</a>
</div>

</body>
</html>