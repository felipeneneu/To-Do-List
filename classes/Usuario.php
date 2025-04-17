<?php
require_once __DIR__ . '/../vendor/autoload.php'; // caminho ajustado conforme estrutura do seu projeto


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class Usuario
{
  private $pdo;
  private $chaveSecretaDaBulma = 'K@m3h@m3h@Sup3rS3cr3t0!NaoConteProVegeta123';

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

    if ($user && password_verify($senha, $user['senha'])) {
      // Aqui criamos o crachá (token)
      $payload = [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'exp' => time() + 3600 // expira em 1 hora
      ];

      $jwt = JWT::encode($payload, $this->chaveSecretaDaBulma, 'HS256');
      return $jwt;
    }

    return false;
  }

  /** @test */
  public function verificarToken($token)
  {
    try {
      $dados = JWT::decode($token, new Key($this->chaveSecretaDaBulma, 'HS256'));
      return $dados;
    } catch (Exception $e) {
      return false;
    }
  }
}