<?php
session_start();

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../auth/login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Conta | Clínica Andreza Carvalho</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pagina-cliente.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="main">

    <section class="bemvindo">
        <h2>Seus Detalhes</h2>
        <p>Aqui estão as informações da sua conta.</p>
    </section>

    <div>
        <a href="cliente.php" class="back-icon">&#8592; Voltar</a>
    </div>

    <section class="painel">
        <h3>Informações da Conta</h3>

        <ul class="dados-cliente" style="list-style: none; padding: 0;">
            <li class="info"><strong>Nome:</strong> <?= $_SESSION['cliente_nome'] ?></li>
            <li class="info"><strong>Email:</strong> <?= $_SESSION['cliente_email'] ?></li>
            <li class="info"><strong>Telefone:</strong> <?= $_SESSION['cliente_tel'] ?? "Não informado" ?></li>
            <li class="info"><strong>Data de Nascimento:</strong> <?= $_SESSION['cliente_nasc'] ?? "Não informado" ?></li>
        </ul>

    </section>

</main>

</body>
</html>
