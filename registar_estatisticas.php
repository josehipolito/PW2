<?php
require 'config.php';
$pdo = ligarBD();

$mensagem = "";

// 1. Processar a gravação dos dados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stats'])) {
    try {
        $stmt = $pdo->prepare("UPDATE jogadores SET golos = ?, assistencias = ?, defesas = ? WHERE id = ?");
        
        foreach ($_POST['stats'] as $id_jogador => $valores) {
            $golos = isset($valores['golos']) ? (int)$valores['golos'] : 0;
            $assist = isset($valores['assistencias']) ? (int)$valores['assistencias'] : 0;
            $defesas = isset($valores['defesas']) ? (int)$valores['defesas'] : 0;
            
            $stmt->execute([$golos, $assist, $defesas, $id_jogador]);
        }
        $mensagem = "✅ Estatísticas atualizadas com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "❌ Erro ao atualizar: " . $e->getMessage();
    }
}

// 2. Obter lista de equipas para o filtro
$equipas = $pdo->query("SELECT id, nome FROM equipas ORDER BY nome ASC")->fetchAll();

// 3. Obter jogadores da equipa selecionada (ou da primeira da lista por defeito)
$id_equipa_sel = isset($_GET['equipa_id']) ? (int)$_GET['equipa_id'] : (isset($equipas[0]['id']) ? $equipas[0]['id'] : 0);

$jogadores = [];
if ($id_equipa_sel > 0) {
    $stmt = $pdo->prepare("SELECT id, nome, posicao, numero, golos, assistencias, defesas FROM jogadores WHERE id_equipa = ? ORDER BY FIELD(posicao, 'Guarda-redes', 'Defesa', 'Médio', 'Avançado'), nome ASC");
    $stmt->execute([$id_equipa_sel]);
    $jogadores = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registar Estatísticas Individuais</title>
    <style>
        body { background: linear-gradient(135deg, #37003c, #24002a); font-family: 'Inter', sans-serif; color: white; padding: 20px; min-height: 100vh; }
        .container { max-width: 900px; margin: auto; background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; backdrop-filter: blur(10px); }
        
        h1 { color: #00ff85; text-align: center; }
        .msg { text-align: center; padding: 10px; margin-bottom: 20px; border-radius: 5px; background: rgba(0,255,133,0.2); color: #00ff85; font-weight: bold; }
        
        .selecao-equipa { background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        select { padding: 10px; border-radius: 5px; border: none; width: 250px; font-size: 1rem; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { background: #00ff85; color: #37003c; }
        
        input[type="number"] { width: 60px; padding: 5px; border-radius: 4px; border: 1px solid #ccc; text-align: center; font-weight: bold; }
        
        .btn-submit { display: block; width: 100%; padding: 15px; margin-top: 20px; background: #00ff85; color: #37003c; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #00cc6a; transform: scale(1.02); }
        
        .voltar { display: inline-block; margin-top: 20px; color: #00ff85; text-decoration: none; }
        .pos-badge { font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

<div class="container">
    <h1>⚽ Registar Estatísticas</h1>

    <?php if ($mensagem): ?>
        <div class="msg"><?= $mensagem ?></div>
    <?php endif; ?>

    <div class="selecao-equipa">
        <form method="GET" id="formEquipa">
            <label>Escolha a Equipa: </label>
            <select name="equipa_id" onchange="this.form.submit()">
                <?php foreach ($equipas as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= ($id_equipa_sel == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($jogadores): ?>
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Jogador / Posição</th>
                    <th>Golos</th>
                    <th>Assistências</th>
                    <th>Defesas (GR)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jogadores as $j): ?>
                <tr>
                    <td><strong><?= $j['numero'] ?></strong></td>
                    <td>
                        <?= htmlspecialchars($j['nome']) ?> <br>
                        <span class="pos-badge"><?= $j['posicao'] ?></span>
                    </td>
                    <td>
                        <?php if ($j['posicao'] != 'Guarda-redes'): ?>
                            <input type="number" name="stats[<?= $j['id'] ?>][golos]" value="<?= $j['golos'] ?>" min="0">
                        <?php else: ?>
                            <input type="hidden" name="stats[<?= $j['id'] ?>][golos]" value="0"> -
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="number" name="stats[<?= $j['id'] ?>][assistencias]" value="<?= $j['assistencias'] ?>" min="0">
                    </td>
                    <td>
                        <?php if ($j['posicao'] == 'Guarda-redes'): ?>
                            <input type="number" name="stats[<?= $j['id'] ?>][defesas]" value="<?= $j['defesas'] ?>" min="0" style="border-color: #00ff85;">
                        <?php else: ?>
                            <input type="hidden" name="stats[<?= $j['id'] ?>][defesas]" value="0"> -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn-submit">💾 Guardar Todas as Alterações</button>
    </form>
    <?php else: ?>
        <p style="text-align: center;">Nenhum jogador encontrado para esta equipa.</p>
    <?php endif; ?>

    <a href="index.html" class="voltar">← Voltar à Home</a>
</div>

</body>
</html>