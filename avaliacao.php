<?php

    require_once 'app/Core/Core.php';
    require_once 'lib/Database/Connection.php';

    require_once 'app/Controller/HomeController.php';
    require_once 'app/Controller/ErrorController.php';
    require_once 'app/Controller/PolicialController.php';
    require_once 'app/Controller/ComunicacaoSocialController.php';
    require_once 'app/Controller/IndexController.php';
    require_once 'app/Controller/AreaRestritaController.php';
    require_once 'app/Controller/IndicacaoController.php';
    require_once 'app/Controller/EapController.php';





    require_once 'vendor/autoload.php';


    session_start();

    //O método file_get_contents() é usado para ler o conteúdo de um arquivo em uma string
    $template = file_get_contents('app/Template/avaliacao.phtml');

    // Verifica se o nome do usuário está na sessão e define a área do usuário
    if (isset($_SESSION['nome'])) {
        $nome = $_SESSION['nome'];
        $area_usuario = "<span>$nome</span>";
    } else {
        $area_usuario = "<a href='signin'>Logar</a>"; // Corrigido: Fechamento da tag href
    }

    if (!isset($_SESSION['nome'])){
        header('Location: ./signin/');
    }

    // Inicia o buffer de saída
    ob_start();
        $core = new Core;
        $core->start($_GET);
        // Obtém o conteúdo do buffer de saída
        $saida = ob_get_contents();
    ob_end_clean();

    // Atribui valores das variáveis de sessão a variáveis locais
    $usuario = $_SESSION['usuario'] ?? null;
    $cpf = $_SESSION['cpf'] ?? null;
    $re = $_SESSION['re'] ?? null;
    $digre = $_SESSION['digre'] ?? null;
    $ptgr = $_SESSION['ptgr'] ?? null;
    $codptgr = $_SESSION['codptgr'] ?? null;
    $nome = $_SESSION['nome'] ?? null;
    $guerra = $_SESSION['guerra'] ?? null;
    $sexo = $_SESSION['sexo'] ?? null;
    $batalhao = $_SESSION['batalhao'] ?? null;
    $unidade = $_SESSION['unidade'] ?? null;
    $cmdo = $_SESSION['cmdo'] ?? null;
    $gcmdo = $_SESSION['gcmdo'] ?? null;
    $codopm = $_SESSION['codopm'] ?? null;
    $situacaoLegal = $_SESSION['situacaoLegal'] ?? null;
    $foto = $_SESSION['foto'] ?? null;
    $fotoBase = $_SESSION['fotoBase'] ?? null;
    $email = $_SESSION['email'] ?? null;
    $telefone = $_SESSION['telefone'] ?? null;
    $funcao = $_SESSION['funcao'] ?? null;
    $funcoes = $_SESSION['funcoes'] ?? null;
    $dataAdmissao = $_SESSION['dataAdmissao'] ?? null;
    $dataNascimento = $_SESSION['dataNascimento'] ?? null;
    $estadoCivil = $_SESSION['estadoCivil'] ?? null;

    // Define as variáveis e valores para substituição
    $variaveis = array(
        "{{usuario}}",
        "{{cpf}}",
        "{{re}}",
        "{{digre}}",
        "{{ptgr}}",
        "{{codptgr}}",
        "{{nome}}",
        "{{guerra}}",
        "{{sexo}}",
        "{{batalhao}}",
        "{{unidade}}",
        "{{cmdo}}",
        "{{gcmdo}}",
        "{{codopm}}",
        "{{situacaoLegal}}",
        "{{foto}}",
        "{{fotoBase}}",
        "{{email}}",
        "{{telefone}}",
        "{{funcao}}",
        "{{dataAdmissao}}",
        "{{dataNascimento}}",
        "{{estadoCivil}}",
        "{{area_dinamica}}",
    );

    $saidas = array(
        $usuario,
        $cpf,
        $re,
        $digre,
        $ptgr,
        $codptgr,
        $nome,
        $guerra,
        $sexo,
        $batalhao,
        $unidade,
        $cmdo,
        $gcmdo,
        $codopm,
        $situacaoLegal,
        $foto,
        $fotoBase,
        $email,
        $telefone,
        $funcao,
        $dataAdmissao,
        $dataNascimento,
        $estadoCivil,
        $saida // Adiciona a variável 'saida' ao final
    );

    // Substitui os placeholders no template com os valores correspondentes
    $tplpronto = str_replace($variaveis, $saidas, $template);

    // Exibe o template pronto
    echo $tplpronto;
