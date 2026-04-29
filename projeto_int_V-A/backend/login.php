<?php
require_once __DIR__ . '/UsuarioDAO.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$senha = $data['senha'];

$resultado = UsuarioDAO::login($email, $senha);

echo json_encode($resultado);
?>