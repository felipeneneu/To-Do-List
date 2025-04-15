<?php
session_start();
require_once("../connect.php");
require_once("../classes/Usuario.php");

$user = new Usuario($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $logado = $user->auth($email, $senha);

  if ($logado) {
    $_SESSION['usuario_id'] = $logado['id'];
    $_SESSION['usuario_nome'] = $logado['nome'];
    header("Location: ../index.php");
    exit();
  } else {
    echo "Email ou senha inválidos ou usuário inativo.";
  }
}
