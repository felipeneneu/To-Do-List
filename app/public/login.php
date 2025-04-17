<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/gh/Loopple/loopple-public-assets@main/motion-tailwind/motion-tailwind.css"
    rel="stylesheet">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>

<body class="bg-white rounded-lg py-5">
  <div class="container flex flex-col mx-auto bg-white rounded-lg pt-12 my-5">
    <div class="flex justify-center w-full h-full my-auto xl:gap-14 lg:justify-normal md:gap-5 draggable">
      <div class="flex items-center justify-center w-full lg:p-12">
        <div class="flex items-center xl:p-10">
          <form class="flex flex-col w-full h-full pb-6 text-center bg-white rounded-3xl" method="POST" id="login-form">
            <h3 class="mb-3 text-4xl font-extrabold text-dark-grey-900">Sign In</h3>
            <p class="mb-4 text-grey-700">Entre com email ou senha</p>

            <div class="flex items-center mb-3">
              <hr class="h-0 border-b border-solid border-grey-500 grow">
              <p class="mx-4 text-grey-600">or</p>
              <hr class="h-0 border-b border-solid border-grey-500 grow">
            </div>
            <label for="email" class="mb-2 text-sm text-start text-grey-900">Email*</label>
            <input id="email" name="email" type="email" placeholder="email@email.com"
              class="flex items-center w-full px-5 py-4 mr-2 text-sm font-medium outline-none focus:bg-grey-400 mb-7 placeholder:text-grey-700 bg-grey-200 text-dark-grey-900 rounded-2xl" />
            <label for="password" class="mb-2 text-sm text-start text-grey-900">Senha*</label>
            <input id="password" name="senha" type="password" placeholder="Entre com sua senha"
              class="flex items-center w-full px-5 py-4 mb-5 mr-2 text-sm font-medium outline-none focus:bg-grey-400 placeholder:text-grey-700 bg-grey-200 text-dark-grey-900 rounded-2xl" />
            <div id="error-message" class="mb-4 text-red-500 text-sm"></div>
            <div class="flex flex-row justify-between mb-8">

              <a href="javascript:void(0)" class="mr-4 text-sm font-medium text-purple-blue-500">Esqueceu sua senha?</a>
            </div>
            <button
              class="w-full px-6 py-5 mb-5 text-sm font-bold leading-none text-white transition duration-300 md:w-96 rounded-2xl hover:bg-purple-blue-600 focus:ring-4 focus:ring-purple-blue-100 bg-purple-blue-500"
              type="submit">Entrar</button>
            <p class="text-sm leading-relaxed text-grey-900">Não se registrou? <a href="javascript:void(0)"
                class="font-bold text-grey-700">Crie sua Conta</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="flex flex-wrap -mx-3 my-5">
    <div class="w-full max-w-full sm:w-3/4 mx-auto text-center">
      <p class="text-sm text-slate-500 py-1">
        ToDoLIST <a href="https://www.loopple.com/theme/motion-landing-library?ref=tailwindcomponents"
          class="text-slate-700 hover:text-slate-900" target="_blank">Criado </a> por: <a href="https://www.loopple.com"
          class="text-slate-700 hover:text-slate-900" target="_blank">Felipe Neneu</a>.
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    // Seleciona o formulário de login usando o seu ID.
    const form = document.querySelector("#login-form");
    // Adiciona um "ouvinte" para o evento de "submit" (quando o botão de "Entrar" é clicado).
    form.addEventListener('submit', async function(event) {
      // Impede o comportamento padrão do formulário de recarregar a página.
      event.preventDefault()
      try {
        // Cria um objeto FormData para pegar todos os dados do formulário (email e senha).
        const formData = new FormData(event.target);

        // Envia uma requisição POST (mais segura para dados de login) para o arquivo '../../pages/login.php' com os dados do formulário usando a biblioteca Axios.
        const {
          data
        } = await axios.post('../../pages/login.php', formData);

        // Se a requisição for bem-sucedida, 'data' conterá a resposta do servidor (provavelmente o token de login).
        // Guarda esse token na sessionStorage (armazenamento temporário no navegador que dura enquanto a aba está aberta) com a chave 'session'.
        sessionStorage.setItem('session', data);
        // Cria uma string com o formato "Bearer SEU_TOKEN" para enviar no cabeçalho de autorização.
        const authSession = 'Bearer ' + sessionStorage.getItem('session');
        // Faz uma requisição GET para o arquivo 'auth.php' (o arquivo que você mostrou antes para verificar o token).
        const authResponse = await axios.get('auth.php', {
          headers: {
            // Envia o token no cabeçalho "Authorization".
            "Authorization": authSession
          }
        })
        // Exibe a resposta do 'auth.php' no console do navegador (para você ver se o token é válido).
        console.log('Auth:', authResponse.data);
        // *** AQUI É O PONTO CRUCIAL PARA REDIRECIONAR PARA O INDEX ***
        // Se a autenticação for bem-sucedida (o 'auth.php' retornar um sucesso),
        // você deve redirecionar o usuário para a página index.php.
        window.location.href = 'index.php'; // Redireciona para a página index.php
      } catch (error) {
        // Se ocorrer algum erro durante a requisição (por exemplo, o login falhou),
        // exibe o erro no console do navegador para ajudar a depurar.
        console.log(error);
        // *** AQUI VOCÊ PODE MOSTRAR UMA MENSAGEM DE ERRO NA TELA ***
        // Por exemplo, atualizando o conteúdo da div com o id 'error-message'.
        const errorMessageDiv = document.getElementById('error-message');
        if (error.response && error.response.data && error.response.data.error) {
          errorMessageDiv.textContent = error.response.data.error;
        } else {
          errorMessageDiv.textContent = 'Erro ao fazer login.';
        }
      }
    })
  </script>

</body>
<html>