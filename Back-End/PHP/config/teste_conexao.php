<?php
require_once "conexao.php";

if ($pdo) {
    echo "✅ Conexão com o banco realizada com sucesso!";
} else {
    echo "❌ Erro na conexão.";
}
?>
