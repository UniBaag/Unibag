<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';

/*
    LOGIN FIXO DO ADMIN DA PLATAFORMA
    EMAIL: admin@unibag.com
    SENHA: 123456
*/

switch ($action) {

    // ======================
    // LOGIN ADMIN PLATAFORMA
    // ======================
    case 'login':

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if ($email === 'admin@unibag.com' && $senha === '123456') {
            $_SESSION['admin_plataforma'] = true;
            echo "✅ Bem-vindo, Administrador da Plataforma!<br><br>";
            echo "<a href='?action=dashboard'>Ir para o Painel</a>";
        } else {
            echo "❌ Credenciais inválidas.";
        }

    break;


    // ======================
    // DASHBOARD COM ATIVOS E INATIVOS
    // ======================
    case 'dashboard':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        // CONTADORES (ATIVOS e INATIVOS)
        $totalUsuariosAtivos     = $pdo->query("SELECT COUNT(*) FROM ClienteUsuario WHERE ativo = 1")->fetchColumn();
        $totalUsuariosInativos   = $pdo->query("SELECT COUNT(*) FROM ClienteUsuario WHERE ativo = 0")->fetchColumn();

        $totalMercadosAtivos     = $pdo->query("SELECT COUNT(*) FROM ClienteMercado WHERE ativo = 1")->fetchColumn();
        $totalMercadosInativos   = $pdo->query("SELECT COUNT(*) FROM ClienteMercado WHERE ativo = 0")->fetchColumn();

        $totalEntregadoresAtivos   = $pdo->query("SELECT COUNT(*) FROM ClienteEntregador WHERE ativo = 1")->fetchColumn();
        $totalEntregadoresInativos = $pdo->query("SELECT COUNT(*) FROM ClienteEntregador WHERE ativo = 0")->fetchColumn();

        echo "<h2>📊 Painel do Administrador - UNIBAG</h2>";

        echo "
        <div>
            👤 Usuários: <strong>$totalUsuariosAtivos ativos</strong> | $totalUsuariosInativos inativos<br>
            <a href='?action=listarUsuarios'>Ver usuários</a>
        </div>
        <br>

        <div>
            🏪 Mercados: <strong>$totalMercadosAtivos ativos</strong> | $totalMercadosInativos inativos<br>
            <a href='?action=listarMercados'>Ver mercados</a>
        </div>
        <br>

        <div>
            🚚 Entregadores: <strong>$totalEntregadoresAtivos ativos</strong> | $totalEntregadoresInativos inativos<br>
            <a href='?action=listarEntregadores'>Ver entregadores</a>
        </div>

        <br>
        <a href='?action=logout'>🚪 Logout</a>
        ";

    break;


    // ======================
    // LISTAR USUÁRIOS
    // ======================
    case 'listarUsuarios':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        try {
            $sql = "SELECT id_usuario, nome, email, telefone, ativo FROM ClienteUsuario";
            $stmt = $pdo->query($sql);
            $usuarios = $stmt->fetchAll();

            if ($usuarios) {
                echo "<h2>📌 Usuários Cadastrados</h2>";
                foreach ($usuarios as $u) {

                    $status = $u['ativo'] == 1 ? '✅ Ativo' : '❌ Inativo';

                    echo "<strong>ID:</strong> {$u['id_usuario']}<br>";
                    echo "<strong>Nome:</strong> {$u['nome']}<br>";
                    echo "<strong>Email:</strong> {$u['email']}<br>";
                    echo "<strong>Telefone:</strong> {$u['telefone']}<br>";
                    echo "<strong>Status:</strong> $status<br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum usuário encontrado.";
            }

            echo "<br><a href='?action=dashboard'>⬅️ Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================
    // LISTAR MERCADOS
    // ======================
    case 'listarMercados':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        try {
            $sql = "SELECT id_mercado, nome, email, telefone, ativo 
                    FROM ClienteMercado";

            $stmt = $pdo->query($sql);
            $mercados = $stmt->fetchAll();

            if ($mercados) {
                echo "<h2>🏪 Mercados Cadastrados</h2>";
                foreach ($mercados as $m) {

                    $status = $m['ativo'] == 1 ? '✅ Ativo' : '❌ Inativo';

                    echo "<strong>ID:</strong> {$m['id_mercado']}<br>";
                    echo "<strong>Nome:</strong> {$m['nome']}<br>";
                    echo "<strong>Email:</strong> {$m['email']}<br>";
                    echo "<strong>Telefone:</strong> {$m['telefone']}<br>";
                    echo "<strong>Status:</strong> $status<br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum mercado encontrado.";
            }

            echo "<br><a href='?action=dashboard'>⬅️ Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================
    // LISTAR ENTREGADORES
    // ======================
    case 'listarEntregadores':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        try {
            $sql = "SELECT id_entregador, nome, email, telefone, veiculo, ativo 
                    FROM ClienteEntregador";

            $stmt = $pdo->query($sql);
            $entregadores = $stmt->fetchAll();

            if ($entregadores) {
                echo "<h2>🚚 Entregadores Cadastrados</h2>";
                foreach ($entregadores as $e) {

                    $status = $e['ativo'] == 1 ? '✅ Ativo' : '❌ Inativo';

                    echo "<strong>ID:</strong> {$e['id_entregador']}<br>";
                    echo "<strong>Nome:</strong> {$e['nome']}<br>";
                    echo "<strong>Email:</strong> {$e['email']}<br>";
                    echo "<strong>Telefone:</strong> {$e['telefone']}<br>";
                    echo "<strong>Veículo:</strong> {$e['veiculo']}<br>";
                    echo "<strong>Status:</strong> $status<br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum entregador encontrado.";
            }

            echo "<br><a href='?action=dashboard'>⬅️ Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================
    // LOGOUT
    // ======================
    case 'logout':

        session_destroy();
        echo "✅ Logout realizado com sucesso.";

    break;


    default:
        echo "⚠️ Ação inválida.";
}
?>
