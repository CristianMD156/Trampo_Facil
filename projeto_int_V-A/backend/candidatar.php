<?php
// Força o retorno em JSON
header('Content-Type: application/json');

require_once __DIR__ . '/conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

// O frontend envia o ID do usuário logado (usuario.id)
$usuario_id = $data['candidato_id'] ?? null;
$vaga_id = $data['vaga_id'] ?? null;

if (!$usuario_id || !$vaga_id) {
    echo json_encode(["erro" => "Dados da candidatura inválidos"]);
    exit;
}

try {
    // PASSO 1: Descobrir qual é o ID real na tabela 'candidato' correspondente a este usuário
    $sqlCandidato = "SELECT id FROM candidato WHERE usuario_id = ?";
    $stmtCand = $pdo->prepare($sqlCandidato);
    $stmtCand->execute([$usuario_id]);
    $candidato = $stmtCand->fetch(PDO::FETCH_ASSOC);

    if (!$candidato) {
        echo json_encode(["erro" => "Perfil de candidato não encontrado para este usuário."]);
        exit;
    }

    $id_real_candidato = $candidato['id'];

    // PASSO 2: Verificar se a vaga existe e seu status (Usando a tabela 'vaga' no singular)
    $sqlVaga = "SELECT status FROM vaga WHERE id = ?";
    $stmtVaga = $pdo->prepare($sqlVaga);
    $stmtVaga->execute([$vaga_id]);
    $vaga = $stmtVaga->fetch(PDO::FETCH_ASSOC);

    if (!$vaga) {
        echo json_encode(["erro" => "Vaga não encontrada no sistema."]);
        exit;
    }

    // Regra de Negócio: Impedir candidaturas em vagas encerradas
    if ($vaga['status'] !== 'ativa') {
        echo json_encode(["erro" => "Esta vaga já foi encerrada e não aceita mais candidatos."]);
        exit;
    }

    // PASSO 3: Evita candidatura duplicada (usando o id do candidato real)
    $sqlCheck = "SELECT * FROM candidatura WHERE candidato_id = ? AND vaga_id = ?";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$id_real_candidato, $vaga_id]);

    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(["erro" => "Você já se candidatou para esta vaga."]);
        exit;
    }

    // PASSO 4: Insere a candidatura vinculando o candidato real à vaga
    $sqlInsert = "INSERT INTO candidatura (candidato_id, vaga_id) VALUES (?, ?)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([$id_real_candidato, $vaga_id]);

    echo json_encode(["sucesso" => "Candidatura realizada com sucesso!"]);

} catch (Exception $e) {
    // Retorna o erro do MySQL de forma limpa
    echo json_encode(["erro" => "Erro no banco de dados: " . $e->getMessage()]);
}
?>