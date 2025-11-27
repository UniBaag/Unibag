<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    // ========================
    // CADASTRO DE MERCADO
    // ========================
    case 'cadastro':

        try {
            $nome         = $_POST['nome'] ?? '';
            $email        = $_POST['email'] ?? '';
            $senha        = $_POST['senha'] ?? '';
            $telefone     = $_POST['telefone'] ?? '';
            $endereco     = $_POST['endereco'] ?? '';
            $cnpj         = $_POST['cnpj'] ?? '';
            $tipo_mercado = $_POST['tipo_mercado'] ?? '';

            if (!$nome || !$email || !$senha) {
                echo "⚠️ Preencha os campos obrigatórios.";
                exit;
            }

            $check = $pdo->prepare("SELECT id_mercado FROM ClienteMercado WHERE email = :email");
            $check->execute([':email' => $email]);

            if ($check->rowCount() > 0) {
                echo "⚠️ Já existe um mercado com esse e-mail.";
                exit;
            }

            $sql = "INSERT INTO ClienteMercado
                    (nome, email, senha, telefone, endereco, cnpj, tipo_mercado, ativo) 
                    VALUES
                    (:nome, :email, :senha, :telefone, :endereco, :cnpj, :tipo_mercado, 1)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome'         => $nome,
                ':email'        => $email,
                ':senha'        => password_hash($senha, PASSWORD_DEFAULT),
                ':telefone'     => $telefone,
                ':endereco'     => $endereco,
                ':cnpj'         => $cnpj,
                ':tipo_mercado' => $tipo_mercado
            ]);

            $ultimoId = $pdo->lastInsertId();

            header("Location: /UNIBAG/Front-End/cadastro_admin.html?id_mercado=$ultimoId");
            exit;

        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar mercado: " . $e->getMessage();
        }

    break;


    // ========================
    // LOGIN DE MERCADO
    // ========================
    case 'login':

        try {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            $sql = "SELECT * FROM ClienteMercado 
                    WHERE email = :email AND ativo = 1 
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);

            $mercado = $stmt->fetch();

            if ($mercado && password_verify($senha, $mercado['senha'])) {
                $_SESSION['mercado_id']   = $mercado['id_mercado'];
                $_SESSION['mercado_nome'] = $mercado['nome'];

                echo "✅ Login realizado com sucesso, " . $mercado['nome'] . "!<br>";
                echo "<a href='../../Front-End/teste_mercado.html'>Voltar</a>";
            } else {
                echo "❌ Email, senha inválidos ou mercado desativado.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro no login: " . $e->getMessage();
        }

    break;


    // ========================
    // UPDATE DE MERCADO
    // ========================
    case 'update':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {
            $id           = $_SESSION['mercado_id'];
            $nome         = $_POST['nome'] ?? '';
            $email        = $_POST['email'] ?? '';
            $telefone     = $_POST['telefone'] ?? '';
            $endereco     = $_POST['endereco'] ?? '';
            $tipo_mercado = $_POST['tipo_mercado'] ?? '';

            $sql = "UPDATE ClienteMercado 
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone,
                        endereco = :endereco,
                        tipo_mercado = :tipo_mercado
                    WHERE id_mercado = :id AND ativo = 1";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome'         => $nome,
                ':email'        => $email,
                ':telefone'     => $telefone,
                ':endereco'     => $endereco,
                ':tipo_mercado' => $tipo_mercado,
                ':id'           => $id
            ]);

            echo "✅ Mercado atualizado com sucesso!<br>";
            echo "<a href='../../Front-End/teste_mercado.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro na atualização: " . $e->getMessage();
        }

    break;


    // ========================
    // DELETE (INATIVAR)
    // ========================
    case 'delete':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {
            $id = $_SESSION['mercado_id'];

            $sql = "UPDATE ClienteMercado SET ativo = 0 WHERE id_mercado = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            session_destroy();

            echo "✅ Mercado inativado com sucesso!";

        } catch (PDOException $e) {
            echo "❌ Erro ao excluir: " . $e->getMessage();
        }

    break;


    // ========================
    // CADASTRO DE ADMIN DO MERCADO
    // ========================
    case 'cadastroAdmin':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado como mercado!";
            exit;
        }

        try {
            $id_mercado = $_SESSION['mercado_id'];

            $check = $pdo->prepare("SELECT ativo FROM ClienteMercado WHERE id_mercado = :id");
            $check->execute([':id' => $id_mercado]);
            $mercado = $check->fetch();

            if (!$mercado || $mercado['ativo'] == 0) {
                echo "❌ Este mercado está desativado.";
                exit;
            }

            $nome  = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (!$nome || !$email || !$senha) {
                echo "⚠️ Preencha todos os campos.";
                exit;
            }

            $sql = "INSERT INTO AdministradorMercado
                    (nome, email, senha, id_mercado)
                    VALUES
                    (:nome, :email, :senha, :id_mercado)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome'       => $nome,
                ':email'      => $email,
                ':senha'      => password_hash($senha, PASSWORD_DEFAULT),
                ':id_mercado' => $id_mercado
            ]);

            echo "✅ Administrador cadastrado com sucesso!<br>";
            echo "<a href='../../Front-End/teste_mercado.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar administrador: " . $e->getMessage();
        }

    break;


    // ========================
    // LISTAR (SO PARA O MERCADO LOGADO)
    // ========================
    case 'listar':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado como mercado.";
            exit;
        }

        try {
            $id = $_SESSION['mercado_id'];

            $sql = "SELECT id_mercado, nome, email, telefone, endereco, cnpj, tipo_mercado, ativo
                    FROM ClienteMercado
                    WHERE id_mercado = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $mercados = $stmt->fetchAll();

            if ($mercados) {
                foreach ($mercados as $m) {

                    $status = $m['ativo'] == 1 ? '🟢 Ativo' : '🔴 Inativo';

                    echo "<strong>ID:</strong> {$m['id_mercado']} <br>";
                    echo "<strong>Nome:</strong> {$m['nome']} <br>";
                    echo "<strong>Email:</strong> {$m['email']} <br>";
                    echo "<strong>Telefone:</strong> {$m['telefone']} <br>";
                    echo "<strong>Endereço:</strong> {$m['endereco']} <br>";
                    echo "<strong>CNPJ:</strong> {$m['cnpj']} <br>";
                    echo "<strong>Tipo:</strong> {$m['tipo_mercado']} <br>";
                    echo "<strong>Status:</strong> {$status} <br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum mercado encontrado.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro ao listar: " . $e->getMessage();
        }

    break;


    // ========================
    // ✅ LISTAR MERCADOS (ADMIN PLATAFORMA)
    // ========================
    case 'listarAdminPlataforma':

        if (!isset($_SESSION['admin_plataforma'])) {
            echo "❌ Acesso negado.";
            exit;
        }

        try {
            $sql = "SELECT id_mercado, nome, email, telefone, endereco, cnpj, tipo_mercado, ativo
                    FROM ClienteMercado";

            $stmt = $pdo->query($sql);
            $mercados = $stmt->fetchAll();

            if ($mercados) {
                foreach ($mercados as $m) {

                    $status = $m['ativo'] == 1 ? '🟢 Ativo' : '🔴 Inativo';

                    echo "<strong>ID:</strong> {$m['id_mercado']} <br>";
                    echo "<strong>Nome:</strong> {$m['nome']} <br>";
                    echo "<strong>Email:</strong> {$m['email']} <br>";
                    echo "<strong>Telefone:</strong> {$m['telefone']} <br>";
                    echo "<strong>Endereço:</strong> {$m['endereco']} <br>";
                    echo "<strong>CNPJ:</strong> {$m['cnpj']} <br>";
                    echo "<strong>Tipo:</strong> {$m['tipo_mercado']} <br>";
                    echo "<strong>Status:</strong> {$status} <br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum mercado encontrado.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro ao listar mercados: " . $e->getMessage();
        }

    break;


    // ✅ LISTAR ADMINISTRADORES DO MERCADO
    case 'listarAdmin':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado como mercado!";
            exit;
        }

        try {
            $id = $_SESSION['mercado_id'];

            $sql = "SELECT id_admin, nome, email 
                    FROM AdministradorMercado 
                    WHERE id_mercado = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $admins = $stmt->fetchAll();

            if ($admins) {
                foreach ($admins as $a) {
                    echo "<strong>ID:</strong> {$a['id_admin']} <br>";
                    echo "<strong>Nome:</strong> {$a['nome']} <br>";
                    echo "<strong>Email:</strong> {$a['email']} <br>";
                    echo "<hr>";
                }
            } else {
                echo "⚠️ Nenhum administrador cadastrado para este mercado.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro ao listar administradores: " . $e->getMessage();
        }

    break;


    // ✅ LOGOUT
    case 'logout':
        session_destroy();
        echo "✅ Logout realizado com sucesso!";
    break;


    default:
        echo "⚠️ Ação inválida!";
}
?>
