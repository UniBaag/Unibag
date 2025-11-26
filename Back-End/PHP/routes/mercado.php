<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    // cadastro de mercado
    case 'cadastro':

        try {
            $nome         = $_POST['nome'];
            $email        = $_POST['email'];
            $senha        = $_POST['senha'];
            $telefone     = $_POST['telefone'];
            $endereco     = $_POST['endereco'];
            $cnpj         = $_POST['cnpj'];
            $tipo_mercado = $_POST['tipo_mercado']; // atacado ou varejo

            $sql = "INSERT INTO ClienteMercado
                    (nome, email, senha, telefone, endereco, cnpj, tipo_mercado) 
                    VALUES
                    (:nome, :email, :senha, :telefone, :endereco, :cnpj, :tipo_mercado)";

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


    // login de mercado
    case 'login':

        try {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $sql = "SELECT * FROM ClienteMercado WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);

            $mercado = $stmt->fetch();

            if ($mercado && password_verify($senha, $mercado['senha'])) {
                $_SESSION['mercado_id']   = $mercado['id_mercado'];
                $_SESSION['mercado_nome'] = $mercado['nome'];

                echo "✅ Login realizado com sucesso, " . $mercado['nome'] . "!<br>";
                echo "<a href='../../Frontend/teste_mercado.html'>Voltar</a>";
            } else {
                echo "❌ Email ou senha inválidos.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro no login: " . $e->getMessage();
        }

    break;


    // update de mercado
    case 'update':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {
            $id           = $_SESSION['mercado_id'];
            $nome         = $_POST['nome'];
            $email        = $_POST['email'];
            $telefone     = $_POST['telefone'];
            $endereco     = $_POST['endereco'];
            $tipo_mercado = $_POST['tipo_mercado'];

            $sql = "UPDATE ClienteMercado 
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone,
                        endereco = :endereco,
                        tipo_mercado = :tipo_mercado
                    WHERE id_mercado = :id";

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
            echo "<a href='../../Frontend/teste_mercado.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro na atualização: " . $e->getMessage();
        }

    break;


    // delete de mercado
    case 'delete':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {
            $id = $_SESSION['mercado_id'];

            $sql = "DELETE FROM ClienteMercado WHERE id_mercado = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            session_destroy();

            echo "✅ Mercado excluído com sucesso!";

        } catch (PDOException $e) {
            echo "❌ Erro ao excluir: " . $e->getMessage();
        }

    break;


    // erro de ação inválida
    case 'cadastroAdmin':

        if (!isset($_SESSION['mercado_id'])) {
            echo "⚠️ Você precisa estar logado como mercado!";
            exit;
        }

        try {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $id_mercado = $_SESSION['mercado_id'];

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
            echo "<a href='../../Frontend/teste_mercado.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar administrador: " . $e->getMessage();
        }

    break;


    default:
        echo "⚠️ Ação inválida!";
}
