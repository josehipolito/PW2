<?php
session_start();

/* ===============================
   FUNÇÃO DE LIGAÇÃO À BD
================================ */
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

/* ===============================
   CONFIGURAÇÃO DA BASE
================================ */
$host = 'localhost';
$db   = 'u506280443_josjoaDB';
$user = 'u506280443_josjoadbUser';
$pass = '7$&9N~8XpT';
$charset = 'utf8mb4';

/* ===============================
   LIGAÇÃO LOCAL
================================ */
$pdoLocal  = ligarBD($host, $db, $user, $pass, $charset);

/* ===============================
   PROCESSAMENTO DO FORMULÁRIO
================================ */
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_jornada = $_POST['id_jornada'];
    $casa       = $_POST['casa'];
    $fora       = $_POST['fora'];
    $golos_casa = $_POST['golos_casa'];
    $golos_fora = $_POST['golos_fora'];

    // Validar que equipa casa != equipa fora
    if ($casa == $fora) {
        $mensagem = 'A equipa da casa e a equipa de fora não podem ser iguais!';
    } else {
        // Verificar se o jogo já existe
        $stmt = $pdoLocal->prepare(
            "SELECT COUNT(*) FROM jogos WHERE id_jornada=? AND equipa_casa=? AND equipa_fora=?"
        );
        $stmt->execute([$id_jornada, $casa, $fora]);
        $existeJogo = $stmt->fetchColumn() > 0;

        if (!$existeJogo) {
            // Inserir jogo
            $stmt = $pdoLocal->prepare(
                "INSERT INTO jogos (id_jornada, equipa_casa, equipa_fora) VALUES (?,?,?)"
            );
            $stmt->execute([$id_jornada, $casa, $fora]);
            $id_jogo = $pdoLocal->lastInsertId();

            // Inserir resultado
            $stmt = $pdoLocal->prepare(
                "INSERT INTO resultados (id_jogo, golos_casa, golos_fora) VALUES (?,?,?)"
            );
            $stmt->execute([$id_jogo, $golos_casa, $golos_fora]);

            $mensagem = 'Jogo e resultado guardados com sucesso!';
        } else {
            $mensagem = 'Este jogo já existe na jornada selecionada!';
        }
    }
}

/* ===============================
   DADOS PARA FORMULÁRIO
================================ */
$jornadas = $pdoLocal->query("SELECT id, numero FROM jornadas ORDER BY numero")->fetchAll();
$equipas  = $pdoLocal->query("SELECT id, nome FROM equipas ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Adicionar Jogo e Resultado</title>
<style>
body { font-family: Arial; background:#f4f4f9; padding:30px; }
section { background:#fff; padding:20px; margin:20px auto; width:60%; border-radius:8px; }
h1, h2 { text-align:center; }
select, input, button { width:100%; padding:10px; margin-top:10px; }
button { background:#007bff; color:#fff; border:none; cursor:pointer; }
button:hover { background:#0056b3; }
p.mensagem { text-align:center; font-weight:bold; color:green; }
p.erro { text-align:center; font-weight:bold; color:red; }
</style>
</head>
<body>

<h1>✏️ Adicionar Jogo e Resultado</h1>

<?php if ($mensagem): ?>
<p class="<?= strpos($mensagem,'sucesso') !== false ? 'mensagem' : 'erro' ?>"><?= $mensagem ?></p>
<?php endif; ?>

<section>
<form method="post">
<h2>Adicionar Jogo</h2>

<select name="id_jornada" required>
<option value="">Selecione a Jornada</option>
<?php foreach ($jornadas as $j): ?>
<option value="<?= $j['id'] ?>">Jornada <?= $j['numero'] ?></option>
<?php endforeach; ?>
</select>

<select name="casa" required>
<option value="">Selecione Equipa Casa</option>
<?php foreach ($equipas as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
<?php endforeach; ?>
</select>

<select name="fora" required>
<option value="">Selecione Equipa Fora</option>
<?php foreach ($equipas as $e): ?>
<option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
<?php endforeach; ?>
</select>

<input type="number" name="golos_casa" placeholder="Golos Casa" required>
<input type="number" name="golos_fora" placeholder="Golos Fora" required>

<button>Guardar Jogo e Resultado</button>
</form>
</section>

</body>
</html>
