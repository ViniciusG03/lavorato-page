document.addEventListener("DOMContentLoaded", function () {
    const formContainer = document.getElementById("formContainer");

    document.querySelectorAll(".ficha-option").forEach((button) => {
        button.addEventListener("click", function () {
            const option = this.getAttribute("data-option");

            // Fecha o modal
            const modalElement = document.getElementById("fichasModal");
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();

            // Remover backdrop do modal após fechamento
            modalElement.addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            });

            // Realiza a requisição AJAX para carregar o formulário correspondente ou redirecionar
            fetch(`../database/back_relatorio.php?option=${option}`, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); 
            })
            .then((text) => {
                try {
                    // Tenta converter a resposta em JSON
                    const data = JSON.parse(text);
                    
                    // Se houver um campo de redirecionamento, realiza o redirecionamento
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Caso contrário, trata como HTML e exibe no formContainer
                        formContainer.innerHTML = text;
                    }
                } catch (error) {
                    // Se o texto não for JSON, trata como HTML diretamente
                    formContainer.innerHTML = text;
                }
            })
            .catch((error) => console.error("Erro ao carregar o formulário:", error));
        });
    });
});
