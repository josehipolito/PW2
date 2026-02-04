<?php
require 'config.php';
$pdo = ligarBD();

// --- ESTATÍSTICAS GERAIS ---
$sqlMedia = "SELECT AVG(golos_casa + golos_fora) as media FROM resultados";
$resMedia = $pdo->query($sqlMedia)->fetch();
$mediaGolos = $resMedia ? $resMedia['media'] : 0;

// --- CONSULTA DE JOGADORES DESTACADOS POR POSIÇÃO ---
// Nota: Aqui podes futuramente adicionar ORDER BY golos ou minutos 
// Por agora, vamos listar alguns nomes de destaque de cada setor
function getDestaques($pdo, $posicao) {
    $stmt = $pdo->prepare("SELECT j.nome, j.numero, e.nome as equipa_nome, e.logo 
                           FROM jogadores j 
                           JOIN equipas e ON j.id_equipa = e.id 
                           WHERE j.posicao = ? 
                           LIMIT 5");
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
    <title>Estatísticas Avançadas - PL</title>
    <style>
        body { background: linear-gradient(180deg, #37003c, #24002a); font-family: 'Inter', sans-serif; color: white; padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        /* Layout em Grelha para os Tops */
        .grid-tops { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 20px; 
            margin-top: 40px; 
        }
        
        .top-card { 
            background: rgba(255, 255, 255, 0.05); 
            border-radius: 15px; 
            padding: 15px; 
            border-top: 4px solid #00ff85;
        }
        
        .top-card h3 { 
            text-align: center; 
            color: #00ff85; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            padding-bottom: 10px; 
        }

        .player-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .player-row img { width: 25px; height: 25px; margin-right: 10px; background: white; border-radius: 3px; }
        .player-info { flex-grow: 1; font-size: 0.9rem; }
        .player-num { font-weight: bold; color: #00ff85; margin-right: 5px; }

        .media-destaque {
            text-align: center;
            background: #4b0055;
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="media-destaque">
        <h1>📊 Estatísticas de Rendimento</h1>
        <p>Média de Golos da Liga: <strong><?= number_format($mediaGolos, 2) ?></strong></p>
    </div>

    <div class="grid-tops">
        <div class="top-card">
            <h3>🧤 Top Guarda-Redes</h3>
            <?php foreach($melhoresGR as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div class="player-info">
                    <span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="top-card">
            <h3>🛡️ Top Defesas</h3>
            <?php foreach($melhoresDEF as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div class="player-info">
                    <span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="top-card">
            <h3>🎯 Top Médios</h3>
            <?php foreach($melhoresMED as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div class="player-info">
                    <span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="top-card">
            <h3>⚽ Top Avançados</h3>
            <?php foreach($melhoresAV as $p): ?>
            <div class="player-row">
                <img src="imagens/<?= $p['logo'] ?>">
                <div class="player-info">
                    <span class="player-num"><?= $p['numero'] ?></span> <?= $p['nome'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <p style="text-align: center; margin-top: 40px;">
        <a href="index.html" style="color: #00ff85; text-decoration: none;">← Voltar ao Início</a>
    </p>
</div>

</body>
</html>