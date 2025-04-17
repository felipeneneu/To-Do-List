<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require_once("../connect.php");
require_once("../classes/Usuario.php");

header("Content-Type: application/json");


$user = new Usuario($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $token = $user->auth($email, $senha);

  if ($token) {
    echo json_encode(['token' => $token]);
  } else {
    http_response_code(401);
    echo json_encode(['erro' => 'Email ou senha inválidos ou usuário inativo.']);
  }
}