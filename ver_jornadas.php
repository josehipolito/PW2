<?php
// --------------------------------------------
// CONEXÃO PDO
// --------------------------------------------
function conectarBD() {
    $host = 'localhost';
    $db   = 'premier_league';
    $user = 'pw2';
    $pass = '1234';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Erro ao conectar: " . $e->getMessage());
    }
}

$pdo = conectarBD();

// Buscar jornadas
$stmt = $pdo->query("SELECT id, numero, data_jornada FROM jornadas ORDER BY numero ASC");
$jornadas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Jornadas - Premier League</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .jornada-box {
            background: white;
            padding: 15px;
            margin: 10px auto;
            width: 60%;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 8px 14px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h1>📅 Jornadas da Premier League</h1>

<?php foreach ($jornadas as $j): ?>
    <div class="jornada-box">
        <div>
            <strong>Jornada <?= $j["numero"] ?></strong><br>
            <?= $j["data_jornada"] ? "Data: " . $j["data_jornada"] : "Data não definida" ?>
        </div>

        <a class="btn" href="ver_jogos.php?id_jornada=<?= $j["id"] ?>">
            Ver Jogos →
        </a>
    </div>
<?php endforeach; ?>

</body>
</html>
