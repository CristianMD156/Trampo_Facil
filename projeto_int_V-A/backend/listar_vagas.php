<?php
header('Content-Type: application/json');
require_once __DIR__ . '/conexao.php';

try {
    $sql = "SELECT * FROM vaga WHERE status = 'ativa'"; 
    $stmt = $pdo->query($sql);
    
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($vagas);

} catch (Exception $e) {
    echo json_encode([
        "erro" => true,
        "mensagem" => $e->getMessage()
    ]);
}
?>