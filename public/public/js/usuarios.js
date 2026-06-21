function excluirUsuario(usuario){
    let id = usuario.getAttribute('data-id');
    let ptgr = usuario.getAttribute('data-ptgr');
    let re = usuario.getAttribute('data-re');
    let nome = usuario.getAttribute('data-nome');
    let perfil = usuario.getAttribute('data-perfil');


    let html = `
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="?pagina=usuarios&metodo=excluirUsuario" method="post">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir usuário</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Confirma a remoção do policial:</p>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <td>Posto/Grad.</td>
                                    <td>RE</td>
                                    <td>Nome</td>
                                    <td>Perfil</td>
                                </tr>
                                <tr>
                                    <td id="excluir_ptgr"></td>
                                    <td id="excluir_re"></td>
                                    <td id="excluir_nome"></td>
                                    <td id="excluir_perfil"></td>
                                </tr>
                            </table>
                            <input type="hidden" name="id_usuario" id="excluir_id_usuario">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

    // Insere o modal na página
    document.getElementById('modal-container').innerHTML = html;

    // Preenche os dados
    document.getElementById('excluir_ptgr').innerText = ptgr;
    document.getElementById('excluir_re').innerText = re;
    document.getElementById('excluir_nome').innerText = nome;
    document.getElementById('excluir_perfil').innerText = perfil;
    document.getElementById('excluir_id_usuario').value = id;

    // Mostra o modal
    const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
    modal.show();
}


function editarUsuario(usuario) {

    // Pegando os atributos do botão
    let id = usuario.getAttribute('data-id');
    let ptgr = usuario.getAttribute('data-ptgr');
    let re = usuario.getAttribute('data-re');
    let nome = usuario.getAttribute('data-nome');
    let codperfil = usuario.getAttribute('data-codperfil');
    let status = usuario.getAttribute('data-status');



    let html = `
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditarUsuarioLabel">Editar Usuário</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>Posto/Grad.</td>
                                <td>RE</td>
                                <td>Nome</td>
                            </tr>
                            <tr>
                                <td id="editar_ptgr"></td>
                                <td id="editar_re"></td>
                                <td id="editar_nome"></td>
                            </tr>
                        </table>
                        <input type="hidden" name="id_usuario" id="editar_id_usuario">

                        <div class="row">
                            <div class="col-md-3">
                                <label for="" class="form-label">Perfil</label>
                                <select name="cod_perfil" id="editar_perfil" class="form-select">
                                    <option value="2">Adm</option>
                                    <option value="3">Div Op</option>
                                    <option value="4">GT</option>
                                    <option value="5">P3 BTL</option>
                                    <option value="6">P3 CIA</option>
                                    <option value="99">Convidado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" id="editar_status" class="form-select">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </div>
        </div>`;

        
    // Insere o modal na página
    document.getElementById('modal-container').innerHTML = html;



    // Preenchendo os campos do modal (que já estão no HTML)
    document.getElementById('editar_ptgr').innerText = ptgr;
    document.getElementById('editar_re').innerText = re;
    document.getElementById('editar_nome').innerText = nome;
    document.getElementById('editar_id_usuario').value = id;
   // document.getElementById('editar_perfil').value = codperfil;
    document.getElementById('editar_status').value = status;


    // Exibindo o modal
    let modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    modal.show();
}
