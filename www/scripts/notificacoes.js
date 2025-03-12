// Arquivo: scripts/notificacoes.js
document.addEventListener("DOMContentLoaded", function () {
  // Verificar notificações a cada 2 minutos (120000 ms)
  const intervaloVerificacao = 120000;

  // Função para verificar notificações
  function verificarNotificacoes() {
    fetch("verificar_notificacoes.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.temNovos) {
          // Exibir badge ou notificação visual
          const badge = document.getElementById("notificacao-badge");
          if (badge) {
            badge.textContent = data.quantidade;
            badge.style.display = "inline-block";
          }

          // Notificar o usuário com uma mensagem no topo da página, se houver notificações novas
          if (data.novasNotificacoes) {
            const notificacaoContainer = document.createElement("div");
            notificacaoContainer.className =
              "alert alert-info alert-dismissible fade show m-3";
            notificacaoContainer.role = "alert";
            notificacaoContainer.innerHTML = `
                            <strong>Novas notificações!</strong> Você tem ${data.quantidade} relatórios compartilhados pendentes.
                            <a href="views/relatorios_compartilhados.php" class="alert-link">Ver relatórios</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;

            // Inserir no topo da página
            const primeiroElemento = document.body.firstChild;
            document.body.insertBefore(notificacaoContainer, primeiroElemento);
          }
        }
      })
      .catch((error) =>
        console.error("Erro ao verificar notificações:", error)
      );
  }

  // Verificar na carga da página
  verificarNotificacoes();

  // Configurar intervalo para verificação periódica
  setInterval(verificarNotificacoes, intervaloVerificacao);
});
