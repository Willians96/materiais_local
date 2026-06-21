<?php

require_once __DIR__ . '/Env.php';
loadEnv(__DIR__ . '/../../.env');

// Definindo uma classe abstrata chamada Connection
abstract class Connection
{
    // Propriedade estática privada para armazenar a conexão com o banco de dados
    private static $conn;

    // Método estático público para obter a conexão com o banco de dados
    public static function getConn()
    {
        // Verifica se a conexão ainda não foi estabelecida
        if (self::$conn == null) {
            try {
                $host = env('DB_HOST', '127.0.0.1');
                $port = env('DB_PORT', '3306');
                $name = env('DB_NAME', 'materiais_local');
                $user = env('DB_USER', 'materiais');
                $pass = env('DB_PASS', '');

                // Cria uma nova conexão PDO com o banco de dados
                self::$conn = new PDO(
                    "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                    $user,
                    $pass
                );

                // Configurar PDO para lançar exceções em caso de erro
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Erro de conexão: ' . $e->getMessage());
            }
        }

        // Retorna a conexão com o banco de dados
        return self::$conn;
    }
}
?>
