<?php

class Usuario
{


    public static function listarUsuarios()
    {
        $con = Connection::getConn();

        $sql = "SELECT * 
                FROM eap_usuarios u
                LEFT JOIN  eap_perfil p
                ON u.cod_perfil = p.cod_perfil";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $usuarios = [];
    
        while ($row = $stmt->fetchObject()) {
            $usuarios[] = $row;
        }
    
        return $usuarios;
    }


    public function consultaUsuário()
    {

    }


    public static function incluirUsuario($dados)
    {
        
        $con = Connection::getConn();

        $sql = "INSERT INTO 
                    eap_usuarios (cpf, re, dg_re, pt_gr, nome, guerra, opm, codopm, email, telefone, funcao, nivel, cod_perfil)
               VALUES (:cpf, :re, :dg_re, :pt_gr, :nome, :guerra, :opm, :codopm, :email, :telefone, :funcao, :nivel, :cod_perfil)";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':cpf', $dados['cpf']);
        $stmt->bindValue(':re', $dados['re']);
        $stmt->bindValue(':dg_re', $dados['dgre']);
        $stmt->bindValue(':pt_gr', $dados['ptgr']);
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':guerra', $dados['guerra']);
        $stmt->bindValue(':opm', $dados['opm']);
        $stmt->bindValue(':codopm', $dados['codopm']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':telefone', $dados['telefone']);
        $stmt->bindValue(':funcao', $dados['funcao']);
        $stmt->bindValue(':cod_perfil', $dados['perfil']);
        $stmt->bindValue(':nivel', 0);

        return $stmt->execute();
        
    }


    public static function buscaUsuario($re)
    {
        $con = Connection::getConn();

     
        $sql = "SELECT * FROM eap_usuarios WHERE re = :re LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':re', $re);
        $stmt->execute();

        $resultado = $stmt->fetchObject();

        return $resultado;
    }


    public static function excluirUsuario($id_usuario)
    {
        $con = Connection::getConn();

        $sql = "DELETE FROM eap_usuarios WHERE id_usuario = :id_usuario";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario);

        return $stmt->execute();
    }


    public static function usuariosSistema()
    {
        $con = Connection::getConn();

        $sql = "SELECT count(*) FROM gt.eap_usuarios";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetchColumn(); // pega o valor da contagem

        return $resultado;
    }


    public static function perfis()
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM eap_perfil;";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public static function consultaPerfilUsuario($usuario)
    {
        $con = Connection::getConn();

        $sql = "SELECT * FROM eap_usuarios as u
                JOIN eap_perfil as p ON
                u.cod_perfil = p.cod_perfil
                WHERE cpf = :usuario";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();

        return $stmt->fetchObject();

    }

    public static function verificarUsuarioCadastrado($usuario, $senha)
    {
        $con = Connection::getConn();

        $sql = "SELECT * 
                FROM eap_usuarios as u
                JOIN eap_perfil as p 
                ON u.cod_perfil = p.cod_perfil
                WHERE cpf = :usuario";
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();

        $resultado = $stmt->fetchObject();

        if ($resultado) {
            // Gerar hash da senha informada
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Atualizar na tabela de usuários
            $sqlUpdate = "UPDATE eap_usuarios 
                            SET senha_hash = :senha_hash 
                        WHERE cpf = :usuario";
            $stmtUpdate = $con->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':senha_hash', $senha_hash);
            $stmtUpdate->bindValue(':usuario', $usuario);
            $stmtUpdate->execute();

            return true; // Senha gravada com sucesso
        }

        return false; // Usuário não encontrado
    }


}