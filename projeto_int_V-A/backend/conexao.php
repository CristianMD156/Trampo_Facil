<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "trampofacil";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["erro" => "Erro de conexão: " . $e->getMessage()]));
}
?>