<?php

class Usuario
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function auth($email, $senha)
  {
    $auth = $this->pdo->prepare("SELECT * FROM usuario WHERE email = :email AND ativo = 1");
    $auth->bindValue(":email", $email);
    $auth->execute();
    $user = $auth->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['senha'] === $senha) {
      return $user;
    }
    return false;
  }
}
