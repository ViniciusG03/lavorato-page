const switchModal = () => {
    const modal = document.querySelector('.modal')
    const modalAtualizar = document.querySelector('.modalAtualizar')
    const actualStyle = modal.style.display

    if(actualStyle == "block") {
        modalAtualizar.style.display = 'block'
        modal.style.display = 'none'
    } else {
        modalAtualizar.style.display = 'none';
        modal.style.display = 'block';
    }
}

const btnCadastrar = document.querySelector('#btn-cadastrar')
btnCadastrar.addEventListener('click', switchModal)

const btnAtualizar = document.querySelector('#btn-atualizar')
btnAtualizar.addEventListener('click', switchModal)

window.onclick = function(event) {
    const modal = document.querySelector('.modal')
    const modalAtualizar = document.querySelector('.modalAtualizar')
    if(event.target == modal) { switchModal() }

    if (event.target == modalAtualizar) { switchModal() }
}   

