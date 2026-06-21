<?php

class ConsultasController
{

    public function index()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('consultas/home.html');

            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);
    
            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

    }

    public function porRE()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('consultas/re.html');

            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);


            if (isset($_POST['re']) and $_POST['re']!='') {
                $re = $_POST['re'];
                
                $parametros['policial'] = Discente::listarPorRE($re);

                $conteudo = $template->render($parametros);    
                echo $conteudo;
            } else {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'RE inválido.';

                header('location: ?pagina=Consultas'); exit;
            }
    


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

}