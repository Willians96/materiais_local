<?php

class LoginController
{


    public function impersonate()
    {

        $re = $_POST['re-impersonate'];


        if(strlen($re) == 6 or strlen($re) == 7) {
            //re
            $dados = PolicialController::buscaPMporRE($re);
        }  else if(strlen($re) == 11) {
            //cpf
            $dados = PolicialController::buscaPMporCPF($re);
        } else {
            //RE inválido
        }


        $erroCodigo = $dados->erroCodigo;

        if ($erroCodigo == -1) {
            //return false;
            
            
        } else {
            // Procura foto
            $foto = PolicialController::procuraFoto($dados->numeroREPM);
            $fotoBase = 'data:image/png;base64,' . $foto;

            // Verifica se existe um e-mail que termina com @gmail.com
            $emailFuncional = PolicialController::procuraEmailFuncional($dados);

            // Verifica se existe um celular
            $telefone = PolicialController::procuraCelular($dados);

            // Busca função ativa principal
            $funcao = PolicialController::procuraFuncao($dados);

            // Busca funções ativas
            $funcoes[] = PolicialController::procuraFuncoes($dados);

            //procurar GCMDO
            //$gcmdo = self::procuraGCMDO(substr($dados->codigoOPMAtualPM->codigoOPM, 0, 5));
            $gcmdo = '';

            // Busca documentos
            $documentos = PolicialController::buscaDocumentos($dados);

             // Busca medalhas
             $medalhas = PolicialController::buscaMedalhas(trim($dados->Documentos->FuncionarioDocumento[0]->Numero . $dados->Documentos->FuncionarioDocumento[0]->DigitoDocumento));

            // Cria instância do Policial
            $policial = new Policial($dados, $emailFuncional, $telefone, $funcao, $funcoes, $foto, $fotoBase, $gcmdo, $documentos, $medalhas);

            // Retorna o objeto Policial
            //return $policial;
        }

        self::criarSessaoUsuario($policial);
        $_SESSION['impersonate'] = true;
    }


    public function criarSessaoUsuario($usuario)
    {
        // Atribuindo valores às variáveis de sessão
        $_SESSION['usuario'] = $usuario->cpf;
        $_SESSION['cpf'] = $usuario->cpf;
        $_SESSION['re'] = $usuario->re;
        $_SESSION['digre'] = $usuario->digre;
        $_SESSION['ptgr'] = $usuario->ptgr;
        $_SESSION['codptgr'] = $usuario->codPtgr;
        $_SESSION['nome'] = $usuario->nome;
        $_SESSION['guerra'] = $usuario->guerra;
        $_SESSION['sexo'] = $usuario->sexo;
        $_SESSION['batalhao'] = $usuario->unidade;
        $_SESSION['unidade'] = $usuario->unidade;
        $_SESSION['cmdo'] = $usuario->cmdo;
        $_SESSION['gcmdo'] = $usuario->gcmdo;
        $_SESSION['codopm'] = $usuario->codopm;
        $_SESSION['situacaoLegal'] = $usuario->situacaoLegal;
        $_SESSION['foto'] = $usuario->foto;
        $_SESSION['fotoBase'] = $usuario->fotoBase;
        $_SESSION['email'] = $usuario->email;
        $_SESSION['telefone'] = $usuario->telefone;
        $_SESSION['funcao'] = $usuario->funcao;
        $_SESSION['funcoes'] = $usuario->funcoes;
        $_SESSION['dataAdmissao'] = $usuario->dataAdmissao;
        $_SESSION['dataNascimento'] = $usuario->dataNascimento;
        $_SESSION['estadoCivil'] = $usuario->estadoCivil;

        //consulta do perfil do usuário
        $perfil = Usuario::consultaPerfilUsuario($usuario->cpf);
        $_SESSION['cod_perfil'] = $perfil->cod_perfil??100;
        $_SESSION['perfil'] = $perfil->perfil??'Visitante';

        $_SESSION['impersonate'] = false;

    }

}