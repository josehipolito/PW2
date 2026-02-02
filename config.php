<?php
function ligarBD() {
    return new PDO(
        "mysql:host=localhost;dbname=premier_league;charset=utf8mb4",
        "pw2",
        "1234",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}
