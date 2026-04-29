<?php
require_once __DIR__ . '/conexao.php';

class VagaDAO {

    public static function criar($empresa_id, $titulo, $descricao, $salario, $cidade) {
        global $pdo;

        $sql = "INSERT INTO vaga (empresa_id, titulo, descricao, salario, cidade)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empresa_id, $titulo, $descricao, $salario, $cidade]);

        return ["sucesso" => "Vaga criada"];
    }
}
?>