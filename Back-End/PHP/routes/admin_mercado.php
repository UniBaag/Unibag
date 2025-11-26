<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

// Verificando ação
$action = $_GET['action'] ?? '';

switch ($action) {

    // ==============================
    // CADASTRO DE ADMINISTRADOR
    // ==============================
    case 'cadastro':

        try {
            $nome       = $_POST['nome'];
            $email      = $_POST['email'];
            $senha      = $_POST['senha'];
            $id_mercado = $_POST['id_mercado'];

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
            echo "<a href='/UNIBAG/Front-End/teste_mercado.html'>Voltar para o início</a>";

        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar administrador: " . $e->getMessage();
        }

    break;


    // ==============================
    // LOGIN DE ADMINISTRADOR
    // ==============================
    case 'login':

        try {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $sql = "SELECT * FROM AdministradorMercado WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);

            $admin = $stmt->fetch();

            if ($admin && password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_id']       = $admin['id_admin'];
                $_SESSION['admin_nome']     = $admin['nome'];
                $_SESSION['admin_mercado']  = $admin['id_mercado'];

                echo "✅ Login realizado com sucesso, " . $admin['nome'] . "!<br>";
                echo "<a href='/UNIBAG/Front-End/teste_mercado.html'>Ir para painel</a>";
            } else {
                echo "❌ Email ou senha inválidos.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro no login: " . $e->getMessage();
        }

    break;


    // ==============================
    // UPDATE DE ADMINISTRADOR
    // ==============================
    case 'update':

        if (!isset($_SESSION['admin_id'])) {
            echo "⚠️ Você precisa estar logado como administrador!";
            exit;
        }

        try {
            $id    = $_SESSION['admin_id'];
            $nome  = $_POST['nome'];
            $email = $_POST['email'];

            $sql = "UPDATE AdministradorMercado
                    SET nome = :nome,
                        email = :email
                    WHERE id_admin = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome'  => $nome,
                ':email' => $email,
                ':id'    => $id
            ]);

            echo "✅ Administrador atualizado com sucesso!";

        } catch (PDOException $e) {
            echo "❌ Erro na atualização: " . $e->getMessage();
        }

    break;


    // ==============================
    // DELETE DE ADMINISTRADOR
    // ==============================
    case 'delete':

        if (!isset($_SESSION['admin_id'])) {
            echo "⚠️ Você precisa estar logado como administrador!";
            exit;
        }

        try {
            $id = $_SESSION['admin_id'];

            $sql = "DELETE FROM AdministradorMercado WHERE id_admin = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            session_destroy();

            echo "✅ Administrador removido com sucesso!";

        } catch (PDOException $e) {
            echo "❌ Erro ao excluir: " . $e->getMessage();
        }

    break;


    default:
        echo "⚠️ Ação inválida!";
}
