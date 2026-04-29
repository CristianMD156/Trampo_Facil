<?php
// Força o retorno em JSON para não quebrar o fetch do JavaScript
header('Content-Type: application/json');

require_once __DIR__ . '/vagaDAO.php';

$data = json_decode(file_get_contents("php://input"), true);

$empresa_id = $data['empresa_id'] ?? null;
$titulo = trim($data['titulo'] ?? '');
$descricao = trim($data['descricao'] ?? '');
$salario = floatval($data['salario'] ?? 0);
$cidade = trim($data['cidade'] ?? '');

// Validações
if (empty($titulo) || empty($descricao) || empty($cidade)) {
    echo json_encode(["erro" => "Título, descrição e cidade são obrigatórios para criar uma vaga."]);
    exit;
}

if (strlen($titulo) < 5) {
    echo json_encode(["erro" => "O título da vaga deve ter no mínimo 5 caracteres."]);
    exit;
}

if ($salario < 0) {
    echo json_encode(["erro" => "O salário não pode ser negativo."]);
    exit;
}

try {
    $resultado = VagaDAO::criar($empresa_id, $titulo, $descricao, $salario, $cidade);
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode(["erro" => "Erro no banco: " . $e->getMessage()]);
}
?>