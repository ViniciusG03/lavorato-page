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
  )
  btnRemover.addEventListener("click", () => {
    modalRemover.show();
  });
});

document.getElementById('updateForm').addEventListener('submit', function(event) {
  event.preventDefault(); 
  var checkbox = document.getElementById('checkbox_guia');
  var isChecked = checkbox.checked ? 1 : 0;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'atualizar_junho.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('checkbox_guia=' + isChecked);

  xhr.onload = function() {
    if (this.status == 200) {
      console.log(this.responseText);
    }
  };
});