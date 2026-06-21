<?php
    // Habilitar exibição de erros para debug (remover em produção)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('log_errors', 1);

    require_once 'app/Core/Core.php';
    require_once 'app/Core/Config.php';
    require_once 'lib/Database/Connection.php';

    require_once 'app/Controller/HomeController.php';
    require_once 'app/Controller/ErrorController.php';
    require_once 'app/Controller/PolicialController.php';
    require_once 'app/Controller/IndexController.php';
    require_once 'app/Controller/EapController.php';
    require_once 'app/Controller/TAFController.php';
    require_once 'app/Controller/TATcontroller.php';
    require_once 'app/Controller/UsuariosController.php';
    require_once 'app/Controller/LoginController.php';
    require_once 'app/Controller/ConsultasController.php';

    require_once 'app/Model/Turma.php';
    require_once 'app/Model/Discente.php';
    require_once 'app/Model/Usuario.php';
    require_once 'app/Model/Ead.php';
    require_once 'app/Model/Acessos.php';
    require_once 'app/Model/Config.php';

    require_once 'vendor/autoload.php';

    require_once 'lib/fpdf/fpdf.php';


    date_default_timezone_set('America/Sao_Paulo');

    // Configurar diretório de sessões
    AppConfig::configureSession(__DIR__);

    session_start();

    // Verifica se o usuário está logado ANTES de carregar o template
    if (!isset($_SESSION['nome'])){
        header('Location: ' . AppConfig::url('signin/'));
        exit;
    }

    //O método file_get_contents() é usado para ler o conteúdo de um arquivo em uma string
    $template = file_get_contents('app/Template/template.phtml');
    if ($template === false) {
        die("Erro ao carregar o template");
    }

    // Verifica se o nome do usuário está na sessão e define a área do usuário
    if (isset($_SESSION['nome'])) {
        $nome = $_SESSION['nome'];
        $area_usuario = "<span>$nome</span>";
    } else {
        $area_usuario = "<a href='" . AppConfig::url('signin/') . "'>Logar</a>"; // Corrigido: Fechamento da tag href
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


    if ($_SESSION['cod_perfil'] == 1 ) {
        $impersonate = '<form action="' . AppConfig::url('?pagina=login&metodo=impersonate') . '" method="post" class="impersonate">
                             <!-- Container que será mostrado/ocultado -->
                             <div class="row container__impersonate" id="container-impersonate">
                                 <div class="col-md-9">
                                     <input type="text" class="form-control number" maxlength="11" placeholder="RE ou CPF" name="re-impersonate">
                                 </div>
                                 <div class="col-md-1">
                                     <button class="btn btn-impersonate">
                                         <svg xmlns="http://www.w3.org/2000/svg" height="24px" fill="currentColor" viewBox="0 0 640 640"><!--!Font Awesome Free 7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                             <path d="M267 48C230.6 48 209.2 106.3 198.7 160L168 160C154.7 160 144 170.7 144 184C144 197.3 154.7 208 168 208L192 208L192 240C192 257 195.3 273.2 201.3 288L192 288L192 288L171.5 288C156.3 288 144 300.3 144 315.5C144 318.5 144.5 321.4 145.4 324.2L174.3 410.8C136.2 443.6 112 492.1 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 492.1 503.8 443.6 465.7 410.9L494.6 324.3C495.5 321.5 496 318.6 496 315.6C496 300.4 483.7 288.1 468.5 288.1L448 288.1L448 288.1L438.7 288.1C444.7 273.3 448 257.1 448 240.1L448 208.1L472 208.1C485.3 208.1 496 197.4 496 184.1C496 170.8 485.3 160.1 472 160.1L441.3 160.1C430.9 106.4 409.4 48.1 373 48.1C363.4 48.1 354 52 345.5 56.3C337.3 60.4 327.1 64.1 320 64.1C312.9 64.1 302.7 60.4 294.5 56.3C286 51.9 276.6 48 267 48zM360.7 532.4L335.9 461.5L363.8 429C366.5 425.8 368 421.8 368 417.6C368 407.9 360.2 400.1 350.5 400.1L289.5 400.1C279.8 400.1 272 407.9 272 417.6C272 421.8 273.5 425.8 276.2 429L304.1 461.5L279.3 532.4L222.3 352L258 352C276.4 362.2 297.5 368 320 368C342.5 368 363.6 362.2 382 352L417.7 352L360.7 532.4zM320 320C285.3 320 255.8 297.9 244.7 267C250.4 270.2 257 272 264 272L276.4 272C292.9 272 307.5 261.4 312.7 245.8C315 238.8 324.9 238.8 327.2 245.8C332.4 261.4 347.1 272 363.5 272L375.9 272C382.9 272 389.5 270.2 395.2 267C384.1 297.9 354.6 320 319.9 320z" />
                                         </svg>
                                     </button>
                                 </div>
                             </div>
     
                             <!-- Botão que ativa a área -->
                             <button type="button" class="btn btn-impersonate" id="btn-toggle-impersonate">
                                 <svg xmlns="http://www.w3.org/2000/svg" height="20px" fill="currentColor" viewBox="0 0 640 640"><!--!Font Awesome Free 7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                     <path d="M267 48C230.6 48 209.2 106.3 198.7 160L168 160C154.7 160 144 170.7 144 184C144 197.3 154.7 208 168 208L192 208L192 240C192 257 195.3 273.2 201.3 288L192 288L192 288L171.5 288C156.3 288 144 300.3 144 315.5C144 318.5 144.5 321.4 145.4 324.2L174.3 410.8C136.2 443.6 112 492.1 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 492.1 503.8 443.6 465.7 410.9L494.6 324.3C495.5 321.5 496 318.6 496 315.6C496 300.4 483.7 288.1 468.5 288.1L448 288.1L448 288.1L438.7 288.1C444.7 273.3 448 257.1 448 240.1L448 208.1L472 208.1C485.3 208.1 496 197.4 496 184.1C496 170.8 485.3 160.1 472 160.1L441.3 160.1C430.9 106.4 409.4 48.1 373 48.1C363.4 48.1 354 52 345.5 56.3C337.3 60.4 327.1 64.1 320 64.1C312.9 64.1 302.7 60.4 294.5 56.3C286 51.9 276.6 48 267 48zM360.7 532.4L335.9 461.5L363.8 429C366.5 425.8 368 421.8 368 417.6C368 407.9 360.2 400.1 350.5 400.1L289.5 400.1C279.8 400.1 272 407.9 272 417.6C272 421.8 273.5 425.8 276.2 429L304.1 461.5L279.3 532.4L222.3 352L258 352C276.4 362.2 297.5 368 320 368C342.5 368 363.6 362.2 382 352L417.7 352L360.7 532.4zM320 320C285.3 320 255.8 297.9 244.7 267C250.4 270.2 257 272 264 272L276.4 272C292.9 272 307.5 261.4 312.7 245.8C315 238.8 324.9 238.8 327.2 245.8C332.4 261.4 347.1 272 363.5 272L375.9 272C382.9 272 389.5 270.2 395.2 267C384.1 297.9 354.6 320 319.9 320z" />
                                 </svg>
                                 Impersonate
                             </button>
                         </form>'; 
    } else {
        $impersonate = '';
    }



    // Define variáveis de caminho base
    $base_path = AppConfig::getBasePath();
    $base_url = AppConfig::url('');

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
        "{{impersonate}}",
        "{{area_dinamica}}",
        "{{base_path}}",
        "{{base_url}}",
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
        $impersonate,
        $saida, // Adiciona a variável 'saida' ao final
        $base_path,
        $base_url,
    );

    // Substitui os placeholders no template com os valores correspondentes
    $tplpronto = str_replace($variaveis, $saidas, $template);

    // Exibe o template pronto
    echo $tplpronto;



    /**
     * Explicação detalhada:
     * 
     * 1. require_once: Inclui os arquivos necessários para o funcionamento do sistema, como classes Core, Model e Controllers.
     * 
     * 2. file_get_contents('app/Template/template.html'): Este comando lê o conteúdo do arquivo template.html que está localizado na pasta app/Template e guarda todo o conteúdo na variável $template.
     * 
     * 3. ob_start(): Inicia o buffer de saída. Isso significa que qualquer saída gerada a partir deste ponto não será enviada diretamente para o navegador ou cliente que fez a requisição HTTP, mas sim será armazenada internamente em um buffer.
     * 
     * 4. $core = new Core;: Cria uma nova instância da classe Core.
     * 
     * 5. $core->start($_GET): Chama o método start da instância $core, passando o array $_GET como argumento. O $_GET é uma superglobal do PHP que contém variáveis passadas para o script via parâmetros de URL (query string).
     * 
     * 6. ob_get_contents(): Obtém o conteúdo do buffer de saída atual, ou seja, tudo o que foi gerado desde o início do buffer até o momento antes desta chamada.
     * 
     * 7. ob_end_clean(): Limpa o buffer de saída e desativa o buffering de saída. Isso significa que o conteúdo que foi armazenado no buffer (com ob_start()) não será enviado para o navegador, mas sim armazenado na variável $saida.
     * 
     * 8. str_replace('{{area_dinamica}}', $saida, $template): Substitui a marcação {{area_dinamica}} no template pelo conteúdo dinâmico gerado e armazenado em $saida, resultando em $str.
     * 
     * 9. echo $str;: Exibe o resultado final no navegador, que é o conteúdo HTML completo com a área dinâmica preenchida.
     * 
     * Resumo:
     * 
     * O código apresentado carrega um template HTML, executa a lógica do sistema (representada pela classe Core e seus métodos) que gera conteúdo dinâmico baseado nos parâmetros recebidos via $_GET, e insere esse conteúdo dinâmico no template. Finalmente, o código exibe o HTML resultante para o usuário final.
     */