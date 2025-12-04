<?php
session_start();

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) die("Erro ao conectar: " . $conn->connect_error);

$produtos = $conn->query("SELECT * FROM produtos ORDER BY id DESC");

// Horários fixos
$horarios_fixos = ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00"];

// Data selecionada: se não tiver, pega a data de hoje
$data_selecionada = $_POST['data'] ?? date('Y-m-d');

// ////////////////////////////
// CARREGA HORÁRIOS OCUPADOS
// ////////////////////////////
$ocupados = [];
if($data_selecionada){
    $stmt = $conn->prepare("SELECT horario FROM agendamentos WHERE data_agendamento=? AND status IN ('pendente','confirmado')");
    $stmt->bind_param("s", $data_selecionada);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){
        $ocupados[] = $row['horario'];
    }
    $stmt->close();
}

// ////////////////////////////
// CONFIRMAR AGENDAMENTO
// ////////////////////////////
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['horario']) && isset($_POST['servico'])){
    $horario = $_POST['horario'];
    $servico = $_POST['servico'];

    $stmt = $conn->prepare("INSERT INTO agendamentos (cliente_id, data_agendamento, horario, servico) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $cliente_id, $data_selecionada, $horario, $servico);

    if ($stmt->execute()) {
        header("Location: http://localhost/clinica-andreza/src/pages/auth/pagina-do-cliente/cliente.php");
        exit;
    } else {
        echo "<script>alert('Erro ao agendar: ".$conn->error."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agendamento - Clínica Andreza</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="agendamento1.css">

<script>
function selecionar(horario){
    // desmarca todos
    document.querySelectorAll(".h-btn").forEach(btn=>{
        btn.classList.remove("btn-selecionado");
    });

    // marca o selecionado
    document.getElementById("btn_"+horario.replace(":","")).classList.add("btn-selecionado");

    // seta input hidden
    document.getElementById("horario_input").value = horario;
}
</script>
</head>
<body>

<header class="header">
    <h1>Clínica Andreza Carvalho</h1>
    <p>Fisioterapeuta Dermatofuncional</p>
</header>

<div class="container">
    <h2>Agendar sessão</h2>

    <form method="POST">

        <label>Data</label>
        <input type="date" name="data" value="<?= $data_selecionada ?>" min="<?= date('Y-m-d') ?>" required onchange="this.form.submit()">

        <label>Horários</label>
        <input type="hidden" name="horario" id="horario_input">

        <div class="horarios">
            <?php foreach($horarios_fixos as $h): ?>
                <?php if(in_array($h,$ocupados)): ?>
                    <button type="button" class="h-btn btn-ocupado" disabled><?= $h ?><br>Ocupado</button>
                <?php else: ?>
                    <button
                        type="button"
                        id="btn_<?= str_replace(':','',$h) ?>"
                        class="h-btn btn-normal"
                        onclick="selecionar('<?= $h ?>')">
                        <?= $h ?><br>Disponível
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <label>Serviço</label>
        <div class="produtos-container">
            <?php while($p = $produtos->fetch_assoc()): ?>
                <div class="produto-item">
                    <input type="radio" name="servico" value="<?= $p['nome'] ?>" id="prod-<?= $p['id'] ?>" required>
                    <label for="prod-<?= $p['id'] ?>">
                        <img src="../adm/<?= $p['imagem'] ?>" alt="">
                        <h4><?= $p['nome'] ?></h4>
                        <p><?= $p['descricao'] ?></p>
                        <p>R$ <?= number_format($p['preco'],2,",",".") ?></p>
                    </label>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- BOTÕES CENTRALIZADOS E MESMO TAMANHO -->
        <div class="form-botoes">
            <button type="submit" class="btn-verde">Confirmar Agendamento</button>
            <a class="btn-verde" href="meus-agendamentos.php">Ver meus agendamentos</a>
        </div>
    </form>
</div>

<!-- Botão voltar igual à Área do Cliente -->
<div style="margin:20px 0; text-align:center;">
    <a href="http://localhost/clinica-andreza/src/pages/auth/pagina-do-cliente/cliente.php" class="back-icon">⬅ Voltar</a>
</div>

</body>
</html>
