<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include __DIR__ . '/../config/conexao.php';

// Verificando ação
$action = $_GET['action'] ?? '';

switch ($action) {

    // cadastro de usuário
    case 'cadastro':

        try {
            $nome     = $_POST['nome'];
            $email    = $_POST['email'];
            $senha    = $_POST['senha'];
            $telefone = $_POST['telefone'];
            $endereco = $_POST['endereco'];
            $cpf      = $_POST['cpf'];

            $sql = "INSERT INTO ClienteUsuario 
                    (nome, email, senha, telefone, endereco, cpf) 
                    VALUES
                    (:nome, :email, :senha, :telefone, :endereco, :cpf)";

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
            echo "<a href='../../Frontend/teste_usuario.html'>Voltar</a>";
            
        } catch (PDOException $e) {
            echo "❌ Erro ao cadastrar: " . $e->getMessage();
        }

    break;


    //  login de usuário
    case 'login':

        try {
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $sql = "SELECT * FROM ClienteUsuario WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);

            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['usuario_id'] = $user['id_usuario'];
                $_SESSION['usuario_nome'] = $user['nome'];

                echo "✅ Login realizado com sucesso, " . $user['nome'] . "!<br>";
                echo "<a href='../../Frontend/teste_usuario.html'>Voltar</a>";
            } else {
                echo "❌ Email ou senha inválidos.";
            }

        } catch (PDOException $e) {
            echo "❌ Erro no login: " . $e->getMessage();
        }

    break;


    // update de usuário
    case 'update':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Você precisa estar logado!";
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

            echo "✅ Perfil atualizado com sucesso!<br>";
            echo "<a href='../../Frontend/teste_usuario.html'>Voltar</a>";

        } catch (PDOException $e) {
            echo "❌ Erro na atualização: " . $e->getMessage();
        }

    break;


    // delete de usuário
    case 'delete':

        if (!isset($_SESSION['usuario_id'])) {
            echo "⚠️ Você precisa estar logado!";
            exit;
        }

        try {
            $id = $_SESSION['usuario_id'];

            $sql = "DELETE FROM ClienteUsuario WHERE id_usuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            session_destroy();

            echo "✅ Conta excluída com sucesso!";
            
        } catch (PDOException $e) {
            echo "❌ Erro ao excluir: " . $e->getMessage();
        }

    break;


    default:
        echo "⚠️ Ação inválida!";
}
