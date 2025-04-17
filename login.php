<?php
session_start();
require_once './vendor/autoload.php';

use app\database\Connection;

require_once("./classes/Usuario.php");

$pdo = Connection::connect();
$user = new Usuario($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $logado = $user->auth($email, $senha);

  if ($logado) {
    $_SESSION['usuario_id'] = $logado['id'];
    $_SESSION['usuario_nome'] = $logado['nome'];
    header("Location: ./index.php");
    exit();
  } else {
    echo "Email ou senha inválidos ou usuário inativo.";
  }
}
