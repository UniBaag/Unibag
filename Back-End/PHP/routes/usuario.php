<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    // ======================================================
    // ✅ CADASTRO DE USUÁRIO
    // ======================================================
    case 'cadastro':

        if (
            empty($_POST['nome']) ||
            empty($_POST['email']) ||
            empty($_POST['senha']) ||
            empty($_POST['telefone']) ||
            empty($_POST['endereco']) ||
            empty($_POST['cpf'])
        ) {
            echo "⚠️ Preencha todos os campos obrigatórios!";
            exit;
        }

        try {

            $nome     = $_POST['nome'];
            $email    = $_POST['email'];
            $senha    = $_POST['senha'];
            $telefone = $_POST['telefone'];
            $endereco = $_POST['endereco'];
            $cpf      = $_POST['cpf'];

            // Verifica se já existe
            $check = $pdo->prepare("SELECT id_usuario FROM ClienteUsuario WHERE email = :email");
            $check->execute([':email' => $email]);

            if ($check->rowCount() > 0) {
                echo "⚠️ Esse e-mail já está cadastrado!";
                exit;
            }

            $sql = "INSERT INTO ClienteUsuario 
                    (nome, email, senha, telefone, endereco, cpf, ativo) 
                    VALUES
                    (:nome, :email, :senha, :telefone, :endereco, :cpf, 1)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome'     => $nome,
                ':email'    => $email,
                ':senha'    => password_hash($senha, PASSWORD_DEFAULT),
                ':telefone' => $telefone,
                ':endereco' => $endereco,
                ':cpf'      => $cpf
            ]);

            echo "✅ Usuário cadastrado com sucesso!<br>";
            echo "<a href='../../Front-End/teste_usuario.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ LOGIN DE USUÁRIO + ADMIN DA PLATAFORMA
    // ======================================================
    case 'login':

        if (empty($_POST['email']) || empty($_POST['senha'])) {
            echo "⚠️ Informe o e-mail e a senha.";
            exit;
        }

        try {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            // ✅ LOGIN ADMIN PLATAFORMA
            if ($email === 'admin@unibag.com' && $senha === 'unibag123') {
                $_SESSION['admin_plataforma'] = true;
                header("Location: /UNIBAG/Front-End/src/pages/AdminPlataforma/index.php");
                exit;
            }

            $sql = "SELECT * 
                    FROM ClienteUsuario 
                    WHERE email = :email 
                    AND ativo = 1
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {

                $_SESSION['usuario_id']   = $user['id_usuario'];
                $_SESSION['usuario_nome'] = $user['nome'];

                echo "✅ Login realizado com sucesso, {$user['nome']}!<br><br>";
                echo "<a href='?action=dashboard'>Ir para o painel</a>";

            } else {
                echo "❌ Email, senha inválidos ou conta inativa.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro no login: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ DASHBOARD DO USUÁRIO
    // ======================================================
    case 'dashboard':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Faça login primeiro.";
            exit;
        }

        echo "<h2>👤 Painel do Usuário - UNIBAG</h2>";
        echo "<p>Bem-vindo, <strong>{$_SESSION['usuario_nome']}</strong></p>";

        echo "
        <a href='?action=meusDados'>📄 Meus dados</a><br><br>
        <a href='?action=logout'>🚪 Logout</a>
        ";

    break;


    // ======================================================
    // ✅ MEUS DADOS
    // ======================================================
    case 'meusDados':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        try {

            $id = $_SESSION['usuario_id'];

            $sql = "SELECT nome, email, telefone, endereco, cpf, ativo 
                    FROM ClienteUsuario 
                    WHERE id_usuario = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id ]);
            $user = $stmt->fetch();

            if ($user) {

                $status = $user['ativo'] == 1 ? '✅ Ativo' : '❌ Inativo';

                echo "<h2>📄 Meus Dados</h2>";
                echo "<strong>Nome:</strong> {$user['nome']}<br>";
                echo "<strong>Email:</strong> {$user['email']}<br>";
                echo "<strong>Telefone:</strong> {$user['telefone']}<br>";
                echo "<strong>Endereço:</strong> {$user['endereco']}<br>";
                echo "<strong>CPF:</strong> {$user['cpf']}<br>";
                echo "<strong>Status:</strong> $status<br><br>";
            }

            echo "<a href='?action=dashboard'>⬅️ Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ ATUALIZAR USUÁRIO
    // ======================================================
    case 'update':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        if (
            empty($_POST['nome']) ||
            empty($_POST['email']) ||
            empty($_POST['telefone']) ||
            empty($_POST['endereco'])
        ) {
            echo "⚠️ Preencha todos os campos!";
            exit;
        }

        try {

            $id       = $_SESSION['usuario_id'];
            $nome     = $_POST['nome'];
            $email    = $_POST['email'];
            $telefone = $_POST['telefone'];
            $endereco = $_POST['endereco'];

            $sql = "UPDATE ClienteUsuario 
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone,
                        endereco = :endereco
                    WHERE id_usuario = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome'     => $nome,
                ':email'    => $email,
                ':telefone' => $telefone,
                ':endereco' => $endereco,
                ':id'       => $id
            ]);

            echo "✅ Dados atualizados com sucesso!<br>";
            echo "<a href='?action=dashboard'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ EXCLUIR (INATIVAR)
    // ======================================================
    case 'delete':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {

            $id = $_SESSION['usuario_id'];

            $sql = "UPDATE ClienteUsuario 
                    SET ativo = 0 
                    WHERE id_usuario = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            session_destroy();

            echo "✅ Conta desativada com sucesso!";

        } catch (PDOException $e) {
            echo "❌ Erro: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ LISTAR USUÁRIOS (ADMIN DA PLATAFORMA)
    // ======================================================
    case 'listar':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "⚠️ Acesso negado.";
            exit;
        }

        try {

            $sql = "SELECT id_usuario, nome, email, telefone, cpf, ativo 
                    FROM ClienteUsuario
                    ORDER BY id_usuario DESC";

            $stmt = $pdo->query($sql);

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($usuarios) {
                header('Content-Type: application/json');
                echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["mensagem" => "Nenhum usuário encontrado"]);
            }

        } catch (PDOException $e) {
            echo "❌ Erro ao listar: " . $e->getMessage();
        }

    break;


    // ======================================================
    // ✅ LOGOUT
    // ======================================================
    case 'logout':

        session_destroy();
        echo "✅ Logout realizado com sucesso.";

    break;


    default:
        echo "⚠️ Ação inválida!";
}
?>
