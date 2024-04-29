const modalCadastrar = document.querySelector(".modal");
const modalAtualizar = document.querySelector(".modal-atualizar");
const modalRelatorio = document.querySelector(".modalRelatorio");

const switchModalCadastrar = () => {
  modalCadastrar.style.display =
    modalCadastrar.style.display === "block" ? "none" : "block";
};

const switchModalAtualizar = () => {
  modalAtualizar.style.display =
    modalAtualizar.style.display === "block" ? "none" : "block";
};

const switchModalRelatorio = () => {
  modalRelatorio.style.display =
    modalRelatorio.style.display === "block" ? "none" : "block";
};

const btnCadastrar = document.querySelector("#btn-cadastrar");
btnCadastrar.addEventListener("click", switchModalCadastrar);

const btnAtualizar = document.querySelector("#btn-atualizar");
btnAtualizar.addEventListener("click", switchModalAtualizar);

const btnRelatorio = document.querySelector('#relatorioButton');
btnRelatorio.addEventListener("click", switchModalRelatorio);

window.addEventListener("click", (event) => {
  if (event.target === modalCadastrar) {
    switchModalCadastrar();
  }

  if (event.target === modalAtualizar) {
    switchModalAtualizar();
  }

  if (event.target === modalRelatorio) {
    switchModalRelatorio();
  }
});
