<?php
require 'config.php';
$pdo = ligarBD();

// 1. Validar e receber o ID da equipa pela URL
$id_equipa = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_equipa <= 0) {
    die("ID de equipa inválido.");
}

// 2. Buscar detalhes da equipa
$stmt = $pdo->prepare("SELECT * FROM equipas WHERE id = ?");
$stmt->execute([$id_equipa]);
$equipa = $stmt->fetch();

if (!$equipa) {
    die("Equipa não encontrada!");
}

// 3. Buscar o plantel (jogadores) ordenado por posição e número
$stmt = $pdo->prepare("
    SELECT * FROM jogadores 
    WHERE id_equipa = ? 
    ORDER BY 
        CASE 
            WHEN posicao = 'Guarda-redes' THEN 1 
            WHEN posicao = 'Defesa' THEN 2 
            WHEN posicao = 'Médio' THEN 3 
            WHEN posicao = 'Avançado' THEN 4 
            ELSE 5 
        END, 
        numero ASC
");
$stmt->execute([$id_equipa]);
$plantel = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($equipa['nome']) ?> | Premier League</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(180deg, #37003c, #24002a);
            color: white;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Cartão do Topo */
        .header-card {
            background: #4b0055;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            border-bottom: 5px solid #00ff85;
        }

        .header-card h1 {
            font-size: 3rem;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .info-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 12px;
            min-width: 200px;
        }

        .info-box span {
            display: block;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #00ff85;
            margin-bottom: 5px;
        }

        /* Tabela de Jogadores */
        h2 {
            margin-top: 40px;
            border-left: 5px solid #00ff85;
            padding-left: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
        }

        th {
            background: #2a002e;
            padding: 15px;
            text-align: left;
            font-size: 0.9rem;
            color: #aaa;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .numero {
            font-weight: bold;
            color: #00ff85;
            font-size: 1.1rem;
            width: 40px;
        }

        .posicao {
            font-size: 0.85rem;
            background: rgba(0, 255, 133, 0.1);
            padding: 4px 10px;
            border-radius: 4px;
            color: #00ff85;
        }

        .btn-voltar {
            display: inline-block;
            margin-bottom: 20px;
            color: #00ff85;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-voltar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="classificacao.php" class="btn-voltar">← Voltar à Classificação</a>

    <div class="header-card">
        <h1><?= htmlspecialchars($equipa['nome']) ?></h1>
        <div class="info-grid">
            <div class="info-box">
                <span>🏟️ Estádio</span>
                <strong><?= htmlspecialchars($equipa['estadio']) ?></strong>
            </div>
            <div class="info-box">
                <span>👔 Treinador</span>
                <strong><?= htmlspecialchars($equipa['treinador']) ?></strong>
            </div>
        </div>
    </div>

    <h2>🏃 Plantel 2025/2026</h2>
    
    <?php if (count($plantel) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Jogador</th>
                    <th>Posição</th>
                    <th>Nacionalidade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plantel as $j): ?>
                <tr>
                    <td class="numero"><?= $j['numero'] ?></td>
                    <td><strong><?= htmlspecialchars($j['nome']) ?></strong></td>
                    <td><span class="posicao"><?= htmlspecialchars($j['posicao']) ?></span></td>
                    <td><?= htmlspecialchars($j['nacionalidade']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; margin-top: 20px; opacity: 0.6;">Ainda não foram inseridos jogadores para esta equipa.</p>
    <?php endif; ?>

</div>

</body>
</html>