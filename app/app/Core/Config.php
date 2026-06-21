<?php

/**
 * Configurações do sistema
 */
class AppConfig
{
    /**
     * Caminho base da aplicação
     * Deve ser ajustado conforme o ambiente de deploy
     */
    const BASE_PATH = '/new/';
    
    /**
     * Retorna o caminho base da aplicação
     * @return string
     */
    public static function getBasePath()
    {
        return self::BASE_PATH;
    }
    
    /**
     * Gera uma URL completa considerando o caminho base
     * @param string $path Caminho relativo (ex: '?pagina=home' ou 'signin/')
     * @return string URL completa
     */
    public static function url($path = '')
    {
        // Remove barra inicial se existir (exceto para caminhos que começam com ?)
        if (strpos($path, '?') === 0) {
            return self::BASE_PATH . ltrim($path, '/');
        }
        
        // Se o caminho já começa com /new/, retorna como está
        if (strpos($path, self::BASE_PATH) === 0) {
            return $path;
        }
        
        // Adiciona o caminho base
        return self::BASE_PATH . ltrim($path, '/');
    }
    
    /**
     * Gera uma URL para assets (CSS, JS, imagens)
     * @param string $assetPath Caminho do asset (ex: 'css/style.css')
     * @return string URL completa do asset
     */
    public static function asset($assetPath)
    {
        return self::BASE_PATH . 'public/' . ltrim($assetPath, '/');
    }
    
    /**
     * Configura o diretório de sessões do PHP
     * Cria um diretório dentro do projeto para armazenar as sessões
     * @param string $baseDir Diretório base do projeto (geralmente __DIR__ . '/..')
     */
    public static function configureSession($baseDir = null)
    {
        if ($baseDir === null) {
            // Tenta determinar o diretório base automaticamente
            $baseDir = dirname(__DIR__, 2); // Volta 2 níveis de app/Core/
        }
        
        $sessionDir = $baseDir . '/tmp/sessions';
        
        // Criar diretório se não existir
        if (!is_dir($sessionDir)) {
            @mkdir($sessionDir, 0777, true);
        }
        
        // Configurar o caminho de sessões se o diretório for gravável
        if (is_dir($sessionDir) && is_writable($sessionDir)) {
            ini_set('session.save_path', $sessionDir);
            return true;
        }
        
        return false;
    }
}

