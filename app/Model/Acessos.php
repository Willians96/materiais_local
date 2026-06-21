<?php

class Acessos
{
    public static function gravarAcesso($dados)
    {
        $con = Connection::getConn();

        $sql = "INSERT INTO eap_acessos 
                   (usuario, re, opm, ip, acesso) 
                VALUES 
                   (:usuario, :re, :opm, :ip, :acesso)";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':usuario', $dados['usuario']);
        $stmt->bindValue(':re',      $dados['re']);
        $stmt->bindValue(':opm',     $dados['opm']);
        $stmt->bindValue(':ip',      $dados['ip']);
        $stmt->bindValue(':acesso',  date('Y-m-d H:i:s')); // grava o horário do acesso

        return $stmt->execute();
    }


    public static function getClientIp()
    {
        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Pode conter múltiplos IPs, pega o primeiro
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Corrigir caso seja IPv6 localhost
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        return trim($ip);
    }

}
