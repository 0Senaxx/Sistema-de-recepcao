<?php
include '../../conexao.php';

if (isset($_GET['cpf'])) {
    $cpf = $_GET['cpf'];

    $sql = "SELECT * FROM visitantes WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $visitante = $result->fetch_assoc();
        echo json_encode([
            'encontrado' => true,
            'nome' => $visitante['nome'],
            'social' => $visitante['social'],
            'orgao' => $visitante['orgao'],
            'foto' => $visitante['foto'] ? '../../Imagens/ImgVisitante/' . basename($visitante['foto']) : ''
        ]);
    } else {
        echo json_encode(['encontrado' => false]);
    }
} else {
    echo json_encode(['encontrado' => false]);
}
