<?php

class Discente
{


   public static function inserirPM($dados)
    {
        $nota_ead = trim($dados['nota'] ?? '');
        $nota_ead = ($nota_ead === '') ? null : str_replace(',', '.', $nota_ead);

        $ias = empty($dados['ias']) ? null : $dados['ias'];

        $conclusao_ead = trim($dados['termino_ead'] ?? '');
        $conclusao_ead = ($conclusao_ead === '') ? null : date('Y-m-d H:i:s', strtotime($conclusao_ead));

        $con = Connection::getConn();

        $sql = "INSERT INTO eap_discentes (
                    pt_gr, re, dg_re, nome, guerra, email, opm, codopm, funcao, ias, turmas_id_turma, usuario, cpf, data_nascimento, sexo, nota_ead, conclusao_ead
                ) VALUES (
                    :ptgr, :re, :dgre, :nome, :guerra, :email, :opm, :codopm, :funcao, :ias, :turmas_id_turma, :usuario, :cpf, :dn, :sexo, :nota, :conclusao_ead
                )";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':ptgr',            $dados['ptgr']);
        $stmt->bindValue(':re',              $dados['re']);
        $stmt->bindValue(':dgre',            $dados['dgre']);
        $stmt->bindValue(':nome',            $dados['nome']);
        $stmt->bindValue(':guerra',          $dados['guerra']);
        $stmt->bindValue(':email',           $dados['email']);
        $stmt->bindValue(':opm',             $dados['opm']);
        $stmt->bindValue(':codopm',          $dados['codopm']);
        $stmt->bindValue(':funcao',          $dados['funcao'] ?? '');
        $stmt->bindValue(':ias',             $ias);
        $stmt->bindValue(':turmas_id_turma', $dados['id_turma']);
        $stmt->bindValue(':cpf',             $dados['cpf']);
        $stmt->bindValue(':dn',              $dados['dn']);
        $stmt->bindValue(':sexo',            $dados['sexo']);
        $stmt->bindValue(':usuario',         $_SESSION['usuario']);
        $stmt->bindValue(':nota',            $nota_ead, $nota_ead === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':conclusao_ead',   $conclusao_ead, $conclusao_ead === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }


    public static function listarDiscentes($id_turma)
    {
        $con = Connection::getConn();

        $sql = "
            SELECT 
                *,
                CASE 
                    WHEN avaliacao_nota IS NOT NULL THEN nota_ead + avaliacao_nota
                    ELSE NULL
                END AS nota_final,
                CASE 
                    WHEN avaliacao_nota IS NOT NULL THEN 
                        CASE 
                            WHEN (nota_ead + avaliacao_nota) < 5 THEN 'Insuficiente'
                            WHEN (nota_ead + avaliacao_nota) < 7 THEN 'Regular'
                            WHEN (nota_ead + avaliacao_nota) < 8.5 THEN 'Bom'
                            WHEN (nota_ead + avaliacao_nota) < 9.6 THEN 'MB'
                            WHEN (nota_ead + avaliacao_nota) <= 10 THEN 'Excep.'
                            ELSE NULL
                        END
                    ELSE NULL
                END AS conceito_final
            FROM eap_discentes
            WHERE turmas_id_turma = :id_turma
            ORDER BY id_discente
        ";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_turma', $id_turma, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function inserirNotaEad($dados)
    {
        $con = Connection::getConn();
    
        $sql = "UPDATE eap_discentes 
                SET nota_ead = :nota,
                    conclusao_ead = :finalizacao_ead
                WHERE id_discente = :id_discente";
    
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':nota', $dados['nota']);
        $stmt->bindValue(':id_discente', $dados['id_discente']);
        $stmt->bindValue(':finalizacao_ead', $dados['finalizacao_ead']);

    
        return $stmt->execute();
    }


    public static function removerPMTurma($id_discente)
    {
        $con = Connection::getConn();

        $sql = "DELETE FROM eap_discentes WHERE id_discente = :id_discente";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_discente', $id_discente);

        return $stmt->execute();
    }


    public static function procuraDiscenteNaTurma($re, $id_turma)
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM eap_discentes WHERE turma_id_tuma = :id_turma AND re = :re";
        
    }


    public static function concluiramEAP($id_eap)
    {
        $con = Connection::getConn();
        $hoje = date('Y-m-d');


       /*$sql = "SELECT count(*) as total FROM gt.eap_discentes as d
                JOIN eap_turmas  as t
                ON d.turmas_id_turma = t.id_turma
                WHERE eap_id_eap = :id_eap AND d.status = '1'";*/

        $sql = "SELECT COUNT(*) AS total
                FROM gt.eap_discentes AS d
                JOIN eap_turmas AS t ON d.turmas_id_turma = t.id_turma
                WHERE t.eap_id_eap = :id_eap
                AND t.termino < :hoje;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->bindValue(':hoje',   $hoje);
        $stmt->execute();
        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado;
    } 


    public static function conceitoMBTAF($id_eap)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) as total FROM gt.eap_discentes as d
                JOIN eap_turmas  as t
                ON d.turmas_id_turma = t.id_turma
                WHERE eap_id_eap = :id_eap AND d.conceito_taf = 'Muito Bom'";

        $stmt = $con->prepare($sql);

        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->execute();
        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado;
    }


    public static function reprovadosTAF($id_eap)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) as total FROM gt.eap_discentes as d
                JOIN eap_turmas  as t
                ON d.turmas_id_turma = t.id_turma
                WHERE eap_id_eap = :id_eap AND d.conceito_taf = 'Reprovado'";

        $stmt = $con->prepare($sql);

        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->execute();
        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado;
    }


    public static function lesionadosTAF($id_eap)
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) as total FROM gt.eap_discentes as d
                JOIN eap_turmas  as t
                ON d.turmas_id_turma = t.id_turma
                WHERE eap_id_eap = :id_eap AND d.lesao_taf != '0'";

        $stmt = $con->prepare($sql);

        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->execute();
        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado; 
    }

    public static function gravarTAT($data_tat, $discentes)
    {
        $usuario  = $_SESSION['usuario'];
        $insercao = date('Y-m-d H:i:s');

        $con = Connection::getConn();

        try {
            $con->beginTransaction(); // Inicia a transação

            $sql = "UPDATE eap_discentes SET 
                        pontuacao_tat = :pontuacao,
                        conceito_tat  = :conceito,
                        nota_tat      = :nota,
                        data_tat      = :data_tat,
                        insercao_tat  = :insercao_tat,
                        usuario_tat   = :usuario
                    WHERE id_discente = :id_discente";

            $stmt = $con->prepare($sql);

            foreach ($discentes as $discente) {
                $nota = is_numeric($discente['nota']) ? $discente['nota'] : null;
                $stmt->bindParam(':pontuacao', $discente['pontuacao_tat']);
                $stmt->bindParam(':conceito',  $discente['conceito_tat']);
                $stmt->bindParam(':nota',      $nota);
                $stmt->bindParam(':data_tat',  $data_tat);
                $stmt->bindParam(':insercao_tat', $insercao);
                $stmt->bindParam(':usuario',   $usuario);
                $stmt->bindParam(':id_discente', $discente['id_discente']);
                $stmt->execute();
            }

            $con->commit(); // Finaliza a transação com sucesso
            return true;

        } catch (PDOException $e) {
            $con->rollBack(); // Reverte alterações em caso de erro
            echo error_log("Erro ao gravar TAT: " . $e->getMessage());
            echo 'erro'. $e->getMessage(); exit;
            return false;
        }
    }


    public static function gravarTAF($dados)
    {
        $usuario  = $_SESSION['usuario'];
        $insercao = date('Y-m-d H:i:s');

        $con = Connection::getConn();


        $id_turma = $dados['id_turma'];
        $id_discente = $dados['id_discente'];

        // Prepare os demais campos
        $idade_taf = $dados['idade_taf'];
        $barra = $dados['barra'];
        $isometria = $dados['isometria'];
        $corrida_2400 = $dados['corrida_2400'];
        $apoio = $dados['apoio'];
        $corrida_50 = $dados['corrida_50'];
        $abdominal = $dados['abdominal'];
        $natacao = $dados['natacao'];

        $barra_pontos = $dados['barra_pontos'];
        $isometria_pontos = $dados['isometria_pontos'];
        $corrida_2400_pontos = $dados['corrida_2400_pontos'];
        $apoio_pontos = $dados['apoio_pontos'];
        $corrida_50_pontos = $dados['corrida_50_pontos'];
        $abdominal_pontos = $dados['abdominal_pontos'];
        $natacao_pontos = $dados['natacao_pontos'];

        $pontuacao = $dados['pontuacao'];
        $resultado = $dados['resultado'];

        $lesao_taf = $dados['lesao'];

        $sql = "UPDATE eap_discentes SET 
                    barra = ?,
                    isometria = ?,
                    corrida_2400 = ?,
                    apoio_frente = ?,
                    corrida_50 = ?,
                    abdominal = ?,
                    natacao = ?,
                    barra_pontos = ?,
                    isometria_pontos = ?,
                    corrida_2400_pontos = ?,
                    apoio_frente_pontos = ?,
                    corrida_50_pontos = ?,
                    abdominal_pontos = ?,
                    natacao_pontos = ?,
                    pontuacao_taf = ?,
                    conceito_taf = ?,
                    usuario_taf = ?,
                    insercao_taf = ?,
                    lesao_taf = ?
                WHERE id_discente = ?";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            $barra,
            $isometria,
            $corrida_2400,
            $apoio,
            $corrida_50,
            $abdominal,
            $natacao,
            $barra_pontos,
            $isometria_pontos,
            $corrida_2400_pontos,
            $apoio_pontos,
            $corrida_50_pontos,
            $abdominal_pontos,
            $natacao_pontos,
            $pontuacao,
            $resultado,
            $usuario,
            $insercao,
            $lesao_taf,
            $id_discente,
        ]);

        return $stmt;
       
    }


    public static function gravarAvaliacao($data_avaliacao, $discentes)
    {

        $con = Connection::getConn();

        try {
            $con->beginTransaction(); // Inicia a transação

            $sql = "UPDATE eap_discentes SET 
                        avaliacao_acertos = :avaliacao_acertos,
                        avaliacao_nota    = :avaliacao_nota,
                        avaliacao_data    = :data_ap
                    WHERE id_discente     = :id_discente";

            $stmt = $con->prepare($sql);

            foreach ($discentes as $discente) {
                $nota = is_numeric($discente['avaliacao_nota']) ? $discente['avaliacao_nota'] : null;

                $stmt->bindParam(':avaliacao_acertos', $discente['avaliacao_acertos']);
                $stmt->bindParam(':avaliacao_nota',    $nota);
                $stmt->bindParam(':data_ap',           $data_avaliacao);
                $stmt->bindParam(':id_discente',       $discente['id_discente']);
                $stmt->execute();
            }

            $con->commit(); // Finaliza a transação com sucesso
            return true;

        } catch (PDOException $e) {
            $con->rollBack(); // Reverte alterações em caso de erro
            echo error_log("Erro ao gravar as notas: " . $e->getMessage());
            echo 'erro'. $e->getMessage(); exit;
            return false;
        }
    }


    public static function verificaInscritoTurma($id_turma, $re)
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM eap_discentes WHERE turmas_id_turma = :id_turma AND re = :re";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_turma', $id_turma);
        $stmt->bindValue(':re',       $re);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }


    public static function atualizarTafViaSitaf($dados_sitaf, $policial, $id_turma)
    {

        $con = Connection::getConn();

        $usuario_taf  = $_SESSION['usuario'];
        $insercao_taf = date('Y-m-d H:i:s');

        $id_discente  = $policial["id_discente"];
        $idade        = $dados_sitaf["idade"];
        $sexo         = $dados_sitaf["sexo"];
        $circ_abd     = $dados_sitaf["circ_abd"];
        $bat_cardiaco = $dados_sitaf["bat_cardiaco"];
        $pressao      = $dados_sitaf["pressao"];
        $peso         = str_replace(',', '.', $dados_sitaf["peso"]);
        $altura       = str_replace(',', '.', $dados_sitaf["altura"]);
        $imc          = str_replace(',', '.', $dados_sitaf["imc"]);
        $abd          = $dados_sitaf["abd"];
        $pt_abd       = $dados_sitaf["pt_abd"];
        $corrida      = $dados_sitaf["corrida"];
        $pt_corrida   = $dados_sitaf["pt_corrida"];
        $flexao       = $dados_sitaf["flexao"]; echo $flexao;
        $pt_flexao    = $dados_sitaf["pt_flexao"];
        $tiro_50m     = str_replace(',', '.', $dados_sitaf["tiro_50m"]);
        $pt_tiro50m   = $dados_sitaf["pt_tiro50m"];
        $natacao      = $dados_sitaf["natacao"];
        $pt_natacao   = $dados_sitaf["pt_natacao"];
        $pontuacao_taf= $dados_sitaf["total"];
        $conceito_taf = $dados_sitaf["conclusao"];
        
        if ($sexo=='F') {
            $isometria    = str_replace(',', ':', $dados_sitaf["barra"]);
            $pt_isometria = $dados_sitaf["pt_barra"];
            $barra        = 0;
            $pt_barra     = 0;
        } else {
            $isometria    = 0;
            $pt_isometria = 0;
            $barra        = $dados_sitaf["barra"];
            $pt_barra     = $dados_sitaf["pt_barra"];
        }



        $sql = "UPDATE eap_discentes 
               SET idade = :idade,
                   peso = :peso,
                   altura = :altura,
                   imc = :imc,
                   barra = :barra,
                   isometria = :isometria,
                   corrida_2400 = :corrida_2400,
                   apoio_frente = :apoio_frente,
                   corrida_50 = :corrida_50,
                   abdominal = :abdominal,
                   natacao = :natacao,
                   barra_pontos = :barra_pontos,
                   isometria_pontos = :isometria_pontos,
                   corrida_2400_pontos = :corrida_2400_pontos,
                   apoio_frente_pontos = :apoio_frente_pontos,
                   corrida_50_pontos = :corrida_50_pontos,
                   abdominal_pontos = :abdominal_pontos,
                   natacao_pontos = :natacao_pontos,
                   pontuacao_taf = :pontuacao_taf,
                   conceito_taf = :conceito_taf,
                   usuario_taf = :usuario_taf,
                   insercao_taf = :insercao_taf,
                   circ_abdominal = :circ_abdominal,
                   batimento_cardiaco = :batimento_cardiaco,
                   pressao_arterial = :pressao_arterial
             WHERE id_discente = :id_discente AND turmas_id_turma = :id_turma";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':idade'                => $idade,
        ':peso'                 => $peso,
        ':altura'               => $altura,
        ':imc'                  => $imc,
        ':barra'                => $barra,
        ':isometria'            => $isometria,
        ':corrida_2400'         => $corrida,
        ':apoio_frente'         => $flexao,
        ':corrida_50'           => $tiro_50m,
        ':abdominal'            => $abd,
        ':natacao'              => $natacao,
        ':barra_pontos'         => $pt_barra,
        ':isometria_pontos'     => $pt_isometria,
        ':corrida_2400_pontos'  => $pt_corrida,
        ':apoio_frente_pontos'  => $pt_flexao,
        ':corrida_50_pontos'    => $pt_tiro50m,
        ':abdominal_pontos'     => $pt_abd,
        ':natacao_pontos'       => $pt_natacao,
        ':pontuacao_taf'        => $pontuacao_taf,
        ':conceito_taf'         => $conceito_taf,
        ':id_discente'          => $id_discente,
        ':usuario_taf'          => $usuario_taf,
        ':insercao_taf'         => $insercao_taf,
        ':circ_abdominal'       => $circ_abd,
        ':batimento_cardiaco'   => $bat_cardiaco,
        ':pressao_arterial'     => $pressao,
        ':id_turma'             => $id_turma
    ]);

    }


    public static function listarPorRE($re)
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM eap_discentes AS d
                JOIN eap_turmas AS t
                ON
                d.turmas_id_turma = t.id_turma
                WHERE re = :re
                ORDER BY t.inicio DESC";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':re', $re, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function meuEap($id_eap)
    {
        $re = $_SESSION['re'];
        $con = Connection::getConn();
        $sql = "SELECT *
                FROM gt.eap_discentes AS d
                JOIN eap_turmas AS t ON d.turmas_id_turma = t.id_turma
                WHERE t.eap_id_eap = :id_eap
                AND d.re = :re LIMIT 1;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_eap', $id_eap);
        $stmt->bindValue(':re',     $re);
        $stmt->execute();
        
        return $stmt->fetch();

    }
   
}