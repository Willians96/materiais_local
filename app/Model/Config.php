<?php


class Config
{


    public static function configuracoes()
    {
        $con = Connection::getConn();
        $sql = "SELECT * FROM eap_config LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

}