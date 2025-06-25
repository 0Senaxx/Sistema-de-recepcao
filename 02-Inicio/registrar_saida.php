<?php
include '../conexao.php';
date_default_timezone_set('America/Manaus');

$saida = date('H:i:s');

if (!empty($_POST['cracha_id'])) {
    $cracha_id = $_POST['cracha_id'];

    // 1️⃣ Verifica se existe visita aberta para o crachá
    $sql = "SELECT id FROM visitas WHERE cracha_id = ? AND saida IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cracha_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $visita = $result->fetch_assoc();

    if ($visita) {
        $visita_id = $visita['id'];

        // 2️⃣ Registrar a saída
        $sqlUpdate = "UPDATE visitas SET saida = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $saida, $visita_id);
        $stmtUpdate->execute();

        // 3️⃣ Tornar o crachá disponível novamente
        $sqlCracha = "UPDATE crachas SET status = 'disponivel' WHERE id = ?";
        $stmtCracha = $conn->prepare($sqlCracha);
        $stmtCracha->bind_param("i", $cracha_id);
        $stmtCracha->execute();

        echo "OK"; // ✅ Resposta para o JS
    } else {
        echo "JA_REGISTRADO"; // ⚡️ Já registrado ou não encontrado
    }
} else {
    echo "ERRO"; // ⚠️ Nenhum crachá enviado
}
