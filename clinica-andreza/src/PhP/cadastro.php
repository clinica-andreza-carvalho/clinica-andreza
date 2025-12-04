<?php
session_start();

// CONEXÃO COM BANCO
$host = "localhost";
$user = "root";
$pass = '';
$db = "clinica_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro ao conectar ao banco: " . $conn->connect_error);
}

// VERIFICA MÉTODO
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acesso inválido");
}

// PEGA DADOS DO FORM
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';
$confirmSenha = $_POST['confirm-password'] ?? '';

// CONFIRMA SENHA
if ($senha !== $confirmSenha) {
    echo "<script>alert('As senhas não coincidem!'); history.back();</script>";
    exit();
}

// VERIFICA SE EMAIL JÁ EXISTE
$sqlCheck = "SELECT id FROM clientes WHERE email = ? LIMIT 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
if ($resCheck->num_rows > 0) {
    echo "<script>alert('Este email já está cadastrado!'); history.back();</script>";
    exit();
}

// CRIPTOGRAFA SENHA
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// INSERÇÃO NO BANCO
$sqlInsert = "INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("sss", $nome, $email, $senhaHash);

if ($stmtInsert->execute()) {
    // CADASTRO OK → REDIRECIONA PARA LOGIN
    header("Location: ../pages/auth/cadastro/login.html");
    exit();
}

?>
