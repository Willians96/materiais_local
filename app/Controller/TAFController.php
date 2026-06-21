<?php


class TAFController
{

    public function index()
    {

        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('taf/taf.html');

            $_SESSION['msg'] = 'error';
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

    public function incluirTAF()
    {
        try {

            $loader = new \Twig\Loader\FilesystemLoader(['app/View', 'app/View/Partners']);
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('taf/taf-inclusao.html');

            $id_turma = $_GET['id'];

            //verificar permissão do perfil
            if (!($_SESSION['cod_perfil'] <= 4 || $_SESSION['cod_perfil'] == 99 ) ) {
                $_SESSION['msg'] = 'error';
                $_SESSION['msgText'] = 'Você não possui permissão para acessar esta página.';
        
                header('location: ?pagina=eap&metodo=turma&id='.$id_turma); exit;
            }

            $parametros = array();

            $parametros = [
                'msg' => $_SESSION['msg'] ?? null,
                'msgText' => $_SESSION['msgText'] ?? null
            ];
            unset($_SESSION['msg'], $_SESSION['msgText']);

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

    public function gravarTAF()
    {

        $id_turma = $_POST['id_turma'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4 ) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('Location: ' . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma)); exit;
        }

        if ( Discente::gravarTAF($_POST) )
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'TAF incluído com sucesso.';
        } else {
            $_SESSION['msg'] = 'erro';
            $_SESSION['msgText'] = 'Erro ao incluir o TAF.';
        }

        /*$id_turma      = $_POST['id_turma'];
        $data_taf      = $_POST['data_taf'];

        $id_discentes  = $_POST['id_discente'] ?? [];
        $pontuacao_taf = $_POST['pontuacao_taf'] ?? [];
        $conceito_taf  = $_POST['conceito_taf'] ?? [];
        
        $discentes = array();

        foreach($id_discentes as $re => $id)
        {
           $discentes[] = [
                'id_discente'   => $id_discentes[$re],
                'pontuacao_taf' => $pontuacao_taf[$re],
                'conceito_taf'  => $conceito_taf[$re]
           ];

        }

        if (Discente::gravarTAF($data_taf, $discentes))
        {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'TAF incluído com sucesso.';
        } else {
            $_SESSION['msg'] = 'success';
            $_SESSION['msgText'] = 'Erro ao incluir o TAF.';
        }*/




        header('Location: ' . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma));

    }

    public static function uploadPlanilhaSITAF()
    {
        $id_turma = $_POST['id_turma'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4 ) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('Location: ' . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma)); exit;
        }

        if (!isset($_FILES['planilha']) || $_FILES['planilha']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao fazer upload da planilha.";
          //  header("Location: ?pagina=taf");
          echo 'Erro ao processar a planilha';
            exit;
        }

        // Caminho temporário
        $arquivoTmp = $_FILES['planilha']['tmp_name'];

        // Abrir arquivo como CSV (Excel salva como "CSV separado por vírgulas" ou "CSV UTF-8")
        if (($handle = fopen($arquivoTmp, "r")) !== false) {
            $dados = [];
            $linha = 0;

            while (($row = fgetcsv($handle, 1000, ";")) !== false) { // geralmente ; no Brasil
                $linha++;

                // pula cabeçalho
                if ($linha == 1) {
                    continue;
                }

                $dados[] = [
                    're'          => trim($row[0]),
                    'ptgr_nome'   => trim($row[1]),
                    'opm'         => trim($row[2]),
                    'idade'       => trim($row[3]),
                    'sexo'        => trim($row[4]),
                    'circ_abd'    => trim($row[5]),
                    'bat_cardiaco'=> trim($row[6]),
                    'pressao'     => trim($row[7]),
                    'peso'        => trim($row[8]),
                    'altura'      => trim($row[9]),
                    'imc'         => trim($row[10]),
                    'barra'       => trim($row[11]),
                    'pt_barra'    => trim($row[12]),
                    'abd'         => trim($row[13]),
                    'pt_abd'      => trim($row[14]),
                    'tiro_50m'    => trim($row[15]),
                    'pt_tiro50m'  => trim($row[16]),
                    'flexao'      => trim($row[17]),
                    'pt_flexao'   => trim($row[18]),
                    'corrida'     => trim($row[19]),
                    'pt_corrida'  => trim($row[20]),
                    'natacao'     => trim($row[21]),
                    'pt_natacao'  => trim($row[22]),
                    'total'       => trim($row[23]),
                    'conclusao'   => trim($row[24]),
                ];
            }
            fclose($handle);



            $discentes = Discente::listarDiscentes($id_turma);

            $error = array();

            foreach($discentes as $policial){

                $encontrado = false;

                foreach($dados as $p) {
                    if ($p['re'] == $policial['re']) {
                        echo $p['ptgr_nome'] . ' - ' . $policial['re'] . '|' . $p['re'] . ' - ' . $policial['guerra'] . ' - ' . $p['conclusao'] . '<br>';

                        Discente::atualizarTafViaSitaf($p, $policial, $id_turma);

                        $encontrado = true;                        
                    }
                }

                if ($encontrado == false) {
                    $error[] = [ 
                                "pt_gr"  => $policial['pt_gr'],
                                "re"     => $policial['re'],
                                "guerra" => $policial['guerra']
                                ];
                }
                
            }


            $_SESSION['msg'] = "success";
            $_SESSION['msgText'] = "Dados do TAF atualizados.";
            header("Location: " . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma));
            exit;
            

        } else {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Erro ao ler a planilha.";
            header("Location: " . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma));
            exit;
        }
    }


    public static function colarPlanilhaSITAF()
    {
        $id_turma = $_POST['id_turma'];

        //verificar permissão do perfil
        if (!($_SESSION['cod_perfil'] <= 4 ) ) {
            $_SESSION['msg'] = 'error';
            $_SESSION['msgText'] = 'Você não possui permissão para esta operação.';
    
            header('Location: ' . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma)); exit;
        }

        if (empty($_POST['celulas'])) {
            $_SESSION['msg'] = "error";
            $_SESSION['msgText'] = "Nenhum dado foi colado.";
            header("Location: " . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma));
            exit;
        }

        // conteúdo colado na textarea
        $celulas = trim($_POST['celulas']);

        // separa em linhas
        $linhas = preg_split("/\r\n|\n|\r/", $celulas);

        $dados = [];
        $linha = 0;

        foreach ($linhas as $row) {
            $linha++;

            // separa colunas por TAB
            $colunas = explode("\t", $row);

            // pula cabeçalho
            if ($linha == 1) {
                continue;
            }

            // monta array igual ao do uploadPlanilhaSITAF
            $dados[] = [
                're'          => trim($colunas[0] ?? ''),
                'ptgr_nome'   => trim($colunas[1] ?? ''),
                'opm'         => trim($colunas[2] ?? ''),
                'idade'       => trim($colunas[3] ?? ''),
                'sexo'        => trim($colunas[4] ?? ''),
                'circ_abd'    => trim($colunas[5] ?? ''),
                'bat_cardiaco'=> trim($colunas[6] ?? ''),
                'pressao'     => trim($colunas[7] ?? ''),
                'peso'        => trim($colunas[8] ?? ''),
                'altura'      => trim($colunas[9] ?? ''),
                'imc'         => trim($colunas[10] ?? ''),
                'barra'       => trim($colunas[11] ?? ''),
                'pt_barra'    => trim($colunas[12] ?? ''),
                'abd'         => trim($colunas[13] ?? ''),
                'pt_abd'      => trim($colunas[14] ?? ''),
                'tiro_50m'    => trim($colunas[15] ?? ''),
                'pt_tiro50m'  => trim($colunas[16] ?? ''),
                'flexao'      => trim($colunas[17] ?? ''),
                'pt_flexao'   => trim($colunas[18] ?? ''),
                'corrida'     => trim($colunas[19] ?? ''),
                'pt_corrida'  => trim($colunas[20] ?? ''),
                'natacao'     => trim($colunas[21] ?? ''),
                'pt_natacao'  => trim($colunas[22] ?? ''),
                'total'       => trim($colunas[23] ?? ''),
                'conclusao'   => trim($colunas[24] ?? ''),
            ];
        }

        // lista discentes da turma
        $discentes = Discente::listarDiscentes($id_turma);

        $error = [];

        foreach ($discentes as $policial) {
            $encontrado = false;

            foreach ($dados as $p) {
                if ($p['re'] == $policial['re']) {
                    Discente::atualizarTafViaSitaf($p, $policial, $id_turma);
                    $encontrado = true;
                }
            }

            if (!$encontrado) {
                $error[] = [
                    "pt_gr"  => $policial['pt_gr'],
                    "re"     => $policial['re'],
                    "guerra" => $policial['guerra']
                ];
            }
        }

        if (!empty($error)) {
            $_SESSION['msg'] = "alert";
            $naoLocalizados = [];
            foreach ($error as $policial) {
                $naoLocalizados[] = "<br>" . $policial['pt_gr'] . " " . $policial['guerra'];
            }
            //$_SESSION['msgText'] = "Não localizado(s) na planilha: " . implode(", ", $naoLocalizados);            
            $_SESSION['msgText'] = "Não localizado(s) na planilha: " . implode($naoLocalizados);
        } else {
            $_SESSION['msg'] = "success";
            $_SESSION['msgText'] = "Dados do TAF atualizados via colagem.";
        }

        header("Location: " . AppConfig::url('?pagina=TAF&metodo=incluirTAF&id=' . $id_turma));
        exit;
    }




    
}