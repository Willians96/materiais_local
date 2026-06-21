<?php


class TATController
{

    public function index()
    {

        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('tat/tat.html');

            $_SESSION['msg'] = 'alert';
            $_SESSION['msgText'] = 'pagina ainda não criada';



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

    public function incluirTAT()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('tat/tat-inclusao.html');

            $id_turma = $_GET['id'];

            //verificar permissão do perfil
            if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99 ) ) {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Você não possui permissão acessar esta página.';
        
                header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
            }

            $parametros = array();

            $parametros['turma']     = Turma::buscaTurma($id_turma)[0];
            $parametros['discentes'] = Discente::listarDiscentes($id_turma);

            $parametros[] = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);


            //Breadcrumb
            $parametros['breadcrumb'] = [
                                            ['label' => 'Início', 'url' => '?pagina=home'],
                                            ['label' => 'Turmas', 'url' => '?pagina=eap&metodo=turmas'],
                                            ['label' => $parametros['turma']['turma'], 'url' => '?pagina=eap&metodo=turma&id=' . $id_turma ],
                                            ['label' => 'TAT ', 'url' => '']
                                        ];

    
            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

    }

    public function gravarTAT()
    {
        $id_turma      = $_POST['id_turma'];
        $data_tat      = $_POST['data_tat'];

        $id_discentes  = $_POST['id_discente'] ?? [];
        $pontuacao_tat = $_POST['pontuacao_tat'] ?? [];
        $conceito_tat  = $_POST['conceito_tat'] ?? [];
        $nota          = $_POST['nota'] ?? [];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4 ) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=TAT&metodo=incluirTAT&id='.$id_turma); exit;
        }
        
        $discentes = array();

        foreach($id_discentes as $re => $id)
        {
           $discentes[] = [
                'id_discente'   => $id_discentes[$re],
                'pontuacao_tat' => $pontuacao_tat[$re],
                'conceito_tat'  => $conceito_tat[$re],
                'nota'          => $nota[$re]
           ];

        }

        if (Discente::gravarTAT($data_tat, $discentes))
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'TAT incluído com sucesso.';
        } else {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Erro ao incluir o TAT.';
        }




        header('location: ?pagina=eap&metodo=turma&id='.$id_turma);
    }


    
}