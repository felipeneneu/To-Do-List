<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/gh/Loopple/loopple-public-assets@main/motion-tailwind/motion-tailwind.css"
    rel="stylesheet">
</head>

<body class="bg-white rounded-lg py-5">
  <div class="container flex flex-col mx-auto bg-white rounded-lg pt-12 my-5">
    <div class="flex justify-center w-full h-full my-auto xl:gap-14 lg:justify-normal md:gap-5 draggable">
      <div class="flex items-center justify-center w-full lg:p-12">
        <div class="flex items-center xl:p-10">
          <form class="flex flex-col w-full h-full pb-6 text-center bg-white rounded-3xl" method="POST" id="login-form"
            action="login.php">
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


</body>
<html>