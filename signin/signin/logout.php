<?php

    require_once '../app/Core/Config.php';
    
    // Configurar diretório de sessões
    AppConfig::configureSession(__DIR__ . '/..');
    
    session_start(); // garante que a sessão foi iniciada
    session_unset(); // limpa variáveis da sessão
    session_destroy(); // destrói a sessão

    header('Location: ' . AppConfig::url('signin/')); // volta pra tela de login
    exit;