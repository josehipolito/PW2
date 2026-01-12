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

$pdo = ligarBD(
    'localhost',
    'u506280443_josjoaDB',
    'u506280443_josjoadbUser',
    '7$&9N~8XpT',
    'utf8mb4'
);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jornada = $_POST['id_jornada'];
    $casa = $_POST['casa'];
    $fora = $_POST['fora'];
    $golos_casa = $_POST['golos_casa'];
    $golos_fora = $_POST['golos_fora'];

    if ($casa == $fora) {
        $mensagem = '❌ Equipas iguais!';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO jogos (id_jornada, equipa_casa, equipa_fora)
             VALUES (?,?,?)"
        );
        $stmt->execute([$id_jornada, $casa, $fora]);
        $id_jogo = $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            "INSERT INTO resultados (id_jogo, golos_casa, golos_fora)
             VALUES (?,?,?)"
        );
        $stmt->execute([$id_jogo, $golos_casa, $golos_fora]);

        $mensagem = '✅ Jogo e resultado guardados!';
    }
}

$jornadas = $pdo->query("SELECT id, numero FROM jornadas ORDER BY numero")->fetchAll();
$equipas  = $pdo->query("SELECT id, nome FROM equipas ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Adicionar Jogo</title>

<style>
body {
    background: linear-gradient(180deg, #37003c, #24002a);
    font-family: Inter, Arial;
    color: white;
    padding: 40px;
}

.card {
    max-width: 600px;
    margin: auto;
    background: #4b0055;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 25px 50px rgba(0,0,0,.4);
}

h1 { text-align: center; }

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
    cursor: pointer;
}

.msg { text-align: center; margin-top: 15px; }
</style>
</head>

<body>

<div class="card">
<h1>✏️ Adicionar Jogo</h1>

<?php if ($mensagem): ?>
<p class="msg"><?= $mensagem ?></p>
<?php endif; ?>

<form method="post">
<select name="id_jornada" required>
<option value="">Jornada</option>
<?php foreach ($jornadas as $j): ?>
<option value="<?= $j['id'] ?>">Jornada <?= $j['numero'] ?></option>
<?php endforeach; ?>
</select>

<select name="casa" required>
<option value="">Equipa Casa</option>
<?php foreach ($equipas as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
<?php endforeach; ?>
</select>

<select name="fora" required>
<option value="">Equipa Fora</option>
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
