<?php
// config/conexao.php

// 0. CARREGA O .ENV (na raiz do projeto)
$env = parse_ini_file(__DIR__ . '/../../../.env');

// 1. DADOS DE CONEXÃO (agora vindo do .env)
$host    = $env['DB_HOST'];
$port    = $env['DB_PORT'];
$db_name = $env['DB_NAME'];
$user    = $env['DB_USER'];
$pass    = $env['DB_PASSWORD'];
$charset = 'utf8mb4';

// 2. CONFIGURAÇÃO SSL (OBRIGATÓRIO PARA AIVEN)
$ssl_ca_path = dirname(__FILE__) . '/ca.pem'; 

// 3. DEFINIÇÃO DA STRING DSN
$dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$charset";

// 4. OPÇÕES PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,

    PDO::MYSQL_ATTR_SSL_CA       => $ssl_ca_path,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

// 5. TENTATIVA DE CONEXÃO
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // echo "Conexão Aiven bem-sucedida!";
} catch (\PDOException $e) {
    echo "<strong>Erro de conexão:</strong> " . $e->getMessage();
    exit;
}
?>
