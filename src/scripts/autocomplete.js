$(document).ready(function () {
    let dropdown = $('#nome-dropdown');
  
    $('#nome').on('input', function () {
      let nome = $(this).val();
      if (nome.length > 2) { // Busca após 3 caracteres
        $.ajax({
          url: '../src/database/autocomplete.php',
          method: 'POST',
          data: { nome: nome },
          success: function (data) {
            try {
              let usuarios = JSON.parse(data);
              if (usuarios.error) {
                console.error(usuarios.error);
                alert(usuarios.error);
              } else if (Array.isArray(usuarios)) {
                dropdown.empty();
                usuarios.forEach(usuario => {
                  dropdown.append($('<option>').text(usuario.paciente_nome).val(usuario.paciente_nome));
                });
              } else {
                console.log("Nenhum resultado encontrado.");
                dropdown.empty();
              }
            } catch (e) {
              console.error("Erro ao analisar JSON: ", e);
            }
          },
          error: function (xhr, status, error) {
            console.error("Erro na requisição AJAX: ", status, error);
          }
        });
      } else {
        dropdown.empty();
      }
    });
  
    dropdown.on('change', function () {
      let selectedNome = $(this).val();
      $.ajax({
        url: '../src/database/autocomplete.php',
        method: 'POST',
        data: { nome: selectedNome },
        success: function (data) {
          try {
            let usuario = JSON.parse(data);
            if (usuario.error) {
              console.error(usuario.error);
              alert(usuario.error);
            } else if (usuario) {
              $('#convenio').val(usuario.paciente_convenio || '');
              $('#entrada').val(usuario.paciente_entrada || '');
              $('#saida').val(usuario.paciente_saida || '');
            }
          } catch (e) {
            console.error("Erro ao analisar JSON: ", e);
          }
        },
        error: function (xhr, status, error) {
          console.error("Erro na requisição AJAX: ", status, error);
        }
      });
    });
  });
  