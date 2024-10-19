document.addEventListener("DOMContentLoaded", () => {
  const btnCadastrar = document.getElementById("btn-cadastrar");
  const modalCadastro = new bootstrap.Modal(
    document.getElementById("modalCadastrarAta")
  );
  btnCadastrar.addEventListener("click", () => {
    modalCadastro.show();
  });
});
