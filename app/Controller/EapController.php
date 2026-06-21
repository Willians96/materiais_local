<?php


class EapController
{

    public function index()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader('app/View/eap');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('home.html');


            $parametros = array();


            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    public function turmas()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('eap/turmas.html');


            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);

            //verificar permissão do perfil
            /**
             * SA => 1
             * Adm => 2
             * DivOp => 3
             * GT => 4 
             * Visitante => 99
             */
            if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99)) {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
        
                header('location: ?pagina=home');
            }

            if (isset($_POST['busca']) && $_POST['busca'] != '') {
                $parametros['turmas'] = Turma::listarTurmaBusca($_POST['busca']);
            } else {
                $parametros['turmas'] = Turma::listarTodas();
            }

    
            //Breadcrumb
            $parametros['breadcrumb'] = [
                                            ['label' => 'Início', 'url' => '?pagina=home'],
                                            ['label' => 'Turmas ', 'url' => '']
                                        ];

            $conteudo = $template->render($parametros);


            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    public function removerTurma()
    {
        //verificar permissão do perfil
        /**
         * SA => 1
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4 )) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=eap&metodo=turmas');
        }


        $id = $_POST['id'];

        // Instancia o model
        $model = new Turma();
            
        // Chama a função de inclusão com os dados do formulário
        $resultado = $model->removerTurma($id);

        // Verifica se deu certo
        if ($resultado) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Turma removida com sucesso.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao remover turma.';
        }

        header('location: ?pagina=eap&metodo=turmas');

    }


    public function editarTurma()
    {
            //verificar permissão do perfil
            /**
             * SA => 1
             * Adm => 2
             * DivOp => 3
             * GT => 4 
             * Visitante => 99
             */
            if (!($_SESSION['cod_perfil'] <= 4 )) {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
        
                header('location: ?pagina=eap&metodo=turmas');
            }
       
        $model = new Turma();

        $resultado = $model->editarTurma($_POST);


        if ($resultado) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Turma editada com sucesso.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao editar a turma.';
        }

        if(isset($_POST['pagina'])){
            header('location: ' . $_POST['pagina'] );
            exit;
        }

        header('location: ?pagina=eap&metodo=turmas');
        exit;

    }

    public function incluirTurma()
    {
        //verificar permissão do perfil
        /**
         * SA => 1
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4 )) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
             header('Location: ?pagina=eap&metodo=turmas');
        }

        // Instancia o model
        $model = new Turma();
    
        // Chama a função de inclusão com os dados do formulário
        $resultado = $model->incluirTurma($_POST);
    
        // Verifica se deu certo
        if ($resultado) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Turma incluída com sucesso.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao incluir turma.';
        }
    
        // Redireciona para a listagem de turmas
        header('Location: ?pagina=eap&metodo=turmas');
        exit;
    }
    

    public function turma()
    {  
        //verificar permissão do perfil
        /**
         * SA => 1
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=home');
        }

        $id = $_GET['id'];

        
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('eap/visualizar-turma.html');


        $parametros = [
            'msg' => $_SESSION['msg'] ?? null,
            'msgText' => $_SESSION['msgText'] ?? null
        ];
        unset($_SESSION['msg'], $_SESSION['msgText']);

        $turma = Turma::buscaTurma($id)[0];

        $discentes = Discente::listarDiscentes($id);

        $inicioTurma = new DateTime($turma['inicio']);
        $inicioLimite = (clone $inicioTurma)->modify('-1 year');

        foreach ( $discentes as &$discente) {
            // Se 'ias' não está vazia e é uma data válida
            if (!empty($discente['ias']) && strtotime($discente['ias'])) {
                $dataIas = new DateTime($discente['ias']);
                $discente['ias_em_dia'] = ($dataIas >= $inicioLimite);
            } else {
                $discente['ias_em_dia'] = false; // IAS ausente ou inválida
            }
        }
        $parametros['discentes'] = $discentes;

        //verificar se existem parametros de consulta pm
        if (isset($_SESSION['parametros'])) {

            $parametros['retornoConsulta'] = true;
            $parametros['foto']        = $_SESSION['parametros']['foto'];
            $parametros['ptgr']        = $_SESSION['parametros']['ptgr'];
            $parametros['re']          = $_SESSION['parametros']['re'];
            $parametros['dgre']        = $_SESSION['parametros']['dgre'];
            $parametros['nome']        = $_SESSION['parametros']['nome'];
            $parametros['guerra']      = $_SESSION['parametros']['guerra'];
            $parametros['email']       = $_SESSION['parametros']['email'];
            $parametros['opm']         = $_SESSION['parametros']['opm'];
            $parametros['funcao']      = $_SESSION['parametros']['funcao'];
            $parametros['codopm']      = $_SESSION['parametros']['codopm'];
            $parametros['cpf']         = $_SESSION['parametros']['cpf'];
            $parametros['dn']          = $_SESSION['parametros']['dn'];
            $parametros['sexo']        = $_SESSION['parametros']['sexo'];
            $parametros['nota']        = $_SESSION['parametros']['nota'];
            $parametros['inicio_ead']  = $_SESSION['parametros']['inicio_ead'];
            $parametros['termino_ead'] = $_SESSION['parametros']['termino_ead'];

            unset($_SESSION['parametros']);

         }

        

        $parametros['turma'] = $turma['turma'];
        $parametros['id_turma'] = $id;
        $parametros['tipo'] = $turma['tipo'];
        $inicio = new DateTime($turma['inicio']);
        $termino = new DateTime($turma['termino']);
        $parametros['inicio'] = $turma['inicio'];
        $parametros['termino'] = $turma['termino'];   
        $parametros['periodo'] = $inicio->format('d/m/Y') . ' a ' . $termino->format('d/m/Y');
        $parametros['total_discentes'] = $turma['total_discentes'];
        $parametros['habilita_avaliacao'] = $turma['habilita_avaliacao'];        
        $parametros['data_tat']    = $turma['data_tat'];
        $parametros['data_taf']    = $turma['data_taf'];
        $parametros['num_bi']      = $turma['num_bi'];
        $parametros['ano_bi']      = $turma['ano_bi'];



        //Breadcrumb
        $parametros['breadcrumb'] = [
            ['label' => 'Início', 'url' => '?pagina=home'],
            ['label' => 'Turmas', 'url' => '?pagina=eap&metodo=turmas'],
            ['label' => 'Turma ' . $parametros['turma'], 'url' => '']
        ];


        $conteudo = $template->render($parametros);

        echo $conteudo;
        
    }


    public function buscaPMInsercao()
    {
        $re     = $_POST['busca-re'];
        $turma  = $_POST['id_turma'];
        $inicio = $_POST['inicio'];
        $tipo   = $_POST['tipo'];

        $policial = PolicialController::buscaPM($re);
        //verificar permissão do perfil
        /**
         * SA => 1
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$turma);
        }
        

        if ($policial) {

            //verificar PM de acordo com Tipo Turma
            if ( $policial->ptgr == 'CB PM' or $policial->ptgr == 'SD PM - 1C' or $policial->ptgr == 'SD PM - 2C' ) {
                $tipo_policial = 'Cb/Sd PM';
            } else if ( $policial->ptgr == 'SUBTEN PM' or $policial->ptgr == '1. SGT PM' or $policial->ptgr == '2. SGT PM' or $policial->ptgr == '3. SGT PM' ) {
                $tipo_policial = 'Subten/Sgt PM';
            } else if ( $policial->ptgr == 'CAP PM' or $policial->ptgr == '1. TEN PM' or $policial->ptgr == '2. TEN PM' )  {
                $tipo_policial = 'Cap/Ten PM';
            } else {
                $tipo_policial = 'Oficiais PM';
            }
            if ( $tipo_policial != $tipo ) {
                $_SESSION['msg'] = "error";
                $_SESSION['msgText'] = "Policial de Posto/Graduação diferente da turma.";
                header('location: ?pagina=eap&metodo=turma&id='.$turma);
                exit;
            }

            //verificar se PM ja está na turma
            if ( Discente::verificaInscritoTurma($turma, $re) ) {
                $_SESSION['msg'] = "error";
                $_SESSION['msgText'] = "Policial já está inscrito nesta turma.";
                header('location: ?pagina=eap&metodo=turma&id='.$turma);
                exit;
            }
            

            

            //buscaNotaEaD
            $nota = Ead::buscaNotaEad($re, $inicio);

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
                'dn' => substr($policial->dataNascimento, 0,10),
                'nota' => $nota['nota'] ?? null,
                'inicio_ead' => $nota['inicio'] ?? null,
                'termino_ead' => $nota['termino'] ?? null
            ];

             $_SESSION['parametros'] = $parametros;

        } else {
            
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Policial não localizado.";
          
            
        }


        header('location: ?pagina=eap&metodo=turma&id='.$turma);

 

    }

    public function incluirPMTurma()
    {
        $id_turma = $_POST['id_turma'];

        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
        }    


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $resultado = Discente::inserirPM($_POST);
    
            if ($resultado) {
                $_SESSION['msg'] = 'success';
                $_SESSION['msgText'] = 'PM incluído na turma com sucesso.';
            } else {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Erro ao incluir PM na turma.';
            }
    
            header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
            exit;
        }

        header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
    }


    public function removerPMTurma()
    {

        $id_turma = $_POST['id_turma'];
        $id_discente = $_POST['id_discente'];

        //verificar permissão do perfil
        /**
         * SA => 1
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
    
            header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
        }


        $resultado = Discente::removerPMTurma($id_discente);
        
        
        if ($resultado) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Policial excluído da turma.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao excluir PM da turma.';
        }


        header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
    }

    public function alterarIAS()
    {
        $id= $_POST['id_turma'];

        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='. $id);
        }    

        $resultado = Ead::alterarIAS($_POST);


        if ($resultado)
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Data da IAS alterada.';            
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao alterar a data da IAS.';
        }

        header('location: ?pagina=eap&metodo=turma&id='. $id);
    }


    public function inserirNotaEaD()
    {
        $id_turma    = $_POST['id_turma'];

        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
        }

        
        
        $resultado = Discente::inserirNotaEad($_POST);
        
        if ($resultado) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Nota EaD incluída com sucesso.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao incluir a nota.';
        }

        header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
        exit;

    }


    public function gerarXML()
    {
        $id_turma = $_GET['id'];

        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99 )) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='. $id_turma);
        }

        $caminho = Turma::gerarXML($id_turma);

        if (!file_exists($caminho)) {
            echo "Arquivo XML não encontrado.";
            return;
        }
    
        // Força o download do XML
        header('Content-Description: File Transfer');
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . basename($caminho) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminho));
        readfile($caminho);
        exit;

    }



    public function notaEad()
    {
        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4) || $_SESSION['cod_perfil'] == 99) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar essa página.';
    
            header('location: ?pagina=home'); exit;
        }


        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('ead/nota-ead.html');

        $parametros = array();

        $parametros = [
            'msg' => $_SESSION['msg'] ?? null,
            'msgText' => $_SESSION['msgText'] ?? null
        ];
        unset($_SESSION['msg'], $_SESSION['msgText']);

        if(isset($_SESSION['notas'])) {
            $parametros['notas'] = $_SESSION['notas'];
            unset($_SESSION['notas']);
        }

        //Breadcrumb
        $parametros['breadcrumb'] = [
                                        ['label' => 'Início', 'url' => '?pagina=home'],
                                        ['label' => 'EaD ', 'url' => '']
                                    ];

        $conteudo = $template->render($parametros);

        echo $conteudo;
    }


    public function uploadNotaEad()
    {
        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=notaEad');
        }


        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
            $arquivoTmp = $_FILES['arquivo']['tmp_name'];
        
            // Abre o arquivo para leitura
            if (($handle = fopen($arquivoTmp, "r")) !== false) {
                $linha = 0;

        
                while (($dados = fgetcsv($handle, 1000, ",")) !== false) {
                    $linha++;
        
                    if ($linha == 1) {
                        // Pula o cabeçalho
                        continue;
                    }

                    // Verificar se o RE não está sozinho
                    if (!empty($dados[2]) && strlen($dados[2]) === 6 && ctype_digit($dados[2])) {
                        $resultado[] = [
                            "sobrenome" => trim($dados[0]),
                            "nome"      => trim($dados[1]),
                            "re"        => trim($dados[2]),
                            "email"     => trim($dados[3]),
                            "unidade"   => trim($dados[4]),
                            "ptgr"      => trim($dados[5]),
                            "status"    => trim($dados[6]),
                            "inicio"    => self::converterData2(trim($dados[7])),
                            "termino"   => self::converterData2(trim($dados[8])),
                            "tempo"     => trim($dados[9]),
                            "nota"      => trim($dados[10]),
                        ];
                         
                    } else {
                        continue;
                    }     

                    

                }

        
                fclose($handle);
            } else {
                echo "Erro ao abrir o arquivo.";
            }

            $resultado = Ead::gravarNotaEad($resultado);

            if ($resultado) {
                
                //$_SESSION['msg'] = 'success';
                //$_SESSION['msgText'] = 'Notas gravadas com sucesso.' . $insercoes;
                
            } else {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Erro ao gravar as notas.';
            }

            header('location: ?pagina=eap&metodo=notaEad');
            exit;
            
        } else {
            echo "Erro no upload do arquivo.";
        }
        
    }

    
    function processaUploadJSON() {
        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=notaEad');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
            $arquivoTmp = $_FILES['arquivo']['tmp_name'];
        
            // Verifica se é JSON
            $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
            if (strtolower($extensao) !== 'json') {
                die('Arquivo inválido. Envie um JSON.');
            }
        
            // Lê o conteúdo
            $conteudo = file_get_contents($arquivoTmp);
            $dadosJson = json_decode($conteudo, true); // true para transformar em array associativo
        
            if (json_last_error() !== JSON_ERROR_NONE) {
                die('Erro ao decodificar JSON: ' . json_last_error_msg());
            }
        
            // A estrutura está dentro de um array aninhado ([[{...}, {...}]])
            if (isset($dadosJson[0]) && is_array($dadosJson[0])) {
                foreach ($dadosJson[0] as $linha) {
                    // Aqui você pode tratar ou inserir os dados no banco
                    $sobrenome = htmlspecialchars($linha['sobrenome'] ?? '');
                    $nome = htmlspecialchars($linha['nome'] ?? '');
                    $re = htmlspecialchars($linha['re'] ?? '');
                    $email = htmlspecialchars($linha['endereodeemail'] ?? '');
                    $unidade = htmlspecialchars($linha['unidade'] ?? '');
                    $posto = htmlspecialchars($linha['postograduao'] ?? '');
                    $status = htmlspecialchars($linha['estado'] ?? '');
                    $inicio = htmlspecialchars($linha['iniciadoem'] ?? '');
                    $termino = htmlspecialchars($linha['completo'] ?? '');
                    $tempo = htmlspecialchars($linha['tempoutilizado'] ?? '');
                    $nota = htmlspecialchars($linha['avaliar400'] ?? '');

                     // Verificar se o RE não está sozinho
                    if (!empty($re) && strlen($re) === 6 && ctype_digit($re)) {
                        $resultado[] = [
                            "sobrenome"      => $sobrenome,
                            "nome"           => $nome,
                            "re"             => $re,
                            "email"          => $email,
                            "unidade"        => $unidade,
                            "ptgr"           => $posto,
                            "status"         => $status,
                            "inicio"         => self::converterData2(trim($inicio)),
                            "termino"        => self::converterData2(trim($termino)),
                            "tempo"          => $tempo,
                            "nota"           => $nota,
                        ];
                    }
                    

                }

                $resultado = Ead::gravarNotaEad($resultado);

                if ($resultado) {
                    //$_SESSION['msg'] = 'success';
                    //$_SESSION['msgText'] = 'Notas gravadas com sucesso.';
                } else {
                    $_SESSION['msg'] = 'error';
                    $_SESSION['msgText'] = 'Erro ao gravar as notas.';
                }
        
                header('location: ?pagina=eap&metodo=notaEad');
                exit;


            } else {
                echo "Formato de JSON inesperado.";
            }
        } else {
            echo "Nenhum arquivo enviado.";
        }
    }


    public function processaPlanilhaColada()
    {
        //verificar permissão do perfil
        /**
         * SA => 1 
         * Adm => 2
         * DivOp => 3
         * GT => 4 
         * Visitante => 99
         */
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=notaEad');
        }


        if (!isset($_POST['planilha']) || empty(trim($_POST['planilha']))) {
            echo "Nenhum dado colado.";
            return;
        }
    
        $texto = trim($_POST['planilha']);
        $linhas = explode("\n", $texto);
        $resultado = [];
    
        foreach ($linhas as $linha) {
            $linha = trim($linha);
    
            // Quebra por tabulação (copiar do Excel geralmente usa tab)
            $dados = explode("\t", $linha);
    
            // Verifica se tem pelo menos 11 colunas e se o campo RE é numérico
            if (count($dados) < 11 || !is_numeric(trim($dados[2]))) {
                continue; // pula se não tiver todas as colunas
            }

            // Verificar se o RE não está sozinho
            if (!empty($dados[2]) && strlen($dados[2]) === 6 && ctype_digit($dados[2])) {
    
                $resultado[] = [
                    "sobrenome"      => trim($dados[0]),
                    "nome"           => trim($dados[1]),
                    "re"             => trim($dados[2]),
                    "email"          => trim($dados[3]),
                    "unidade"        => trim($dados[4]),
                    "ptgr"           => trim($dados[5]),
                    "status"         => trim($dados[6]),
                    "inicio"         => self::converterData2(trim($dados[7])),
                    "termino"        => self::converterData2(trim($dados[8])),
                    "tempo"          => trim($dados[9]),
                    "nota"           => trim($dados[10]),
                ];

            }
        }

        $resultado = Ead::gravarNotaEad($resultado);

        if ($resultado) {
            //$_SESSION['msg'] = 'success';
            //$_SESSION['msgText'] = 'Notas gravadas com sucesso.';
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Erro ao gravar as notas.';
        }

        header('location: ?pagina=eap&metodo=notaEad');
        exit;
    }



    public function converterData($texto) {
        $meses = [
            'janeiro' => '01',
            'fevereiro' => '02',
            'março' => '03',
            'abril' => '04',
            'maio' => '05',
            'junho' => '06',
            'julho' => '07',
            'agosto' => '08',
            'setembro' => '09',
            'outubro' => '10',
            'novembro' => '11',
            'dezembro' => '12'
        ];
    
        // Exemplo: "17 fevereiro 2025 14:47 PM"
        $partes = preg_split('/\s+/', trim($texto));
    
        if (count($partes) < 4) return null;
    
        $dia = $partes[0];
        $mes = $meses[strtolower($partes[1])] ?? null;
        $ano = $partes[2];
        $hora = $partes[3];
    
        if (!$mes) return null;
    
        $dataStr = "$dia/$mes/$ano $hora";

        
        $data = DateTime::createFromFormat('d/m/Y h:i', $dataStr);
        // return $dataStr;
        return $data ? $data->format('Y-m-d H:i:s') : null;
    }


    public static function converterData2($texto)
    {
        $meses = [
            'janeiro' => '01',
            'fevereiro' => '02',
            'março' => '03',
            'abril' => '04',
            'maio' => '05',
            'junho' => '06',
            'julho' => '07',
            'agosto' => '08',
            'setembro' => '09',
            'outubro' => '10',
            'novembro' => '11',
            'dezembro' => '12'
        ];
    
        // Extrai hora (últimos 8 caracteres)
        $hora = substr($texto, -8);
        $hora = trim(substr($hora, 0, 5)); // exemplo: "12:50"
    
        // Divide o restante da string
        $partes = explode(" ", trim($texto));
    
        if (count($partes) < 3) {
            return null; // Dados insuficientes para montar a data
        }
    
        $dia = $partes[0];
        $mesNome = strtolower($partes[1]);
        $ano = $partes[2];
    
        if (!isset($meses[$mesNome])) {
            return null; // Mês inválido
        }
    
        $mes = $meses[$mesNome];
        $dataStr = "$ano-$mes-$dia $hora";
    
        // Usa o DateTime para validar e formatar corretamente
        $dataObj = DateTime::createFromFormat('Y-m-d H:i', $dataStr);
    
        if ($dataObj === false) {
            return null; // Conversão falhou
        }
    
        return $dataObj->format('Y-m-d H:i:s');
    }


    public function listarNotasEadCbSd()
    {
        $tipo = 'Cb/Sd PM';
        $notas = Ead::listaNotasEap($tipo);

        $_SESSION['notas'] = $notas;

        header('location: ?pagina=eap&metodo=notaEad');
    }


    public function listarNotasEadSubSgt()
    {
        $tipo = 'Subten/Sgt PM';
        $notas = Ead::listaNotasEap($tipo);

        $_SESSION['notas'] = $notas;

        header('location: ?pagina=eap&metodo=notaEad');
    }



    public function listarNotasEadCapTen()
    {
        $tipo = 'Cap/Ten PM';
        $notas = Ead::listaNotasEap($tipo);

        $_SESSION['notas'] = $notas;

        header('location: ?pagina=eap&metodo=notaEad');
    }


    public function conceitos()
    {
        if (!isset($_GET['id'])) {
            header('location: ?pagina=eap&metodo=turmas');
        }
        $id = $_GET['id'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4) || $_SESSION['cod_perfil'] == 99) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar essa página.';

            header('location: ?pagina=eap&metodo=turmas'); exit;
        }

        
        $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('eap/conceitos.html');


        $parametros = [
            'msg' => $_SESSION['msg'] ?? null,
            'msgText' => $_SESSION['msgText'] ?? null
        ];
        unset($_SESSION['msg'], $_SESSION['msgText']);

        $parametros['discentes'] = Discente::listarDiscentes($id);

        //verificar se existem parametros de consulta pm
        if (isset($_SESSION['parametros'])) {

            $parametros['retornoConsulta'] = true;
            $parametros['foto']        = $_SESSION['parametros']['foto'];
            $parametros['ptgr']        = $_SESSION['parametros']['ptgr'];
            $parametros['re']          = $_SESSION['parametros']['re'];
            $parametros['dgre']        = $_SESSION['parametros']['dgre'];
            $parametros['nome']        = $_SESSION['parametros']['nome'];
            $parametros['guerra']      = $_SESSION['parametros']['guerra'];
            $parametros['email']       = $_SESSION['parametros']['email'];
            $parametros['opm']         = $_SESSION['parametros']['opm'];
            $parametros['funcao']      = $_SESSION['parametros']['funcao'];
            $parametros['codopm']      = $_SESSION['parametros']['codopm'];
            $parametros['cpf']         = $_SESSION['parametros']['cpf'];
            $parametros['dn']          = $_SESSION['parametros']['dn'];
            $parametros['sexo']        = $_SESSION['parametros']['sexo'];
            $parametros['nota']        = $_SESSION['parametros']['nota'];
            $parametros['inicio_ead']  = $_SESSION['parametros']['inicio_ead'];
            $parametros['termino_ead'] = $_SESSION['parametros']['termino_ead'];

            unset($_SESSION['parametros']);

         }

        
        $turma = Turma::buscaTurma($id)[0];


        $parametros['turma'] = $turma['turma'];
        $parametros['id_turma'] = $id;
        $parametros['tipo'] = $turma['tipo'];
        $inicio = new DateTime($turma['inicio']);
        $termino = new DateTime($turma['termino']);
        $parametros['inicio'] = $turma['inicio'];
        $parametros['termino'] = $turma['termino'];   
        $parametros['periodo'] = $inicio->format('d/m/Y') . ' a ' . $termino->format('d/m/Y');
        $parametros['total_discentes'] = $turma['total_discentes'];

        //Breadcrumb
        $parametros['breadcrumb'] = [
            ['label' => 'Início', 'url' => '?pagina=home'],
            ['label' => 'Turmas', 'url' => '?pagina=eap&metodo=turmas'],
            ['label' => 'Turma ' . $parametros['turma'], 'url' => '?pagina=eap&metodo=turma&id=' . $id ],
            ['label' => 'Conceitos ', 'url' => '']
        ];

        $conteudo = $template->render($parametros);

        echo $conteudo;
    }


    public function incluirNotaAP()
    {
        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4) || $_SESSION['cod_perfil'] == 99) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para acessar essa página.';
    
            header('location: ?pagina=home'); exit;
        }

        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('eap/avaliacao-profissional.html');

            $id_turma = $_GET['id'];

            $parametros = array();

            $parametros['turma']     = Turma::buscaTurma($id_turma)[0];
            $parametros['discentes'] = Discente::listarDiscentes($id_turma);

            $parametros [] = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);

            //Breadcrumb
            $parametros['breadcrumb'] = [
                                            ['label' => 'Início', 'url' => '?pagina=home'],
                                            ['label' => 'Turmas', 'url' => '?pagina=eap&metodo=turmas'],
                                            ['label' => $parametros['turma']['turma'], 'url' => '?pagina=eap&metodo=turma&id=' . $id_turma ],
                                            ['label' => 'TAF ', 'url' => '']
                                        ];
    
            $conteudo = $template->render($parametros);

            echo $conteudo;


        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }



    public function atualizarNotaEadDiscentes()
    {
        $id_turma = $_GET['id'];
        $inicio   = $_GET['inicio'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
        }

        $discentes = Discente::listarDiscentes($id_turma);

        $cont = 0;

        foreach($discentes as $d) {

            $resultado = Ead::buscaNotaEad($d['re'], $inicio);

            if (isset($resultado['nota'])) {
                $retorno = Ead::atualizarNotaEad($d['id_discente'], $resultado['nota'], $resultado['termino']);
                if ($retorno){
                    $cont++;
                }
            } 

        }


        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = $cont . ' notas atualizadas.';


        header('location: ?pagina=eap&metodo=turma&id=' . $id_turma);
        exit;



    }
    


    public function gerarPDFConceito()
    {        

        $id = $_GET['id'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4) || $_SESSION['cod_perfil'] == 99) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id); exit;
        }


        $turma     = Turma::buscaTurma($id);
        $discentes = Discente::listarDiscentes($id);

        $primeiro_dia = (new DateTime($turma[0]['inicio']))->format('d/m/Y');
        $terceiro_dia = (new DateTime($turma[0]['termino']))->format('d/m/Y');

        $pdf = new FPDF('L', 'mm', 'A4'); // L = Landscape (horizontal)
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);

        // Cabeçalho centralizado
        $pdf->Cell(0, 1, utf8_decode('COMANDO DE POLICIAMENTO DO INTERIOR SETE'), 0, 1, 'C');
        $pdf->Cell(0, 7, utf8_decode('GABINETE DE TREINAMENTO'), 0, 1, 'C');
        $pdf->Cell(0, 10, utf8_decode('ESTÁGIO DE ATUALIZAÇÃO PROFISSIONAL'), 0, 1, 'C');
        $pdf->Cell(0, 9, utf8_decode($turma[0]['turma'] . ' ' . ' DE ' . $primeiro_dia . ' À ' .  $terceiro_dia), 0, 1, 'C');

        // Linha 1: Cabeçalhos principais
        $pdf->Cell(8, 14, '#', 1, 0, 'C');
        $pdf->Cell(23, 14, utf8_decode('Posto/Grad.'), 1, 0, 'C');
        $pdf->Cell(18, 14, 'RE', 1, 0, 'C');
        $pdf->Cell(76, 14, 'Nome', 1, 0, 'C');
        $pdf->Cell(25, 14, 'OPM', 1, 0, 'C');

        // Salvar posição para subtítulos
        $xSub = $pdf->GetX();
        $ySub = $pdf->GetY();

        $pdf->Cell(31, 7, 'TAT', 1, 0, 'C');
        $pdf->Cell(31, 7, 'TAF', 1, 0, 'C');
        $pdf->Cell(31, 7, utf8_decode('Avaliação'), 1, 0, 'C');

        // Salvar posição para célula "Assinatura"
        $xAss = $pdf->GetX();
        $yAss = $pdf->GetY();
        $pdf->SetXY($xAss, $yAss); // posiciona na linha 1
        $pdf->Cell(35, 14, 'Assinatura', 1, 0, 'C');
        $pdf->SetXY($xSub, $ySub + 7);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, 7, 'Pontos', 1, 0, 'C');
        $pdf->Cell(16, 7, 'Conceito', 1, 0, 'C');
        $pdf->Cell(15, 7, 'Pontos', 1, 0, 'C');
        $pdf->Cell(16, 7, 'Conceito', 1, 0, 'C');
        $pdf->Cell(15, 7, 'Nota', 1, 0, 'C');
        $pdf->Cell(16, 7, 'Conceito', 1, 0, 'C');

        $pdf->Ln();


        $fill = false;
        $pdf->SetFont('Arial', '', 8);
        foreach ($discentes as $i => $discente) {
            if ($fill) {
                $pdf->SetFillColor(230, 230, 230);
            } else {
                // Branco ou sem preenchimento
                $pdf->SetFillColor(255, 255, 255);
            }
                $pdf->SetFillColor(200,220,255);

            $avaliacao = $discente['avaliacao_nota'] + $discente['nota_ead'];
            if ($discente['avaliacao_nota'] == '' or $discente['nota_ead'] == ''){
                $avaliacao = '';
                $conceito_avaliacao = '';
            } else {
                if ($avaliacao < 5) {
                    $conceito_avaliacao = 'Insuficiente';
                } elseif ($avaliacao >= 5 && $avaliacao < 7) {
                    $conceito_avaliacao = 'Regular';
                } elseif ($avaliacao >= 7 && $avaliacao < 8.5) {
                    $conceito_avaliacao = 'Bom';
                } elseif ($avaliacao >= 8.5 && $avaliacao < 9.6) {
                    $conceito_avaliacao = 'MB';
                } else {
                    $conceito_avaliacao = 'Excep.';
                }
            }


            $conceito_tat = $discente['conceito_tat'] == 'Muito Bom' ? 'MB' : $discente['conceito_tat'];
            $conceito_taf = $discente['conceito_taf'] == 'Muito Bom' ? 'MB' : $discente['conceito_taf'];

            $pdf->Cell(8, 6, $i+1, 1, 0, 'C');
            $pdf->Cell(23, 6, utf8_decode($discente['pt_gr']), 1, 0, 'C');
            $pdf->Cell(18, 6, $discente['re'] . '-' . $discente['dg_re'], 1, 0, 'C');
            $pdf->Cell(76, 6, utf8_decode($discente['nome']), 1, 0);
            $pdf->Cell(25, 6, $discente['opm'], 1, 0, 'C');
            $pdf->Cell(15, 6, $discente['pontuacao_tat'], 1, 0, 'C');
            $pdf->Cell(16, 6, $conceito_tat, 1, 0, 'C');
            $pdf->Cell(15, 6, $discente['pontuacao_taf'], 1, 0, 'C');
            $pdf->Cell(16, 6, $conceito_taf, 1, 0, 'C');
            $pdf->Cell(15, 6, $avaliacao, 1, 0, 'C');
            $pdf->Cell(16, 6, utf8_decode($conceito_avaliacao), 1, 0, 'C');
            $pdf->Cell(35, 6, '', 1, 1);

            $fill = !$fill; // alterna a variável para a próxima linha
        }

        $pdf->Output('I', $turma[0]['turma'] . ' ' . $turma[0]['inicio'] . ' a ' . $turma[0]['termino'] . '.pdf');

        exit;

    }


    public function gravarAvaliacao()
    {

        
        $id_turma          = $_POST['id_turma'];
        $data_ap           = $_POST['data_ap'];
        $id_discentes      = $_POST['id_discente'] ?? [];
        $avaliacao_acertos = $_POST['avaliacao_acertos'] ?? [];
        $avaliacao_nota    =  $_POST['avaliacao_nota'] ?? [];


        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
        }
        
        $discentes = array();

        foreach($id_discentes as $re => $id)
        {
           $discentes[] = [
                'id_discente'       => $id_discentes[$re],
                'avaliacao_acertos' => $avaliacao_acertos[$re],
                'avaliacao_nota'    => $avaliacao_nota[$re]
           ];

        }

        if (Discente::gravarAvaliacao($data_ap, $discentes))
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Notas salvas com sucesso.';
        } else {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Erro ao gravar as notas.';
        }




        header('location: ?pagina=eap&metodo=turma&id='.$id_turma);
    }



    public function inclusaoEmGrupo()
    {
        $registros = preg_split('/\r\n|\r|\n/', $_POST['listaRE']);

        $registros = array_filter($registros, function($linha) {
            return trim($linha) !== '';
        });

        $id_turma = $_POST['id_turma'];
        $inicio   = $_POST['inicio'];
        $tipo     = $_POST['tipo'];


        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4)) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
        }

        $erros = [];
        $sucessos = 0;

        foreach($registros as $re)
        {
            $re = trim($re);
            $policial = PolicialController::buscaPM($re);

            if (!$policial) {
                $erros[] = "RE $re não localizado.";
                continue;
            }

            // Verifica tipo
            if (in_array($policial->ptgr, ['CB PM', 'SD PM - 1C', 'SD PM - 2C'])) {
                $tipo_policial = 'Cb/Sd PM';
            } elseif (in_array($policial->ptgr, ['SUBTEN PM', '1. SGT PM', '2. SGT PM', '3. SGT PM'])) {
                $tipo_policial = 'Subten/Sgt PM';
            } else if ( $policial->ptgr == 'CAP PM' or $policial->ptgr == '1. TEN PM' or $policial->ptgr == '2. TEN PM' )  {
                $tipo_policial = 'Cap/Ten PM';
            } else {
                $tipo_policial = 'Oficiais PM';
            }

            if ($tipo_policial != $tipo) {
                $erros[] = " RE $re posto/graduação diferente da turma.";
                continue;
            }

            // Já está na turma?
            if (Discente::verificaInscritoTurma($id_turma, $re)) {
                $erros[] = " RE $re já está inscrito nesta turma.";
                continue;
            }

            // Dados para inclusão
            $nota = Ead::buscaNotaEad($re, $inicio);

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
                'dn' => substr($policial->dataNascimento, 0,10),
                'nota' => $nota['nota'] ?? null,
                'inicio_ead' => $nota['inicio'] ?? null,
                'termino_ead' => $nota['termino'] ?? null,
                'id_turma' => $id_turma
            ];

            if (Discente::inserirPM($parametros)) {
                $sucessos++;
            } else {
                $erros[] = "Erro ao incluir RE $re na turma.";
            }
        }

        // Mensagens finais
        if ($sucessos > 0) {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = "$sucessos policial(is) incluído(s) com sucesso. ";
            if (!empty($erros)) {
                $_SESSION['msgText'] .= "Ocorreram os seguintes problemas:" . implode($erros);
            }
        } else {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = "Nenhum policial incluído." . implode($erros);
        }

        header('location: ?pagina=eap&metodo=turma&id=' . $id_turma);
    }


    public function habilitaDesabilitaAvaliacao()
    {
        $id_turma = $_POST['id_turma'];
        
        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
        }

        //caso esteja habilitando pra uma turma, desabilitar todas as outras
        if (isset($_POST['habilita_avaliacao'])) {
            Turma::desabilitaTodasAvaliacoes();
        }
        Turma::habilitaDesabilitaAvaliacao($_POST);
        header('location: ?pagina=eap&metodo=turma&id=' . $id_turma);
    }


    public function gravarBI()
    {
        $id_turma = $_POST['id_turma'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
        }


        if (Turma::gravarBI($_POST))
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Número do Boletim Interno salvo.';
        } else {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Erro ao gravar o número do Boletim Interno.';
        }




        header('location: ?pagina=eap&metodo=turma&id='.$id_turma);
    }
    
    
    
}