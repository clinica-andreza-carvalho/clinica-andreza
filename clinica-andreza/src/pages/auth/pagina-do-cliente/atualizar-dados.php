<?php
session_start();

// Bloqueio de acesso
if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../auth/login.html");
    exit;
}

$host = "localhost";
$user = "root";
$pass = '';
$db = "clinica_db";

// Conexão
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// Dados do formulário
$cliente_id = $_SESSION['cliente_id'];
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$tel = trim($_POST['telefone']);
$nasc = trim($_POST['data_nascimento']);

// Verificar se o email já existe em outro cliente
$sql_check = "SELECT id FROM clientes WHERE email=? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $email, $cliente_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if($result_check->num_rows > 0) {
    header("Location: cliente.php?status=email_erro");
    exit;
}

// Atualizar dados
$sql = "UPDATE clientes SET nome=?, email=?, telefone=?, nascimento=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $nome, $email, $tel, $nasc, $cliente_id);

if ($stmt->execute()) {
    // Atualiza sessão
    $_SESSION['cliente_nome'] = $nome;
    $_SESSION['cliente_email'] = $email;
    $_SESSION['cliente_tel'] = $tel;
    $_SESSION['cliente_nasc'] = $nasc;

    header("Location: cliente.php?status=ok");
    exit;
}

// Se deu erro geral
header("Location: cliente.php?status=erro");
exit;
