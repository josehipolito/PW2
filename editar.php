<?php
require 'config.php';
//$pdo = ligarBD();
$pdo = ligarBDstr();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['casa'] == $_POST['fora']) {
        $msg = '❌ Equipas iguais!';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO jogos (id_jornada, equipa_casa, equipa_fora)
             VALUES (?,?,?)"
        );
        $stmt->execute([
            $_POST['id_jornada'],
            $_POST['casa'],
            $_POST['fora']
        ]);

        $idJogo = $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            "INSERT INTO resultados (id_jogo, golos_casa, golos_fora)
             VALUES (?,?,?)"
        );
        $stmt->execute([
            $idJogo,
            $_POST['golos_casa'],
            $_POST['golos_fora']
        ]);

        $msg = '✅ Jogo guardado com sucesso!';
    }
}

$jornadas = $pdo->query("SELECT id, numero FROM jornadas")->fetchAll();
$equipas = $pdo->query("SELECT id, nome FROM equipas")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Adicionar Jogo</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter;
    color: white;
}
.card {
    max-width: 600px;
    margin: 40px auto;
    background: #4b0055;
    padding: 30px;
    border-radius: 16px;
}
select, input, button {
    width: 100%;
    padding: 12px;
    margin-top: 12px;
    border-radius: 8px;
    border: none;
}
button {
    background: #00ff85;
    font-weight: bold;
}
</style>
</head>

<body>
<div class="card">
<h1>✏️ Adicionar Jogo</h1>
<p><?= $msg ?></p>

<form method="post">
<select name="id_jornada" required>
<option value="">Jornada</option>
<?php foreach ($jornadas as $j): ?>
<option value="<?= $j['id'] ?>">Jornada <?= $j['numero'] ?></option>
<?php endforeach; ?>
</select>

<select name="casa" required>
<option value="">Casa</option>
<?php foreach ($equipas as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
<?php endforeach; ?>
</select>

<select name="fora" required>
<option value="">Fora</option>
<?php foreach ($equipas as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
<?php endforeach; ?>
</select>

<input type="number" name="golos_casa" placeholder="Golos Casa" required>
<input type="number" name="golos_fora" placeholder="Golos Fora" required>

<button>Guardar</button>
</form>
</div>
</body>
</html>
