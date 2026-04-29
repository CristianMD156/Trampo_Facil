<?php
require_once __DIR__ . '/conexao.php';

class UsuarioDAO {

    public static function cadastrar($nome, $email, $senha, $tipo) {
        global $pdo;

        // Verifica duplicidade
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            return ["erro" => "E-mail já cadastrado"];
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (nome, email, senha, tipo)
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senhaHash, $tipo]);

$idUsuario = $pdo->lastInsertId();

// inserir na tabela específica
if ($tipo == "candidato") {
    $sql = "INSERT INTO candidato (usuario_id) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsuario]);

} else if ($tipo == "empresa") {
    $sql = "INSERT INTO empresa (usuario_id) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsuario]);
}

return ["sucesso" => "Cadastro realizado"];


    }

    public static function login($email, $senha) {
        global $pdo;

        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        } else {
            return ["erro" => "Login inválido"];
        }
    }
}
?>