if (typeof jQuery == "undefined") {
  console.error("jQuery não foi carregado.");
} else {
  console.log("jQuery foi carregado.");
}

document.addEventListener("DOMContentLoaded", () => {
  const btnCadastrar = document.getElementById("btn-cadastrar");
  if (btnCadastrar) {
    const modalCadastro = new bootstrap.Modal(
      document.getElementById("modalCadastro")
    );
    btnCadastrar.addEventListener("click", () => {
      modalCadastro.show();
    });
  } else {
    console.error("Botão de cadastro não encontrado.");
  }

  const btnAtualizar = document.getElementById("btn-atualizar");
  const modalAtualizar = new bootstrap.Modal(
    document.getElementById("modalAtualizacao")
  );
  btnAtualizar.addEventListener("click", () => {
    modalAtualizar.show();
  });

  const btnRelatorio = document.getElementById("btn-relatorio");
  const modalRelatorio = new bootstrap.Modal(
    document.getElementById("modalRelatorio")
  );
  btnRelatorio.addEventListener("click", () => {
    modalRelatorio.show();
  });

  const btnRemover = document.getElementById("btn-remover");
  if (btnRemover) {
    const modalRemover = new bootstrap.Modal(
      document.getElementById("modalRemover")
    );
    btnRemover.addEventListener("click", () => {
      modalRemover.show();
    });
  } else {
    console.error("Botão de remoção não encontrado.");
  }

  const btnAtualizarEmMassa = document.getElementById("btn-atualizarEmMassa");
  const modalAtualizarEmMassa = new bootstrap.Modal(
    document.getElementById("modalAtualizar")
  );
  btnAtualizarEmMassa.addEventListener("click", () => {
    modalAtualizarEmMassa.show();
  });

  // Formulário de cadastro - submit via AJAX
  const formCadastro = document.getElementById("formCadastro");
  if (formCadastro) {
    formCadastro.addEventListener("submit", function (event) {
      event.preventDefault();

      const formData = new FormData(this);

      // Mostrar indicador de carregamento
      const submitBtn = formCadastro.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...';
      submitBtn.disabled = true;

      fetch("database/cadastrar.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          return response.text();
        })
        .then((text) => {
          // Extrair a parte JSON da resposta
          let jsonData = null;
          try {
            // Procurar por padrão JSON na resposta
            const jsonMatch = text.match(/(\{.*\})/);
            if (jsonMatch && jsonMatch[0]) {
              jsonData = JSON.parse(jsonMatch[0]);
              console.log("JSON extraído:", jsonData);
            }
          } catch (e) {
            console.error("Erro ao extrair JSON:", e);
          }

          if (jsonData && jsonData.success) {
            // Criar elemento de notificação de sucesso
            const notification = document.createElement("div");
            notification.className =
              "alert alert-success alert-dismissible fade show";
            notification.innerHTML = `
            <strong>Sucesso!</strong> ${
              jsonData.message || "Operação realizada com sucesso!"
            }
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;

            // Adicionar ao topo do container do formulário
            const modalBody = formCadastro.closest(".modal-body");
            modalBody.insertBefore(notification, modalBody.firstChild);

            // Limpar o formulário
            formCadastro.reset();

            // Remover a notificação após 5 segundos
            setTimeout(() => {
              notification.classList.remove("show");
              setTimeout(() => notification.remove(), 300);
            }, 5000);
          } else if (
            text.includes("sucesso") ||
            text.includes("Guia cadastrada com sucesso")
          ) {
            // Fallback: procurar por palavras-chave de sucesso no texto
            const notification = document.createElement("div");
            notification.className =
              "alert alert-success alert-dismissible fade show";
            notification.innerHTML = `
            <strong>Sucesso!</strong> Operação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          `;

            // Adicionar ao topo do container do formulário
            const modalBody = formCadastro.closest(".modal-body");
            modalBody.insertBefore(notification, modalBody.firstChild);

            // Limpar o formulário
            formCadastro.reset();

            // Remover a notificação após 5 segundos
            setTimeout(() => {
              notification.classList.remove("show");
              setTimeout(() => notification.remove(), 300);
            }, 5000);
          } else {
            throw new Error(
              "Não foi possível determinar o status da operação."
            );
          }
        })
        .catch((error) => {
          console.error("Erro:", error);

          // Mostrar erro geral
          const notification = document.createElement("div");
          notification.className =
            "alert alert-danger alert-dismissible fade show";
          notification.innerHTML = `
          <strong>Erro!</strong> ${
            error.message || "Ocorreu um erro ao processar sua requisição."
          }
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

          const modalBody = formCadastro.closest(".modal-body");
          modalBody.insertBefore(notification, modalBody.firstChild);
        })
        .finally(() => {
          // Restaurar botão de envio
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
    });
  }
  // Formulário de atualização - submit via AJAX
  const updateForm = document.getElementById("updateForm");
  if (updateForm) {
    updateForm.addEventListener("submit", function (event) {
      event.preventDefault();

      const formData = new FormData(this);

      // Verificar se checkbox está marcado
      const checkbox = document.getElementById("checkbox_guia");
      if (checkbox) {
        const isChecked = checkbox.checked ? 1 : 0;
        formData.set("checkbox_guia", isChecked);
      }

      // Mostrar indicador de carregamento
      const submitBtn = updateForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...';
      submitBtn.disabled = true;

      fetch("database/atualizar.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          return response.text();
        })
        .then((text) => {
          // Extrair a parte JSON da resposta
          let jsonData = null;
          try {
            // Procurar por padrão JSON na resposta
            const jsonMatch = text.match(/(\{.*\})/);
            if (jsonMatch && jsonMatch[0]) {
              jsonData = JSON.parse(jsonMatch[0]);
              console.log("JSON extraído:", jsonData);
            }
          } catch (e) {
            console.error("Erro ao extrair JSON:", e);
          }

          if (jsonData && jsonData.success) {
            // Criar elemento de notificação de sucesso
            const notification = document.createElement("div");
            notification.className =
              "alert alert-success alert-dismissible fade show";
            notification.innerHTML = `
          <strong>Sucesso!</strong> ${
            jsonData.message || "Atualização realizada com sucesso!"
          }
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            // Adicionar ao topo do container do formulário
            const modalBody = updateForm.closest(".modal-body");
            modalBody.insertBefore(notification, modalBody.firstChild);

            // Opcionalmente limpar campos específicos (manter o ID e outros essenciais)
            const fieldsToReset = [
              "status_guia",
              "correcao_guia",
              "numero_lote",
              "valor_guia",
              "data_remessa",
            ];
            fieldsToReset.forEach((fieldId) => {
              const field = document.getElementById(fieldId);
              if (field) field.value = "";
            });

            // Remover a notificação após 5 segundos
            setTimeout(() => {
              notification.classList.remove("show");
              setTimeout(() => notification.remove(), 300);
            }, 5000);
          } else if (
            text.includes("sucesso") ||
            text.includes("Atualização bem-sucedida")
          ) {
            // Fallback: procurar por palavras-chave de sucesso no texto
            const notification = document.createElement("div");
            notification.className =
              "alert alert-success alert-dismissible fade show";
            notification.innerHTML = `
          <strong>Sucesso!</strong> Atualização realizada com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            // Adicionar ao topo do container do formulário
            const modalBody = updateForm.closest(".modal-body");
            modalBody.insertBefore(notification, modalBody.firstChild);

            // Opcionalmente limpar campos específicos
            const fieldsToReset = [
              "status_guia",
              "correcao_guia",
              "numero_lote",
              "valor_guia",
              "data_remessa",
            ];
            fieldsToReset.forEach((fieldId) => {
              const field = document.getElementById(fieldId);
              if (field) field.value = "";
            });

            // Remover a notificação após 5 segundos
            setTimeout(() => {
              notification.classList.remove("show");
              setTimeout(() => notification.remove(), 300);
            }, 5000);
          } else {
            throw new Error(
              "Não foi possível determinar o status da operação."
            );
          }
        })
        .catch((error) => {
          console.error("Erro:", error);

          // Mostrar erro geral
          const notification = document.createElement("div");
          notification.className =
            "alert alert-danger alert-dismissible fade show";
          notification.innerHTML = `
        <strong>Erro!</strong> ${
          error.message || "Ocorreu um erro ao processar sua requisição."
        }
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;

          const modalBody = updateForm.closest(".modal-body");
          modalBody.insertBefore(notification, modalBody.firstChild);
        })
        .finally(() => {
          // Restaurar botão de envio
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
    });
  }

  // Upload de arquivo Excel em massa
  const excelFileInput = document.getElementById("excelFile");
  if (excelFileInput) {
    excelFileInput.addEventListener("change", function () {
      const fileNameDisplay = document.createElement("div");
      fileNameDisplay.className = "mt-2 text-muted";

      if (this.files.length > 0) {
        fileNameDisplay.innerHTML = `<i class="fas fa-file-excel me-1"></i> ${this.files[0].name}`;
      } else {
        fileNameDisplay.innerHTML = "";
      }

      // Remover exibição anterior se existir
      const oldDisplay = this.parentElement.querySelector(".text-muted");
      if (oldDisplay) oldDisplay.remove();

      this.parentElement.appendChild(fileNameDisplay);
    });
  }

  // GUIAS EM MASSA - Atualizar status em massa
  let guiasSelecionadas = new Set(); // Mantém guias selecionadas

  // Função para buscar guias ao digitar no campo
  $("#buscaGuia").on("keyup", function () {
    let termo = $(this).val();
    console.log("Termo de busca: ", termo);

    $.ajax({
      url: "database/listar_guias.php",
      type: "GET",
      data: { busca: termo },
      success: function (response) {
        console.log("Resposta do servidor: ", response);
        $("#listaGuias").html(response);

        // Restaurar seleções anteriores
        $(".checkbox-guia").each(function () {
          let guiaId = $(this).val();
          if (guiasSelecionadas.has(guiaId)) {
            $(this).prop("checked", true);
          }
        });

        // Adicionar evento para capturar seleções mesmo após novas buscas
        $(".checkbox-guia").on("change", function () {
          let guiaId = $(this).val();
          if ($(this).is(":checked")) {
            guiasSelecionadas.add(guiaId);
          } else {
            guiasSelecionadas.delete(guiaId);
          }
          console.log("Guias selecionadas: ", Array.from(guiasSelecionadas));
        });
      },
      error: function (xhr, status, error) {
        console.error("Erro ao buscar guias:", status, error);
        alert("Erro ao buscar guias.");
      },
    });
  });

  // Atualizar status em massa
  $("#confirmarAtualizacao").on("click", function () {
    let novoStatus = $("#novoStatus").val();

    if (guiasSelecionadas.size === 0) {
      alert("Selecione pelo menos uma guia.");
      return;
    }

    $.ajax({
      url: "database/atualizar_guias.php",
      type: "POST",
      data: {
        guias: Array.from(guiasSelecionadas), // Converter Set para Array
        status: novoStatus,
      },
      success: function (response) {
        alert(response);
        location.reload();
      },
      error: function () {
        alert("Erro ao atualizar guias.");
      },
    });
  });
});
