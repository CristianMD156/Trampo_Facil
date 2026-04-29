<?php
header('Content-Type: application/json');
require_once __DIR__ . '/conexao.php';

// Pegamos o ID do usuário via GET na URL
$usuario_id = $_GET['usuario_id'] ?? null;

if (!$usuario_id) {
    echo json_encode(["erro" => "ID do usuário não fornecido."]);
    exit;
}

try {
    // 1. Descobrir o ID real do candidato
    $sqlCand = "SELECT id FROM candidato WHERE usuario_id = ?";
    $stmtCand = $pdo->prepare($sqlCand);
    $stmtCand->execute([$usuario_id]);
    $cand = $stmtCand->fetch(PDO::FETCH_ASSOC);

    if (!$cand) {
        echo json_encode([]); // Retorna vazio se o perfil de candidato não existir
        exit;
    }

    $id_candidato = $cand['id'];

    // 2. Buscar as candidaturas cruzando (JOIN) com os dados da vaga
    $sql = "SELECT c.status as status_candidatura, c.data_candidatura, v.titulo, v.cidade
            FROM candidatura c
            JOIN vaga v ON c.vaga_id = v.id
            WHERE c.candidato_id = ?
            ORDER BY c.data_candidatura DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_candidato]);
            
    $candidaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($candidaturas);

} catch (Exception $e) {
    echo json_encode(["erro" => "Erro no banco: " . $e->getMessage()]);
}
?>