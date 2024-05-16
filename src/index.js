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
});
