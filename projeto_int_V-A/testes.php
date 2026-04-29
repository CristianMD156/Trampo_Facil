<?php
// Configuração da URL base do seu projeto local
$baseUrl = "http://localhost/projeto_int_V-A/backend";

// Função melhorada para aceitar GET e POST
function testarEndpoint($nomeTeste, $url, $metodo = 'POST', $dados = null) {
    echo "<b>Testando:</b> $nomeTeste... <br>";
    
    $opcoes = [
        'http' => [
            'method'  => $metodo,
            'header'  => 'Content-type: application/json',
            'ignore_errors' => true
        ]
    ];

    if ($dados !== null) {
        $opcoes['http']['content'] = json_encode($dados);
    }
    
    $contexto = stream_context_create($opcoes);
    $resultado = file_get_contents($url, false, $contexto);
    
    if ($resultado === FALSE) {
         echo "<span style='color:red'>❌ FALHA DE CONEXÃO (Erro 500 ou Rota não encontrada)</span><br><hr>";
         return null;
    }
    
    $jsonResp = json_decode($resultado, true);
    
    if (isset($jsonResp['erro'])) {
        echo "<span style='color:orange'>⚠️ RETORNO (Com erro tratado):</span> " . $jsonResp['erro'] . "<br><hr>";
    } else {
        echo "<span style='color:green'>✅ SUCESSO:</span> " . print_r($jsonResp, true) . "<br><hr>";
    }

    return $jsonResp; // Retorna os dados para usar no próximo teste, se necessário
}

echo "<h1>Relatório Completo de Testes Automatizados - TrampoFácil</h1>";
echo "<h3>Gerado em: " . date('d/m/Y H:i:s') . "</h3><hr>";

// ==========================================
// MÓDULO 1: CADASTRO E LOGIN
// ==========================================
echo "<h2>1. Usuários e Autenticação</h2>";

// Gera um email único para não dar erro de duplicidade ao rodar o teste várias vezes
$emailTeste = "empresa_" . uniqid() . "@teste.com";
$senhaTeste = "123456";

// 1.1 Teste de Cadastro Válido
$cadastro = testarEndpoint(
    "1.1. Cadastro de nova empresa com sucesso", 
    "$baseUrl/cadastrar.php", 
    'POST',
    ["nome" => "Empresa Teste Auto", "email" => $emailTeste, "senha" => $senhaTeste, "tipo" => "empresa"]
);

// 1.2 Teste de Cadastro com E-mail Duplicado
testarEndpoint(
    "1.2. Impedir cadastro com e-mail já existente", 
    "$baseUrl/cadastrar.php", 
    'POST',
    ["nome" => "Clonador", "email" => $emailTeste, "senha" => "qualquer", "tipo" => "empresa"]
);

// 1.3 Teste de Login Válido
$login = testarEndpoint(
    "1.3. Login válido com o usuário recém-criado", 
    "$baseUrl/login.php", 
    'POST',
    ["email" => $emailTeste, "senha" => $senhaTeste]
);

// 1.4 Teste de Login Inválido
testarEndpoint(
    "1.4. Login com senha incorreta", 
    "$baseUrl/login.php", 
    'POST',
    ["email" => $emailTeste, "senha" => "senha_errada"]
);


// ==========================================
// MÓDULO 2: VAGAS (Regras de Negócio)
// ==========================================
echo "<h2>2. Gerenciamento de Vagas</h2>";

// Vamos pegar o ID da empresa que acabou de fazer login (se o teste passou)
$idEmpresaLogada = isset($login['id']) ? $login['id'] : 1;

// 2.1 Criar vaga com sucesso
$novaVaga = testarEndpoint(
    "2.1. Criação de Vaga com sucesso", 
    "$baseUrl/criar_vaga.php", 
    'POST',
    [
        "empresa_id" => $idEmpresaLogada, 
        "titulo" => "Desenvolvedor de Testes", 
        "descricao" => "Vaga gerada via script automatizado.", 
        "salario" => 5500.00, 
        "cidade" => "Home Office"
    ]
);

// 2.2 Criar vaga com salário negativo
testarEndpoint(
    "2.2. Regra de Negócio: Impedir vaga com salário negativo", 
    "$baseUrl/criar_vaga.php", 
    'POST',
    [
        "empresa_id" => $idEmpresaLogada, 
        "titulo" => "Estagiário Explorável", 
        "descricao" => "Descrição", 
        "salario" => -500.00, // Deve dar erro
        "cidade" => "São Paulo"
    ]
);

// 2.3 Criar vaga sem título
testarEndpoint(
    "2.3. Regra de Negócio: Impedir vaga com título muito curto/vazio", 
    "$baseUrl/criar_vaga.php", 
    'POST',
    [
        "empresa_id" => $idEmpresaLogada, 
        "titulo" => "TI", // Muito curto (menos de 5 chars)
        "descricao" => "Falta título", 
        "salario" => 2000.00, 
        "cidade" => "Rio de Janeiro"
    ]
);

// 2.4 Listar Vagas
testarEndpoint(
    "2.4. Listar todas as vagas ativas (GET)", 
    "$baseUrl/listar_vagas.php", 
    'GET'
);

// ==========================================
// MÓDULO 3: CANDIDATURAS
// ==========================================
echo "<h2>3. Candidaturas</h2>";

// 3.1 Candidatura com dados incompletos
testarEndpoint(
    "3.1. Validação de segurança: Impedir candidatura sem ID da Vaga", 
    "$baseUrl/candidatar.php", 
    'POST',
    ["candidato_id" => 1, "vaga_id" => null]
);

?>
<style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; padding: 20px; }
    b { color: #0056b3; }
    hr { border: 0; height: 1px; background: #ddd; margin: 15px 0; }
    h2 { color: #1e3a8a; margin-top: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 5px; }
</style>