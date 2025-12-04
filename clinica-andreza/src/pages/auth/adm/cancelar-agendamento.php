<?php
session_start();

// Verifica se admin está logado
if (!isset($_SESSION['adm_id'])) {
    header("Location: login-adm.php");
    exit;
}

// Conexão
$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) { die("Erro: " . $conn->connect_error); }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE agendamentos SET status='cancelado' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Redireciona de volta para o painel
header("Location: adm.php");
exit;
?>
