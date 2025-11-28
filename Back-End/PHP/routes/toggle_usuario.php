<?php
require_once __DIR__ . '/../config/conexao.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int) $_GET['id'];
    $status = (int) $_GET['status'];

    // Inverte o status
    $novoStatus = ($status === 1) ? 0 : 1;

    $sql = "UPDATE ClienteUsuario SET ativo = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $novoStatus, $id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Erro ao atualizar status.";
    }
}
