const switchModal = () => {
    const modal = document.querySelector('.modal')
    const actualStyle = modal.style.display

    if(actualStyle == "block") {
        modal.style.display = 'none'
    } else {
        modal.style.display = 'block';
    }
}

const switchModalAtualizar = () => {
    const modalAtualizar = document.querySelector('.modalAtualizar')
    const actualStyleAtualizar = modalAtualizar.style.display

    if(actualStyleAtualizar == "block") {
        modal.style.display = 'none';
    } else {
        modal.style.display = 'block';
    }
}

const btnCadastrar = document.querySelector('#btn-cadastrar')
btnCadastrar.addEventListener('click', switchModal)

const btnAtualizar = document.querySelector('#btn-atualizar')
btnAtualizar.addEventListener('click', switchModalAtualizar)

window.onclick = function(event) {
    const modal = document.querySelector('.modal')
    if(event.target == modal) {
        switchModal()
    }
}   

window.onclick = function(event) {
    const modalAtualizar = document.querySelector('modalAtualizar')
    if(event.target == modalAtualizar) {
        switchModalAtualizar()
    }
}
