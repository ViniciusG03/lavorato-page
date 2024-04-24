const modalCadastrar = document.querySelector('.modal');
const modalAtualizar = document.querySelector('.modal-atualizar');

const switchModalCadastrar = () => {
    modalCadastrar.style.display = modalCadastrar.style.display === 'block' ? 'none' : 'block';
};

const switchModalAtualizar = () => {
    modalAtualizar.style.display = modalAtualizar.style.display === 'block' ? 'none' : 'block';
};

const btnCadastrar = document.querySelector('#btn-cadastrar');
btnCadastrar.addEventListener('click', switchModalCadastrar);

const btnAtualizar = document.querySelector('#btn-atualizar');
btnAtualizar.addEventListener('click', switchModalAtualizar);

window.addEventListener('click', (event) => {
    if (event.target === modalCadastrar) {
        switchModalCadastrar();
    }

    if (event.target === modalAtualizar) {
        switchModalAtualizar();
    }
});
