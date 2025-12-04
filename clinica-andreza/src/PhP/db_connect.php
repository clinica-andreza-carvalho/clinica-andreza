
<?php
$host = "localhost";
$db = "clinica_db";
$user = "root"; // ou outro usuário
$pass = '';     // coloque sua senha se tiver

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>


  