<?php
header('Content-Type: application/json');
require_once __DIR__ . '/UsuarioDAO.php';

$data = json_decode(file_get_contents("php://input"), true);

$nome = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$senha = $data['senha'] ?? '';
$tipo = $data['tipo'] ?? '';

if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
    echo json_encode(["erro" => "Todos os dados são obrigatórios."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["erro" => "Formato de e-mail inválido."]);
    exit;
}

$resultado = UsuarioDAO::cadastrar($nome, $email, $senha, $tipo);
echo json_encode($resultado);
?>