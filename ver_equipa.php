<?php
require 'config.php';
$pdo = ligarBD();

// 1. Validar o ID da equipa
if (!isset($_GET['id'])) {
    die('Equipa não especificada.');
}

$id_equipa = (int)$_GET['id'];

// 2. Buscar os dados da EQUIPA (incluindo o LOGO)
$stmt = $pdo->prepare("SELECT nome, estadio, treinador, logo FROM equipas WHERE id = ?");
$stmt->execute([$id_equipa]);
$equipa = $stmt->fetch();

if (!$equipa) {
    die('Equipa não encontrada.');
}

// 3. Buscar os JOGADORES desta equipa
$stmt = $pdo->prepare("SELECT nome, posicao, numero, nacionalidade FROM jogadores WHERE id_equipa = ? ORDER BY FIELD(posicao, 'Guarda-redes', 'Defesa', 'Médio', 'Avançado'), numero ASC");
$stmt->execute([$id_equipa]);
$jogadores = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($equipa['nome']) ?> - Plantel</title>
    <style>
        body {
            background: linear-gradient(180deg, #37003c, #24002a);
            font-family: 'Inter', sans-serif;
            color: white;
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 900px; margin: auto; }
        
        /* Estilo para o Cabeçalho com Logo */
        .header-equipa {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .header-equipa img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: white;
            padding: 5px;
            border-radius: 10px;
        }
        .info-equipa h1 { margin: 0; font-size: 2.5rem; }
        
        table { width: 100%; border-collapse: collapse; background: #4b0055; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { background: rgba(0,0,0,0.2); color: #00ff85; }
        tr:hover { background: rgba(255,255,255,0.05); }
    </style>
</head>
<body>

<div class="container">
    <div class="header-equipa">
        <img src="imagens/<?= htmlspecialchars($equipa['logo']) ?>" alt="Logo <?= htmlspecialchars($equipa['nome']) ?>">
        <div class="info-equipa">
            <h1><?= htmlspecialchars($equipa['nome']) ?></h1>
            <p>🏟️ <strong>Estádio:</strong> <?= htmlspecialchars($equipa['estadio']) ?> | 👔 <strong>Treinador:</strong> <?= htmlspecialchars($equipa['treinador']) ?></p>
        </div>
    </div>

    <h2>Plantel</h2>
    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>Nome</th>
                <th>Posição</th>
                <th>Nacionalidade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jogadores as $j): ?>
            <tr>
                <td><strong><?= $j['numero'] ?></strong></td>
                <td><?= htmlspecialchars($j['nome']) ?></td>
                <td><?= htmlspecialchars($j['posicao']) ?></td>
                <td><?= htmlspecialchars($j['nacionalidade']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <a href="classificacao.php" style="color: #00ff85; text-decoration: none;">← Voltar à Classificação</a>
</div>

</body>
</html>