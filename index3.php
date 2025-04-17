<?php
// Arquivo: index.php (Página principal protegida)

// Mestre Kame diz: Reforços primeiro! Conexão e Classe Tarefa.
require_once("connect.php");
require_once("./classes/Tarefa.php"); // Verifique se o caminho "./classes/" está correto

// Mestre Kame diz: Agora a NOVA Barreira de Ki! Verificação do Crachá JWT!
require_once 'vendor/autoload.php'; // Carrega a biblioteca JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;


// --- INÍCIO DA VERIFICAÇÃO JWT ---

$jwt = null;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null; // Pega o cabeçalho Authorization

// Mestre Kame diz: O Guerreiro mostrou o Scouter (Authorization Header)?
if ($authHeader) {
  // Verifica se o formato é "Bearer <token>"
  if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwt = $matches[1];
  } else {
    // Formato inválido
    header('HTTP/1.1 400 Bad Request');
    echo 'Formato de token inválido.';
    // Ou redirecionar para o login:
    // header('Location: ./pages/app.php?erro=token_invalido_formato');
    exit();
  }
}

// Mestre Kame diz: Sem Scouter (token), sem entrada na Sala do Tempo!
if (!$jwt) {
  header('HTTP/1.1 401 Unauthorized'); // Não autorizado
  // Redireciona para a página de login (ajuste o caminho se necessário)
  header('Location: ./pages/app.php?erro=nao_autenticado');
  exit();
}

// Mestre Kame diz: Verificando a assinatura e validade do Scouter (token)...
$chaveSecretaDaBulma = 'K@m3h@m3h@Sup3rS3cr3t0!NaoConteProVegeta123'; // A MESMA chave secreta usada no login_jwt.php! GUARDE BEM!
$dadosDecodificados = null;

try {
  $keyObject = new Key($chaveSecretaDaBulma, 'HS256'); // Prepara a chave para verificação
  $dadosDecodificados = JWT::decode($jwt, $keyObject); // Tenta decodificar e validar!

  // Mestre Kame diz: Scouter válido! Pegando os dados do Guerreiro DE DENTRO do Scouter!
  // Certifique-se que 'data', 'id' e 'nome' existem no payload que você criou em login_jwt.php
  if (!isset($dadosDecodificados->data->id) || !isset($dadosDecodificados->data->nome)) {
    throw new Exception('Payload do token incompleto.');
  }
  $userIdFromJwt = $dadosDecodificados->data->id;   // <--- ID do Usuário vindo do JWT
  $userNameFromJwt = $dadosDecodificados->data->nome; // <--- Nome do Usuário vindo do JWT

} catch (ExpiredException $e) {
  // Mestre Kame diz: Este Scouter virou pó! (Expirou)
  header('HTTP/1.1 401 Unauthorized');
  header('Location: ./pages/app.php?erro=token_expirado');
  exit();
} catch (SignatureInvalidException $e) {
  // Mestre Kame diz: ALERTA! Assinatura do Scouter não confere! Falsificação?
  header('HTTP/1.1 401 Unauthorized');
  header('Location: ./pages/app.php?erro=token_invalido_assinatura');
  exit();
} catch (Exception $e) { // Outros erros (malformado, payload inesperado, etc.)
  // Mestre Kame diz: Hmm, problema estranho com este Scouter.
  header('HTTP/1.1 400 Bad Request');
  echo 'Erro ao processar token: ' . $e->getMessage();
  // Ou redirecionar para o login:
  // header('Location: ./pages/app.php?erro=token_invalido');
  exit();
}

// --- FIM DA VERIFICAÇÃO JWT ---
// Se o script chegou até aqui, o usuário está autenticado via JWT!
// $userIdFromJwt e $userNameFromJwt contêm os dados do usuário.

// Mestre Kame diz: Preparando as ferramentas de Tarefa para o Guerreiro autenticado!
if (!isset($pdo)) {
  die("Erro Mestre: A conexão com o banco de dados (\$pdo) não foi estabelecida em connect.php!");
}
$tarefa = new Tarefa($pdo);

// --- Processamento dos Formulários (Add, Complete, Delete) ---
// Mestre Kame diz: Agora usamos o ID que veio do Scouter (JWT) para as ações!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'salvar' && isset($_POST['titulo'])) {
    $titulo = filter_input(INPUT_POST, 'titulo');
    $descricao = filter_input(INPUT_POST, 'descricao');
    // USA O ID DO JWT AQUI!
    $tarefa->salvar($titulo, $descricao, $userIdFromJwt);
    header("Location: index.php"); // Recarrega a página para ver a nova tarefa
    exit();
  }

  if ($action === 'concluir' && isset($_POST['id'])) {
    // Aqui geralmente não precisamos do ID do usuário, só o ID da tarefa,
    // mas seria bom verificar se a tarefa pertence ao usuário $userIdFromJwt antes de concluir.
    // (A classe Tarefa->concluir talvez precise dessa lógica)
    $tarefa->concluir($_POST['id']);
    header("Location: index.php");
    exit();
  }

  if ($action === 'deletar' && isset($_POST['id'])) {
    // Mesma coisa aqui: verificar permissão antes de deletar.
    $tarefa->deletar($_POST['id']);
    header("Location: index.php");
    exit();
  }
}

// --- Busca de Dados ---
// Mestre Kame diz: Listando as missões (tarefas) APENAS deste Guerreiro (identificado pelo JWT)!
$tarefas = $tarefa->listar($userIdFromJwt); // USA O ID DO JWT AQUI!
$total = count($tarefas);
$concluidas = count(array_filter($tarefas, fn($t) => $t['concluido']));
$progresso = $total > 0 ? round(($concluidas / $total) * 100) : 0;
// Lógica da cor da barra (continua igual)
$corBarra = match (true) {
  $progresso <= 30 => 'bg-red-500',
  $progresso <= 70 => 'bg-yellow-400',
  $progresso < 100 => 'bg-blue-500',
  default => 'bg-green-600',
};

// Agora o restante do HTML pode usar $userNameFromJwt e $tarefas normalmente
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Lista de Tarefas</title>
  <link href="output.css" rel="stylesheet">
</head>

<body class="bg-green-100 min-h-screen p-8">

  <div class="max-w-xl mx-auto bg-white shadow-lg p-6 rounded-lg">
    <a href="index.php"><img src="./logo.svg" alt="" class="max-w-[200px] mx-auto mb-4"></a>

    <h1 class="text-3xl font-bold mb-4 text-center text-slate-600">📝 Bem vindo: <?= htmlspecialchars($userNameFromJwt) ?></h1>

    <form action="index.php" method="POST" class="flex flex-col gap-4 mb-6">
    </form>

    <?php if ($total > 0): ?>
    <?php endif; ?>
    <ul class="space-y-4">
      <?php foreach ($tarefas as $t): ?>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="flex flex-wrap -mx-3 my-5">
    <div class="w-full max-w-full sm:w-3/4 mx-auto text-center">
      <p class="text-sm py-1">
        <a href="#" id="logout-link" class="text-green-900 hover:text-slate-900">Clique para sair.</a>
      </p>
    </div>
  </div>

  <script>
    // Mestre Kame diz: Ensinando o Guerreiro a descartar o Scouter (logout)
    document.addEventListener('DOMContentLoaded', function() {
      const logoutLink = document.getElementById('logout-link');
      if (logoutLink) {
        logoutLink.addEventListener('click', function(event) {
          event.preventDefault(); // Impede o link de navegar para '#'

          // Mestre Kame diz: Esquecendo o Crachá/Scouter...
          localStorage.removeItem('jwtToken'); // Remove o token do localStorage

          // Mestre Kame diz: De volta ao portão de entrada!
          // Ajuste o caminho para a página de login se necessário
          window.location.href = './pages/app.php';
        });
      }
    });
  </script>

</body>

</html>