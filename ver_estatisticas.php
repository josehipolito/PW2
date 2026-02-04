<?php
require 'config.php';
$pdo = ligarBD();

// --- 1. ESTATÍSTICAS COLETIVAS (Golos e Médias) ---
$sqlMedia = "SELECT AVG(golos_casa + golos_fora) as media FROM resultados";
$resMedia = $pdo->query($sqlMedia)->fetch();
$mediaGolos = $resMedia ? $resMedia['media'] : 0;

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

$jogadoresCount = $pdo->query("SELECT id_equipa, COUNT(*) as total FROM jogadores GROUP BY id_equipa")->fetchAll();
foreach ($jogadoresCount as $jc) {
    if (isset($stats[$jc['id_equipa']])) { $stats[$jc['id_equipa']]['jogadores'] = $jc['total']; }
}

// Ordenação para os Cards
$melhorAtaque = $stats;
usort($melhorAtaque, fn($a, $b) => $b['gm'] <=> $a['gm']);

$melhorDefesa = $stats;
usort($melhorDefesa, fn($a, $b) => $a['gs'] <=> $b['gs']);

// --- 2. LISTAS INDIVIDUAIS POR POSIÇÃO (Destaques) ---
function getDestaques($pdo, $posicao) {
    $stmt = $pdo->prepare("SELECT j.nome, j.numero, e.nome as equipa_nome, e.logo 
                           FROM jogadores j 
                           JOIN equipas e ON j.id_equipa = e.id 
                           WHERE j.posicao = ? 
                           ORDER BY j.nome ASC LIMIT 5");
    $stmt->execute([$posicao]);
    return $stmt->fetchAll();
}

$melhoresGR = getDestaques($pdo, 'Guarda-redes');
$melhoresDEF = getDestaques($pdo, 'Defesa');
$melhoresMED = getDestaques($pdo, 'Médio');
$melhoresAV = getDestaques($pdo, 'Avançado');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Estatísticas Globais - Premier League</title>
    <style>
        body { background: linear-gradient(180deg, #37003c, #24002a); font-family: 'Inter', sans-serif; color: white; padding: 20px; margin: 0; }
        .container { max-width: 1100px; margin: auto; }
        h1, h2 { text-align: center; color: #00ff85; }
        
        /* Seção de Cards Coletivos */
        .grid-coletiva { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 50px; }
        .card-main { background: #4b0055; padding: 20px; border-radius: 15px; text-align: center; border-bottom: 4px solid #00ff85; }
        .stat-value { font-size: 2.2rem; font-weight: bold; color: #00ff85; margin: 10px 0; }
        .team-info { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px; }
        .team-info img { width: 35px; height: 35px; background: white; border-radius: 5px; padding: 2px; }

        /* Seção de Listas por Posição */
        .grid-posicoes { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .pos-card { background: rgba(255, 255, 255, 0.05); border-radius: 15px; padding: 15px; border-top: 3px solid #00ff85; }
        .pos-card h3 { font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-top: 0; }
        .player-row { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem; }
        .player-row img { width: 20px; height: 20px; margin-right: 10px; background: white; border-radius: 2px; }
        .player-num { font-weight: bold; color: #00ff85; margin-right: 5px; }
        
        .back-btn { display: block; text-align: center; margin-top: 40px; color: #00ff85; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>📊 Estatísticas da Liga</h1>

    <div class="grid-coletiva">
        <div class="card-main">
            <h3>⚽ Média de Golos</h3>
            <div class="stat-value"><?= number_format($mediaGolos, 2) ?></div>
            <p>Por partida</p>
        </div>
        <div class="card-main">
            <h3>🔥 Melhor Ataque</h3>
            <div class="team-info">
                <img src="imagens/<?= $melhorAtaque[0]['logo'] ?>">
                <span><?= $melhorAtaque[0]['nome'] ?></span>
            </div>
            <div class="stat-value"><?= $melhorAtaque[0]['gm'] ?></div>
            <p>Golos Marcados</p>
        </div>
        <div class="card-main">
            <h3>🛡️ Melhor Defesa</h3>
            <div class="team-info">
                <img src="imagens/<?= $melhorDefesa[0]['logo'] ?>">
                <span><?= $melhorDefesa[0]['nome'] ?></span>
            </div>
            <div class="stat-value"><?= $melhorDefesa[0]['gs'] ?></div>
            <p>Golos Sofridos</p>
        </div>
    </div>

    <hr style="opacity: 0.1; margin-bottom: 40px;">

    <h2>🌟 Destaques por Setor</h2>
    <div class="grid-posicoes">
        <div class="pos-card">
            <h3>🧤 Guarda-Redes</h3>
            <?php foreach($melhoresGR as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div><span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pos-card">
            <h3>🛡️ Defesas</h3>
            <?php foreach($melhoresDEF as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div><span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pos-card">
            <h3>🎯 Médios</h3>
            <?php foreach($melhoresMED as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div><span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pos-card">
            <h3>⚽ Avançados</h3>
            <?php foreach($melhoresAV as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div><span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <a href="index.html" class="back-btn">← Voltar à Página Principal</a>
</div>

</body>
</html>