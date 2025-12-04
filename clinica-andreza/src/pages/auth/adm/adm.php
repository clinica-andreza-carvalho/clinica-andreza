<?php
session_start();
if (!isset($_SESSION['adm_id'])) {
    header("Location: login-adm.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "clinica_db");
if ($conn->connect_error) { die("Erro na Conexão: " . $conn->connect_error); }

/* ----------- AGENDAMENTOS ----------- */
$sql = "SELECT a.id, a.data_agendamento, a.horario, a.servico, a.status, c.nome AS cliente_nome
        FROM agendamentos a
        JOIN clientes c ON a.cliente_id = c.id
        ORDER BY a.data_agendamento DESC, a.horario DESC";
$result = $conn->query($sql);

$totalPedidos = $conn->query("SELECT COUNT(*) AS total FROM agendamentos")->fetch_assoc()['total'];
$totalCancelados = $conn->query("SELECT COUNT(*) AS total FROM agendamentos WHERE status='cancelado'")->fetch_assoc()['total'];

/* ----------- Ações ----------- */
if(isset($_GET['cancelar'])){
    $id = intval($_GET['cancelar']);
    $conn->query("UPDATE agendamentos SET status='cancelado' WHERE id=$id");
    header("Location: adm.php?page=dashboard");
    exit;
}

if(isset($_GET['confirmar'])){
    $id = intval($_GET['confirmar']);
    $conn->query("UPDATE agendamentos SET status='confirmado' WHERE id=$id");
    header("Location: adm.php?page=dashboard");
    exit;
}

/* ----------- PRODUTOS ----------- */
if(isset($_POST['add_produto'])){
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $imagemPath='';

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
        if(!is_dir('uploads')) mkdir("uploads", 0777, true);
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $ext;
        $imagemPath = "uploads/" . $novoNome;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagemPath);
    }

    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $nome, $descricao, $preco, $imagemPath);
    $stmt->execute();
    header("Location: adm.php?page=produtos");
}

if(isset($_POST['edit_produto'])){
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
        if(!is_dir('uploads')) mkdir("uploads", 0777, true);
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $ext;
        $imagemPath = "uploads/" . $novoNome;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagemPath);

        $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?");
        $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $imagemPath, $id);
    } else {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, preco=? WHERE id=?");
        $stmt->bind_param("ssdi", $nome, $descricao, $preco, $id);
    }
    $stmt->execute();
    header("Location: adm.php?page=produtos");
}

if(isset($_GET['delete_produto'])){
    $conn->query("DELETE FROM produtos WHERE id=".intval($_GET['delete_produto']));
    header("Location: adm.php?page=produtos");
}

$produtos = $conn->query("SELECT * FROM produtos ORDER BY id DESC");

/* ----------- Aba ativa ----------- */
$activePage = $_GET['page'] ?? 'dashboard';

/* ----------- Produto para edição ----------- */
$edit_produto = null;
if(isset($_GET['edit_produto'])){
    $id = intval($_GET['edit_produto']);
    $res = $conn->query("SELECT * FROM produtos WHERE id=$id");
    $edit_produto = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>
<link rel="stylesheet" href="adm.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2 class="logo">Painel</h2>
        <nav>
            <a href="adm.php?page=dashboard" class="<?= $activePage=='dashboard'?'active':'' ?>">Dashboard</a>
            <a href="adm.php?page=produtos" class="<?= $activePage=='produtos'?'active':'' ?>">Produtos</a>
        </nav>

        <a class="back-icon" href="../../../../index.html">Voltar para a página inicial</a>
    </aside>

    <main class="content">

        <!-- DASHBOARD -->
        <?php if($activePage == 'dashboard'): ?>
        <h1>Dashboard</h1>
        <div class="cards">
            <div class="card">
                <h3>Total de Agendamentos</h3>
                <p class="valor"><?= $totalPedidos ?></p>
            </div>

            <div class="card">
                <h3>Cancelados</h3>
                <p class="valor cancelado"><?= $totalCancelados ?></p>
            </div>
        </div>

        <h2>Últimos Agendamentos</h2>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result->num_rows == 0): ?>
                <tr><td colspan="6">Nenhum agendamento encontrado</td></tr>
            <?php else:
                while($row = $result->fetch_assoc()):
                $statusClass = "status-" . $row["status"];
            ?>
                <tr>
                    <td><?= $row['cliente_nome'] ?></td>
                    <td><?= $row['servico'] ?></td>
                    <td><?= date("d/m/Y", strtotime($row['data_agendamento'])) ?></td>
                    <td><?= substr($row['horario'], 0, 5) ?></td>
                    <td><span class="status <?= $statusClass ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <?php if($row['status']=="pendente"): ?>
                            <a class="btn" href="?confirmar=<?= $row['id'] ?>&page=dashboard">Confirmar</a>
                            <a class="btn btn-cancelar" href="?cancelar=<?= $row['id'] ?>&page=dashboard">Cancelar</a>
                        <?php elseif($row['status']=="confirmado"): ?>
                            <a class="btn btn-cancelar" href="?cancelar=<?= $row['id'] ?>&page=dashboard">Cancelar</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- PRODUTOS -->
        <?php if($activePage == 'produtos'): ?>
        <h1>Produtos</h1>

        <form method="POST" enctype="multipart/form-data">
            <h3><?= isset($_GET['edit_produto']) ? 'Editar Produto' : 'Adicionar Produto' ?></h3>

            <?php if(isset($_GET['edit_produto'])): ?>
                <input type="hidden" name="id" value="<?= $edit_produto['id'] ?>">
            <?php endif; ?>

            <input type="text" name="nome" placeholder="Nome" value="<?= $edit_produto['nome'] ?? "" ?>" required>
            <textarea name="descricao" placeholder="Descrição"><?= $edit_produto['descricao'] ?? "" ?></textarea>
            <input type="text" name="preco" placeholder="Preço" value="<?= $edit_produto['preco'] ?? "" ?>" required>

            <label>Imagem:</label>
            <input type="file" name="imagem">

            <button type="submit" name="<?= isset($_GET['edit_produto']) ? 'edit_produto' : 'add_produto' ?>">
                <?= isset($_GET['edit_produto']) ? "Salvar Alteração" : "Adicionar Produto" ?>
            </button>
        </form>

        <div class="produtos-grid">
            <?php while($p = $produtos->fetch_assoc()): ?>
            <div class="produto-card">
                <img src="<?= $p['imagem'] ?: 'https://via.placeholder.com/200' ?>">
                <h3><?= $p['nome'] ?></h3>
                <p><?= $p['descricao'] ?></p>
                <p><b>R$ <?= number_format($p['preco'], 2, ',', '.') ?></b></p>

                <a class="btn" href="adm.php?page=produtos&edit_produto=<?= $p['id'] ?>">Editar</a>
                <a class="btn btn-cancelar" onclick="return confirm('Excluir produto?')" href="adm.php?page=produtos&delete_produto=<?= $p['id'] ?>">Excluir</a>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
