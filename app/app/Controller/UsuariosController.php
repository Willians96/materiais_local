<?php

class UsuariosController
{

    public function index()
    {
        if (!($_SESSION['cod_perfil'] <= 4 )) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=home'); exit;
        }


        try {
            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('usuarios/home.html');


            $parametros = array();

            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);


            //verificar se existem parametros de consulta pm
            if (isset($_SESSION['parametros'])) {

                $parametros['retornoConsulta'] = true;
                $parametros['foto'] = $_SESSION['parametros']['foto'];
                $parametros['ptgr'] = $_SESSION['parametros']['ptgr'];
                $parametros['re'] = $_SESSION['parametros']['re'];
                $parametros['dgre'] = $_SESSION['parametros']['dgre'];
                $parametros['nome'] = $_SESSION['parametros']['nome'];
                $parametros['guerra'] = $_SESSION['parametros']['guerra'];
                $parametros['email'] = $_SESSION['parametros']['email'];
                $parametros['opm'] = $_SESSION['parametros']['opm'];
                $parametros['funcao'] = $_SESSION['parametros']['funcao'];
                $parametros['codopm'] = $_SESSION['parametros']['codopm'];
                $parametros['cpf'] = $_SESSION['parametros']['cpf'];
                $parametros['dn'] = $_SESSION['parametros']['dn'];
                $parametros['sexo'] = $_SESSION['parametros']['sexo'];

                unset($_SESSION['parametros']);

            }

            $parametros['perfis'] = Usuario::perfis();
         
            $parametros['usuarios'] = Usuario::listarUsuarios();
           

            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    public function incluirUsuario()
    {
        $busca = Usuario::buscaUsuario($_POST['re']);
        if ($busca) {
            $_SESSION['msg'] = "alert";
            $_SESSION['msgText'] = "Policial já possui cadastro.";
            header('location: ?pagina=usuarios');
            exit;
        }

        $resultado = Usuario::incluirUsuario($_POST);

        if ($resultado)
        {
            $_SESSION['msg'] = "sucess";
            $_SESSION['msgText'] = "Policial cadastrado.";
        } else {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao cadastrar.";
        }

        header('location: ?pagina=usuarios');    

    }


    public function alteraUsuario()
    {

    }


    public function excluirUsuario()
    {
        $id_usuario = $_POST['id_usuario'];

        $resultado = Usuario::excluirUsuario($id_usuario);

        if ($resultado)
        {
            $_SESSION['msg'] = "sucess";
            $_SESSION['msgText'] = "Usuário removido.";
        } else {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao remover.";
        }

        header('location: ?pagina=usuarios');    
        

    }


    public function listaUsuarios()
    {

    }


    public function consultaUsuario()
    {

    }


    public function consultaPM()
    {
        $re = $_POST['re'];

        $policial = PolicialController::buscaPM($re);

        if ($policial) {
            $parametros = [
                'foto' => $policial->fotoBase,
                're' => $policial->re,
                'nome' => $policial->nome,
                'guerra' => $policial->guerra,
                'email' => $policial->email,
                'opm' => $policial->opm,
                'funcao' => $policial->funcao,
                'ptgr' => $policial->ptgr,
                'dgre' => $policial->dgre,
                'codopm' => $policial->codopm,
                'cpf' => $policial->cpf,
                'sexo' => $policial->sexo,
                'dn' => substr($policial->dataNascimento, 0,10) 
            ];
            $_SESSION['parametros'] = $parametros;
        } else {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Policial não localizado.";
        }

        header('location: ?pagina=usuarios');
    }

}