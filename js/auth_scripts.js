// Mestre Kame diz: Vamos checar o Scouter (localStorage) do Guerreiro!
document.addEventListener("DOMContentLoaded", function () {
  const token = localStorage.getItem("jwtToken");
  if (token) {
    // Poderia adicionar uma validação rápida do token aqui se quisesse,
    // mas o mais comum é só redirecionar. A validação real ocorrerá
    // quando ele tentar acessar um recurso protegido.
    console.log(
      "Mestre Kame diz: Guerreiro já tem um Crachá JWT! Redirecionando..."
    );
    // Ajuste o caminho do redirecionamento se necessário
    window.location.href = "../index.php";
  } else {
    console.log("Mestre Kame diz: Guerreiro precisa se identificar.");
  }

  // --- Lógica para tratar o ENVIO do formulário via JavaScript ---
  const loginForm = document.getElementById("login-form");
  const errorMessageDiv = document.getElementById("error-message");

  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault(); // Impede o envio padrão do formulário HTML

    errorMessageDiv.textContent = ""; // Limpa erros anteriores
    const formData = new FormData(loginForm);

    try {
      // Mestre Kame diz: Enviando as credenciais para Bulma verificar...
      const response = await fetch(loginForm.action, {
        // Usa a action definida no form
        method: "POST",
        body: formData,
      });
      const result = await response.json(); // Espera uma resposta JSON
      if (response.ok && result.success && result.token) {
        // SUCESSO! Bulma enviou o Crachá JWT!
        console.log("Mestre Kame diz: Crachá recebido!", result.token);
        // Guarda o Crachá no Scouter (localStorage)
        localStorage.setItem("jwtToken", result.token);
        // Teletransporta para a área principal!
        window.location.href = "../index.php";
      } else {
        // FALHA! Bulma não deu o crachá. Mostra o erro.
        console.error("Mestre Kame diz: Falha no login.", result.message);
        errorMessageDiv.textContent =
          result.message || "Erro ao tentar fazer login.";
      }
    } catch (error) {
      // Erro na comunicação com o Quartel General (rede, script PHP com erro grave)
      console.error("Mestre Kame diz: Erro na comunicação!", error);
      errorMessageDiv.textContent =
        "Não foi possível conectar ao servidor de login.";
    }
  });
});
