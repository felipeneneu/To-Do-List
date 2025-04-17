<?php
// Arquivo: login_jwt.php (ou pode adaptar o processa_login.php)
// Mestre Kame diz: Preparar para forjar o Crachá Mágico JWT!

// --- PASSO 1: Preparação e Reforços ---
// (Não precisamos mais de session_start() para o login em si!)

// Chamar os reforços (includes) - Ajuste os caminhos se necessário
require '../vendor/autoload.php';
require_once("../connect.php");

// Chamar a ferramenta JWT da Capsule Corp (Biblioteca firebase/php-jwt)
// Certifique-se que rodou: composer require firebase/php-jwt
require_once("../classes/Usuario.php");

use Firebase\JWT\JWT;
// A classe Key não é usada para ENCODE, apenas para DECODE, mas bom ter o 'use' aqui.
// use Firebase\JWT\Key; // Será usada nos scripts que VERIFICAM o token

// --- PASSO 2: Verificar Método e Pegar Dados ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Mestre Kame diz: Checando as credenciais enviadas na cápsula POST...
  if (!isset($pdo)) {
    // Responder com erro JSON se $pdo não existir
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Erro Mestre: Falha na conexão com o banco de dados.']);
    exit();
  }
  $user = new Usuario($pdo);
  // Pegar dados (Lembre-se de validar/sanitizar em produção!)
  $email = filter_input(INPUT_POST, 'email') ?? null;
  $senha = filter_input(INPUT_POST, 'senha') ?? null;

  if (!$email || !$senha) {
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['sucess' => false, 'message' => 'Mestre Kame diz: Email e Senha são obrigatórios!']);
    exit();
  }

  // --- PASSO 3: Autenticação (Usando a Classe Usuario) ---
  $logado = $user->auth($email, $senha); // O método auth verifica no banco
  if ($logado) {
    // SUCESSO! Ki Compatível! Hora de forjar o Crachá JWT!
    // Mestre Kame diz: Credenciais corretas! Forjando o Crachá Mágico agora...

    // --- PASSO 4: Forjar o Crachá JWT ---

    // A Caneta Mágica Super Secreta da Bulma! (GUARDAR EM LOCAL SEGURO!)
    $chaveSecretaDaBulma = 'K@m3h@m3h@Sup3rS3cr3t0!NaoConteProVegeta123'; // NÃO DEIXE AQUI EM PRODUÇÃO!
    $agora = time();
    $server = 'SeuServidorCapsuleCorp.com';
    $paraQuem = 'UsuariosValidosApp';

    $payload = [
      'iss' => $server, // Emissor
      'aud' => $paraQuem, // Audiência
      'iat' => $agora, // Emitido Em
      'nbf' => $agora, // Não Válido Antes De
      'exp' => $agora + (60 * 60 * 1), // EXPIRAÇÃO! Essencial! (Ex: 1 hora)
      // Dados do Guerreiro (payload - o que o crachá informa)
      'data' => [
        'id' => $logado['id'],  // ID do usuário vindo do banco
        'nome' => $logado['nome']  // Nome do usuário vindo do banco
        // Adicione outras informações PÚBLICAS se necessário (ex: 'nivelAcesso')
        // NUNCA coloque senhas ou dados muito sensíveis aqui!
      ]

    ];

    // Bulma usa a Caneta Secreta para criar o Crachá assinado!
    $jwt = JWT::encode($payload, $chaveSecretaDaBulma, 'HS256');

    // --- PASSO 5: Entregar o Crachá para o Guerreiro (Responder com JSON) ---
    // Mestre Kame diz: Missão cumprida! Entregando o Crachá JWT!

    header('Content-Type: application/json');
    http_response_code(200); //ok
    echo json_encode([
      'success' => true,
      'message' => 'Login bem-sucedido!',
      'token' => $jwt // O Crachá Mágico!
    ]);
    exit();
  } else {
    // FALHA NA AUTENTICAÇÃO! Ki não bateu!
    // Mestre Kame diz: Hmm, Ki incompatível. Credenciais erradas ou Guerreiro meditando.
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Email ou senha inválidos ou usuário inativo.']);
    exit();
  }
} else {
  // Mestre Kame diz: Este Quartel General só aceita mensagens via POST!
  header('Content-Type: application/json');
  http_response_code(405); // Method Not Allowed
  echo json_encode(['success' => false, 'message' => 'Método não permitido. Use POST.']);
  exit();
}
