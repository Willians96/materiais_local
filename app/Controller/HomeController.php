<?php

    class HomeController
    {
        public function index()
        {
            try {
                $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
                $twig = new \Twig\Environment($loader);
                $template = $twig->load('home.html');

                $parametros = [
                    'msg' => $_SESSION['msg'] ?? null,
                    'msgText' => $_SESSION['msgText'] ?? null
                ];
                unset($_SESSION['msg'], $_SESSION['msgText']);

                $parametros['cod_perfil']    = $_SESSION['cod_perfil'];
                $parametros['perfil']        = $_SESSION['perfil'];

                $parametros['config'] = Config::configuracoes();
                $id_eap_ativo = $parametros['config']['eap_id_eap'];

                $parametros['eap']            = Ead::eapAtivo($id_eap_ativo);
                $parametros['meu_eap']        = Discente::meuEap($id_eap_ativo);
                $parametros['turmas']         = Turma::listarTodas();
                $parametros['concluiram_eap'] = Discente::concluiramEAP($id_eap_ativo);
                $parametros['conceito_mb']    = Discente::conceitoMBTAF($id_eap_ativo);
                $parametros['reprovados_taf'] = Discente::reprovadosTAF($id_eap_ativo); 
                $parametros['lesionados_taf'] = Discente::lesionadosTAF($id_eap_ativo);
                $parametros['turmas_eap']     = Turma::turmasEapConcluidas($id_eap_ativo);
                $parametros['usuarios']       = Usuario::usuariosSistema();                
                $parametros['turma_atual']    = Turma::turmaAtual();
                $parametros['turma_anterior'] = Turma::turmaAnterior();
                $parametros['turma_proxima']  = Turma::turmaProxima();

                $conteudo = $template->render($parametros);

                echo $conteudo;

            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }