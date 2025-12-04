<?php
session_start();
if (!isset($_SESSION['adm_id'])) {
    header("Location: login-adm.php");
    exit;
}

$conn = new mysqli("localhost", "root", '', "clinica_db");
if ($conn->connect_error) { die("Erro: " . $conn->connect_error); }

if (!isset($_GET['id'])) { header("Location: adm.php#produtos"); exit; }
$id = intval($_GET['id']);

// Puxar produto
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto) { header("Location: adm.php#produtos"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $marca = $_POST['marca'];
    $preco = $_POST['preco'];
    $preco_promocional = $_POST['preco_promocional'];
    $tags = $_POST['tags'];

    $sql = "UPDATE produtos SET nome=?, descricao=?, marca=?, preco=?, preco_promocional=?, tags=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddssi", $nome, $descricao, $marca, $preco, $preco_promocional, $tags, $id);
    if ($stmt->execute()) {
        header("Location: adm.php#produtos");
        exit;
    } else {
        $erro = "Erro ao atualizar produto: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Produto</title>
<link rel="stylesheet" href="adm.css">
</head>
<body>
<div class="container">
    <div class="content" style="flex:1; padding:20px;">
        <h1>Editar Produto</h1>
        <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        <form method="POST">
            <label>Nome</label><br>
            <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required><br><br>

            <label>Descrição</label><br>
            <textarea name="descricao" rows="3" required><?= htmlspecialchars($produto['descricao']) ?></textarea><br><br>

            <label>Marca</label><br>
            <input type="text" name="marca" value="<?= htmlspecialchars($produto['marca']) ?>" required><br><br>

            <label>Preço</label><br>
            <input type="number" name="preco" step="0.01" value="<?= $produto['preco'] ?>" required><br><br>

            <label>Preço Promocional</label><br>
            <input type="number" name="preco_promocional" step="0.01" value="<?= $produto['preco_promocional'] ?>"><br><br>

            <label>Tags</label><br>
            <input type="text" name="tags" value="<?= htmlspecialchars($produto['tags']) ?>"><br><br>

            <button type="submit" class="btn">Atualizar Produto</button>
            <a href="adm.php#produtos" class="btn" style="background:#dc3545;">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
