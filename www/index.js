if (typeof jQuery == 'undefined') {
  console.error('jQuery não foi carregado.');
} else {
  console.log('jQuery foi carregado.');
}

document.addEventListener("DOMContentLoaded", () => {
  const btnCadastrar = document.getElementById("btn-cadastrar");
  const modalCadastro = new bootstrap.Modal(
    document.getElementById("modalCadastro")
  );
  btnCadastrar.addEventListener("click", () => {
    modalCadastro.show();
  });

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
  const modalRemover = new bootstrap.Modal(
    document.getElementById("modalRemover")
  );
  btnRemover.addEventListener("click", () => {
    modalRemover.show();
  });

  const btnAtualizarEmMassa = document.getElementById("btn-atualizarEmMassa");
  const modalAtualizarEmMassa = new bootstrap.Modal(
    document.getElementById("modalAtualizar")
  );
  btnAtualizarEmMassa.addEventListener("click", () => {
    modalAtualizarEmMassa.show();
  });
});

$(document).ready(function () {
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
