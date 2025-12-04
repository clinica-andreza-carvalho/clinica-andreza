<?php
session_start();
if (!isset($_SESSION['adm_id'])) {
    header("Location: login-adm.php");
    exit;
}

$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) { die("Erro: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $marca = $_POST['marca'];
    $preco = $_POST['preco'];
    $preco_promocional = $_POST['preco_promocional'];
    $tags = $_POST['tags'];

    // Inserir no banco
    $sql = "INSERT INTO produtos (nome, descricao, marca, preco, preco_promocional, tags) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddss", $nome, $descricao, $marca, $preco, $preco_promocional, $tags);

    if ($stmt->execute()) {
        header("Location: adm.php#produtos");
        exit;
    } else {
        $erro = "Erro ao cadastrar produto: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastrar Produto</title>
<link rel="stylesheet" href="adm.css">
</head>
<body>
<div class="container">
    <div class="content" style="flex:1; padding:20px;">
        <h1>Cadastrar Produto</h1>
        <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        <form method="POST">
            <label>Nome</label><br>
            <input type="text" name="nome" required><br><br>

            <label>Descrição</label><br>
            <textarea name="descricao" rows="3" required></textarea><br><br>

            <label>Marca</label><br>
            <input type="text" name="marca" required><br><br>

            <label>Preço</label><br>
            <input type="number" name="preco" step="0.01" required><br><br>

            <label>Preço Promocional</label><br>
            <input type="number" name="preco_promocional" step="0.01"><br><br>

            <label>Tags</label><br>
            <input type="text" name="tags" placeholder="Separe por vírgula"><br><br>

            <button type="submit" class="btn">Cadastrar Produto</button>
            <a href="adm.php#produtos" class="btn" style="background:#dc3545;">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
