<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';

// ============================
// LOGIN FIXO ADMIN PLATAFORMA
// ============================
if ($action === 'login') {

    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($email === 'admin@unibag.com' && $senha === '123456') {
        $_SESSION['admin_plataforma'] = true;

        header("Location: /UNIBAG/Front-End/src/pages/AdminPlataforma/index.php");
        exit;
    } else {
        echo "❌ Credenciais inválidas.";
    }

    exit;
}

// ============================
// PROTEÇÃO DAS ROTAS
// ============================
if (!isset($_SESSION['admin_plataforma'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// ============================
// AÇÕES
// ============================
switch ($action) {

    // ==========================
    // DADOS DO DASHBOARD (CARDS)
    // ==========================
    case 'dashboard':

        $data = [
            "usuarios" => $pdo->query("SELECT COUNT(*) FROM ClienteUsuario")->fetchColumn(),
            "mercados" => $pdo->query("SELECT COUNT(*) FROM ClienteMercado")->fetchColumn(),
            "entregadores" => $pdo->query("SELECT COUNT(*) FROM ClienteEntregador")->fetchColumn(),
        ];

        echo json_encode($data);
    break;

    // ======================
    // LISTAR USUÁRIOS
    // ======================
    case 'listarUsuarios':

        $sql = "SELECT id_usuario, nome, email, telefone, cpf, ativo 
                FROM ClienteUsuario
                ORDER BY id_usuario DESC";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    break;

    // ======================
    // LISTAR MERCADOS
    // ======================
    case 'listarMercados':

        $sql = "SELECT id_mercado, nome, email, telefone, ativo 
                FROM ClienteMercado
                ORDER BY id_mercado DESC";

        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    break;

    // ======================
    // DELETAR (INATIVAR) USUÁRIO
    // ======================
    case 'deletarUsuario':

        $id = $_POST['id'] ?? '';

        if (!$id) {
            echo json_encode(["erro" => "ID não informado"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE ClienteUsuario SET ativo = 0 WHERE id_usuario = ?");
        $stmt->execute([$id]);

        echo json_encode(["sucesso" => "Usuário inativado"]);
    break;

    // ======================
    // DELETAR (INATIVAR) MERCADO
    // ======================
    case 'deletarMercado':

        $id = $_POST['id'] ?? '';

        if (!$id) {
            echo json_encode(["erro" => "ID não informado"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE ClienteMercado SET ativo = 0 WHERE id_mercado = ?");
        $stmt->execute([$id]);

        echo json_encode(["sucesso" => "Mercado inativado"]);
    break;

    // ======================
    // LOGOUT
    // ======================
    case 'logout':

        session_destroy();
        echo json_encode(["sucesso" => "Logout realizado"]);
    break;

    default:
        echo json_encode(["erro" => "Ação inválida"]);
}
?>