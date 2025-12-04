<?php
session_start();

// CONEXÃO COM BANCO
$host = "localhost";
$user = "root";
$pass = '';
$db = "clinica_db";

$conn = new mysqli($host, $user, $pass, $db);

// ERRO DE CONEXÃO
if ($conn->connect_error) {
    header("Location: ../pages/auth/cadastro/erro-login.html");
    exit;
}

// VERIFICA MÉTODO
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/auth/cadastro/erro-login.html");
    exit;
}

// PEGA DADOS
$email = $_POST['email'];
$senha = $_POST['password'];

// CONSULTA
$sql = "SELECT * FROM clientes WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

// NÃO EXISTE USUÁRIO
if ($res->num_rows === 0) {
    header("Location: ../pages/auth/cadastro/erro-login.html");
    exit;
}

$user = $res->fetch_assoc();

// SENHA ERRADA
if (!password_verify($senha, $user['senha'])) {
    header("Location: ../pages/auth/cadastro/erro-login.html");
    exit;
}

// LOGIN OK
$_SESSION['cliente_id'] = $user['id'];
$_SESSION['cliente_nome'] = $user['nome'];

// REDIRECIONA PARA A PÁGINA DO CLIENTE
header("Location: http://localhost/clinica-andreza/src/pages/auth/pagina-do-cliente/cliente.php");
exit();
?>
