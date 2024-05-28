$(document).ready(function(){
    $('#nome').on('input', function(){
        let nome = $(this).val();
        if(nome.length > 2) { // Busca após 3 caracteres
            $.ajax({
                url: 'cadastrar.php',
                method: 'POST',
                data: {nome: nome},
                success: function(data) {
                    let usuario = JSON.parse(data);
                    if(usuario) {
                        $('#convenio').val(usuario.convenio);
                        $('#entrada').val(usuario.entrada);
                        $('#saida').val(usuario.saida);
                    } else {
                        $('#convenio').val('');
                        $('#entrada').val('');
                        $('#saida').val('');
                    }
                }
            });
        }
    });
});
