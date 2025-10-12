<?php
// Backend para o site DB Super
// Este script PHP lida com o envio do formulário de contato.
// Armazena os dados do cliente (nome, email, mensagem) em um banco de dados MySQL.
// A mensagem pode incluir detalhes sobre o serviço solicitado.

header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root"; // Usuário padrão do XAMPP
$password = ""; // Senha padrão do XAMPP (vazia)
$dbname = "db_super";

// Conexão com o MySQL (sem selecionar banco ainda)
$conn = new mysqli($servername, $username, $password);

// Verificar conexão
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Criar o banco de dados se não existir (com charset UTF-8 para suportar português)
$createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($createDbSql) !== TRUE) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao criar/verificar o banco: ' . $conn->error]);
    $conn->close();
    exit();
}

// Selecionar o banco
if (!$conn->select_db($dbname)) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao selecionar o banco: ' . $conn->error]);
    $conn->close();
    exit();
}

// Criar a tabela se não existir
$createTableSql = "
    CREATE TABLE IF NOT EXISTS solicitacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        mensagem TEXT NOT NULL,
        data_solicitacao DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";
if ($conn->query($createTableSql) !== TRUE) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao criar/verificar a tabela: ' . $conn->error]);
    $conn->close();
    exit();
}

// Receber dados do POST (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$nome = isset($data['nome']) ? trim($data['nome']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$mensagem = isset($data['mensagem']) ? trim($data['mensagem']) : '';

if (empty($nome) || empty($email) || empty($mensagem)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios']);
    $conn->close();
    exit();
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'E-mail inválido']);
    $conn->close();
    exit();
}

// Preparar e executar a inserção
$stmt = $conn->prepare("INSERT INTO solicitacoes (nome, email, mensagem, data_solicitacao) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $nome, $email, $mensagem);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Solicitação armazenada com sucesso! Entraremos em contato em breve.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao armazenar a solicitação: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>