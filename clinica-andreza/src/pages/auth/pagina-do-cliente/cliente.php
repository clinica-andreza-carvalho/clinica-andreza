<?php
session_start();

// Bloqueio de acesso
if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../auth/login.html");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Conexão
$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// Pegar dados do cliente do banco
$sql_cliente = "SELECT nome, email, telefone, nascimento FROM clientes WHERE id=?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $cliente_id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$clienteDados = $result_cliente->fetch_assoc();

// Atualiza sessão
$_SESSION['cliente_nome'] = $clienteDados['nome'];
$_SESSION['cliente_email'] = $clienteDados['email'];
$_SESSION['cliente_tel'] = $clienteDados['telefone'];
$_SESSION['cliente_nasc'] = $clienteDados['nascimento'];

// Pegar próximo agendamento
$sql_agendamento = "SELECT * FROM agendamentos WHERE cliente_id=? AND status IN ('pendente','confirmado') ORDER BY data_agendamento ASC, horario ASC LIMIT 1";
$stmt_agendamento = $conn->prepare($sql_agendamento);
$stmt_agendamento->bind_param("i", $cliente_id);
$stmt_agendamento->execute();
$result_agendamento = $stmt_agendamento->get_result();
$proximoAgendamento = $result_agendamento->fetch_assoc();

// Mensagem de status
$statusMsg = "";
$statusTipo = ""; // sucesso ou erro
if(isset($_GET['status'])) {
    if($_GET['status'] == "ok") {
        $statusMsg = "Dados atualizados com sucesso!";
        $statusTipo = "sucesso";
    }
    if($_GET['status'] == "email_erro") {
        $statusMsg = "Este email já está cadastrado!";
        $statusTipo = "erro";
    }
    if($_GET['status'] == "erro") {
        $statusMsg = "Ocorreu um erro ao atualizar.";
        $statusTipo = "erro";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Cliente | Andreza Carvalho</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="pagina-cliente.css">
  <link rel="stylesheet" href="style.css">

  <style>
    /* Mensagens de status com animação */
    .status-msg {
        text-align: center;
        font-weight: bold;
        padding: 12px 20px;
        border-radius: 12px;
        margin: 20px auto;
        max-width: 500px;
        font-size: 16px;
        opacity: 0;
        animation: mostrarMsg 5s forwards; /* animação de 5s */
    }
    .status-msg.sucesso {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-msg.erro {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @keyframes mostrarMsg {
        0% { opacity: 0; transform: translateY(-10px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
  </style>
</head>
<body>
<main class="main">

    <section class="bemvindo">
      <h2>Bem-vinda(o), <strong><?= htmlspecialchars($clienteDados['nome']) ?></strong></h2>
      <p>Aqui você pode gerenciar seus dados e agendamentos.</p>
    </section>

    <div>
      <a href="../../../../index.html" class="back-icon">Voltar para a página inicial!</a>  
    </div>

    <?php if($statusMsg): ?>
      <div class="status-msg <?= $statusTipo ?>"><?= htmlspecialchars($statusMsg) ?></div>
    <?php endif; ?>

    <section class="painel">
      <h3>Seus Dados</h3>
      <form class="dados-cliente" method="POST" action="atualizar-dados.php">
        <div class="info">
          <label>Nome Completo</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($clienteDados['nome']) ?>" required>
        </div>

        <div class="info">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($clienteDados['email']) ?>" required>
        </div>

        <div class="info">
          <label>Telefone</label>
          <input type="text" name="telefone" value="<?= htmlspecialchars($clienteDados['telefone']) ?>">
        </div>

        <div class="info">
          <label>Data de Nascimento</label>
          <input type="date" name="data_nascimento" value="<?= $clienteDados['nascimento'] ?>">
        </div>

        <div class="acoes-form">
          <button type="submit" class="btn">Salvar Alterações</button>
          <a href="detalhes.php" class="btn btn-detalhes">Ver Detalhes</a>
        </div>
      </form>
    </section>

    <section class="proximo-agendamento">
      <h3>Próximo Agendamento</h3>
      <?php if ($proximoAgendamento): 
        $status = $proximoAgendamento['status'];
        $statusClass = "status-" . $status; 
      ?>
        <div class="agendamento-box">
          <p><strong>Data:</strong> <?= date("d/m/Y", strtotime($proximoAgendamento['data_agendamento'])) ?></p>
          <p><strong>Horário:</strong> <?= substr($proximoAgendamento['horario'], 0, 5) ?></p>
          <p><strong>Serviço:</strong> <?= htmlspecialchars($proximoAgendamento['servico']) ?></p>
          <p><strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= ucfirst($status) ?></span></p>
        </div>
      <?php else: ?>
        <p>Você não possui agendamentos ativos.</p>
      <?php endif; ?>

      <div class="acoes-agendamento">
        <a href="../agendamento/agendamento.php" class="btn">Fazer Novo Agendamento</a>
        <a href="../agendamento/meus-agendamentos.php" class="btn">Ver Meus Agendamentos</a>
      </div>
    </section>

</main>
</body>
</html>
