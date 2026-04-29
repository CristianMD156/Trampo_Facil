<?php
require_once __DIR__ . '/conexao.php';

class CandidaturaDAO {

    public static function candidatar($candidato_id, $vaga_id) {
        global $pdo;

        $sql = "INSERT INTO candidatura (candidato_id, vaga_id)
                VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$candidato_id, $vaga_id]);

        return ["sucesso" => "Candidatura realizada"];
    }
}
?>