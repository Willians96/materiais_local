<?php


class Ead{

    public static function gravarNotaEad($dados)
    {

        $usuario = $_SESSION['usuario'];
        $inseridos = 0;
        $ignorados = 0;

        $con = Connection::getConn();

        $sql = "INSERT INTO eap_nota_ead 
            (re, nome, sobrenome, unidade, pt_gr, status, inicio, termino, tempo, tipo, usuario, nota)
            VALUES 
            (:re, :nome, :sobrenome, :unidade, :ptgr, :status, :inicio, :termino, :tempo, :tipo, :usuario, :nota)";
    
        $stmt = $con->prepare($sql);
        
        foreach ($dados as $policial) {

            if (self::verificaSeInseridoTabela($policial)) {
                $ignorados++;
                continue; // já inserido, pula para o próximo
            }
        
            $sobrenome = substr(trim($policial['sobrenome']), 0, 45);
            $nome     = substr(trim($policial['nome']), 0, 45);
            $re       = $policial['re'];
            $unidade  = substr(trim($policial['unidade']), 0, 30);
            $ptgr     = $policial['ptgr'];
            $status   = $policial['status'];
            $inicio   = $policial['inicio'];
            $termino  = $policial['termino'];
            $tempo    = substr(trim($policial['tempo']), 0, 30);
            $nota     = str_replace(',', '.', trim($policial['nota']));
            $tipo     = self::verificarTipo($ptgr);

            if ($ptgr == 'SOLDADO PM 2. CLASSE') {
                $ptgr = 'SOLDADO PM';
            }
        
            $stmt->execute([
                ':re'       => $re,
                ':nome'     => $nome,
                ':sobrenome'=> $sobrenome,
                ':unidade'  => $unidade,
                ':ptgr'     => $ptgr,
                ':status'   => $status,
                ':inicio'   => $inicio ? date('Y-m-d H:i:s', strtotime($inicio)) : null,
                ':termino'  => $termino ? date('Y-m-d H:i:s', strtotime($termino)) : null,
                ':tempo'    => $tempo,
                ':tipo'     => $tipo,
                ':usuario'  => $usuario,
                ':nota'     => $nota
            ]);
            $inseridos++;

        }

        $_SESSION['msg'] = 'success';
        $_SESSION['msgText'] = "Notas gravadas com sucesso. Inserido $inseridos registro(s), ignorado(s) $ignorados.";
    
        return true;

    }


    public static function verificarTipo($ptgr)
    {
        $pref = substr($ptgr, 0, 4);
        
        if ($pref == 'SOLD' or $pref == 'CABO')
        {
            $tipo = 'Cb/Sd PM';
        } else if ($pref == '1. T' or $pref == '2. T' or $pref == 'CAP ')
        {
            $tipo = 'Cap/Ten PM';
        } else 
        {
            $tipo = 'Subten/Sgt PM';
        }
        return $tipo;
    }


    public static function verificaSeInseridoTabela($policial)
    {
        $re = $policial['re'];
        $termino = $policial['termino'];
        $termino = $termino ? date('Y-m-d H:i:s', strtotime($termino)) : null;
    
        $con = Connection::getConn();
        $sql = "SELECT COUNT(*) FROM gt.eap_nota_ead WHERE re = :re AND termino = :termino;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':re', $re);
        $stmt->bindValue(':termino', $termino);
        $stmt->execute();
    
        $quantidade = $stmt->fetchColumn();
    
        return $quantidade > 0;
    }


    public static function listaNotasEap($tipo)
    {
        $con = Connection::getConn();
        $sql =  "SELECT * FROM gt.eap_nota_ead WHERE tipo = :tipo;";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->execute();
        $resultado = $stmt->fetchAll();

        return $resultado;
    }


    public static function buscaNotaEad($re, $inicio)
    {
        $timestamp = strtotime($inicio);
        $ano = date('Y', $timestamp);
        $mes = (int)date('m', $timestamp);

        // Meses válidos: mês atual e dois anteriores
        $mesAtual = $mes;
        $mesAnterior1 = $mes - 1 <= 0 ? 12 + ($mes - 1) : $mes - 1;
        $mesAnterior2 = $mes - 2 <= 0 ? 12 + ($mes - 2) : $mes - 2;

        // Ano para os meses anteriores (caso mês atual seja janeiro ou fevereiro)
        $anoMes1 = $mesAnterior1 > $mes ? $ano - 1 : $ano;
        $anoMes2 = $mesAnterior2 > $mes ? $ano - 1 : $ano;

        $conn = Connection::getConn();

        //busca a nota dos últimos 3 meses, incluindo o mês de início do eap
        $sql = "SELECT inicio, termino, nota
                FROM gt.eap_nota_ead
                WHERE re = :re
                AND (
                        (YEAR(termino) = :anoAtual AND MONTH(termino) = :mesAtual)
                    OR (YEAR(termino) = :anoMes1 AND MONTH(termino) = :mesAnterior1)
                    OR (YEAR(termino) = :anoMes2 AND MONTH(termino) = :mesAnterior2)
                )
                AND termino <= :inicio
                ORDER BY nota DESC
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':re', $re);
        $stmt->bindValue(':anoAtual', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':mesAtual', $mesAtual, PDO::PARAM_INT);
        $stmt->bindValue(':anoMes1', $anoMes1, PDO::PARAM_INT);
        $stmt->bindValue(':mesAnterior1', $mesAnterior1, PDO::PARAM_INT);
        $stmt->bindValue(':anoMes2', $anoMes2, PDO::PARAM_INT);
        $stmt->bindValue(':mesAnterior2', $mesAnterior2, PDO::PARAM_INT);
        $stmt->bindValue(':inicio', date('Y-m-d H:i:s', $timestamp));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function alterarIAS($dados)
    {
        $con = Connection::getConn();

        $sql = "UPDATE eap_discentes
                SET ias = :data_ias
                WHERE id_discente = :id_discente";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':data_ias',    $dados['data_ias']);
        $stmt->bindValue(':id_discente', $dados['id_discente']);

        return $stmt->execute();
    }


    public static function atualizarNotaEad($id_discente, $nota, $conclusao)
    {
        $con = Connection::getConn();

        $sql = "UPDATE eap_discentes
                SET nota_ead = :nota,
                    conclusao_ead = :conclusao
                WHERE id_discente = :id_discente";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':nota', $nota);
        $stmt->bindValue(':conclusao', $conclusao);
        $stmt->bindValue(':id_discente', $id_discente);

        return $stmt->execute();
    }


    public static function eapAtivo($id_eap)
    {
        $con = Connection::getConn();
        $sql = "SELECT * FROM eap WHERE id_eap = :id_eap";
        $stmt = $con->prepare($sql);
        $stmt->bindValue('id_eap', $id_eap);
        $stmt->execute();
        return $stmt->fetch();
    }



}