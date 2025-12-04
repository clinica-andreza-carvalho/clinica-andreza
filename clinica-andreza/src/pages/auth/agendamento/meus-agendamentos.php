<?php
session_start();

// Se o cliente não estiver logado, redireciona
if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// CONEXÃO
$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// CANCELAR AGENDAMENTO
if (isset($_GET['cancelar'])) {
    $idCancel = intval($_GET['cancelar']);
    $sqlCancel = "UPDATE agendamentos SET status='cancelado' WHERE id=? AND cliente_id=?";
    $stmtCancel = $conn->prepare($sqlCancel);
    $stmtCancel->bind_param("ii", $idCancel, $cliente_id);
    $stmtCancel->execute();
    header("Location: meus-agendamentos.php"); 
    exit;
}

// PEGAR AGENDAMENTOS DO CLIENTE
$sql = "SELECT * FROM agendamentos WHERE cliente_id = ? ORDER BY data_agendamento DESC, horario DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Agendamentos</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
<link rel="stylesheet" href="meus-agendamentos.css">

</head>
<body>

<h2> Meus Agendamentos</h2>

<?php if ($result->num_rows == 0): ?>
    <p style="text-align:center; color:#555;">Você ainda não possui agendamentos.</p>
<?php else: ?>
<table>
    <tr>
        <th>Data</th>
        <th>Horário</th>
        <th>Serviço</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <?php $statusClass = "status-" . $row['status']; ?>
    <tr>
        <td><?= date("d/m/Y", strtotime($row['data_agendamento'])) ?></td>
        <td><?= substr($row['horario'], 0, 5) ?></td>
        <td><?= $row['servico'] ?></td>
        <td><span class="status <?= $statusClass ?>"><?= ucfirst($row['status']) ?></span></td>
        <td>
            <?php if($row['status'] != 'cancelado'): ?>
            <a class="btn btn-cancelar" href="?cancelar=<?= $row['id'] ?>" onclick="return confirm('Deseja realmente cancelar este agendamento?')">Cancelar</a>
            <?php else: ?>
            -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php endif; ?>

<a class="back-btn" href="http://localhost/clinica-andreza/src/pages/auth/pagina-do-cliente/cliente.php">⬅ Voltar</a>

</body>
</html>
