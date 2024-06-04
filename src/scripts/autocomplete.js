$(document).ready(function () {
  $("#nome").on("input", function () {
    let nome = $(this).val();
    if (nome.length > 2) {
      $.ajax({
<<<<<<< Updated upstream
        url: "../src/database/autocomplete.php",
=======
        url: "../database/autocomplete.php",
>>>>>>> Stashed changes
        method: "POST",
        data: { nome: nome },
        success: function (data) {
          try {
            let usuarios = JSON.parse(data);
            if (usuarios.error) {
              console.error(usuarios.error);
              alert(usuarios.error);
            } else if (Array.isArray(usuarios)) {
              let suggestions = $("#nome-suggestions");
              suggestions.empty().show();
              usuarios.forEach((usuario) => {
                suggestions.append(
                  $('<a class="dropdown-item">')
                    .text(usuario.paciente_nome)
                    .data("usuario", usuario)
                );
              });
            } else {
              $("#nome-suggestions").empty().hide();
            }
          } catch (e) {
            console.error("Erro ao analisar JSON: ", data);
          }
        },
        error: function (xhr, status, error) {
          console.error("Erro na requisição AJAX: ", status, error);
        },
      });
    } else {
      $("#nome-suggestions").empty().hide();
    }
  });

  $(document).on("click", "#nome-suggestions a", function () {
    let usuario = $(this).data("usuario");
    $("#nome").val(usuario.paciente_nome);
    $("#convenio").val(usuario.paciente_convenio || "");
    $("#entrada").val(usuario.paciente_entrada || "");
    $("#saida").val(usuario.paciente_saida || "");
    $("#nome-suggestions").empty().hide();
  });

  $(document).on("click", function (e) {
    if (
      !$(e.target).closest("#nome-suggestions").length &&
      !$(e.target).is("#nome")
    ) {
      $("#nome-suggestions").empty().hide();
    }
  });
});
