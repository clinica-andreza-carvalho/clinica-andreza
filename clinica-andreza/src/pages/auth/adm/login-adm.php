<?php
session_start();

// Conexão
$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['password']; // atenção: name="password" no HTML

    $stmt = $conn->prepare("SELECT * FROM administradores WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($senha, $admin['senha'])) {
        $_SESSION['adm_id'] = $admin['id'];
        $_SESSION['adm_nome'] = $admin['nome'];
        header("Location: adm.php");
        exit;
    } else {
        $erro = "Email ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Administrador</title>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../cadastro/cadastro.css">
</head>
<body>

<dialog open>

    <a href="../../../../index.html" class="back-icon">&#8592;</a>

    <div class="header">
      <div class="logo-circle">
        <img src="../../../assets/imagens-header/image/logo-clinica.png" alt="logo-clinica">
      </div>
      <div>
        <h1 class="clinic-name">Clínica Andreza Carvalho</h1>
        <p class="subtitle">Fisioterapeuta Dermatofuncional</p>
      </div>
    </div>

    <?php if (!empty($erro)) echo "<p style='color:red; text-align:center;'>$erro</p>"; ?>

    <form action="" method="POST">
      <h2 style="color:#5a3e3e; margin-bottom: 20px;">Login Administrador</h2>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Digite seu email" required />

      <label for="password">Senha</label>
      <input type="password" id="password" name="password" placeholder="**********" required />

      <button type="submit" class="btn-submit">Entrar</button>
    </form>

    
</dialog>

</body>
</html>
