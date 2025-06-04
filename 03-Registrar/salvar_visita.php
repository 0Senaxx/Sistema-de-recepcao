<?php
include '../conexao.php';

// Função para salvar foto (base64 ou arquivo)
function salvarFoto() {
    // Se vier via base64 (webcam)
    if (!empty($_POST['foto_base64'])) {
        $foto_base64 = str_replace('data:image/jpeg;base64,', '', $_POST['foto_base64']);
        $foto_base64 = str_replace(' ', '+', $foto_base64);
        $foto_data = base64_decode($foto_base64);

        $pasta = "../04-Visitantes/Fotos/";
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);

        $nome_arquivo = $pasta . uniqid() . '.jpg';
        file_put_contents($nome_arquivo, $foto_data);

        return $nome_arquivo;
    }

    return null; // Nenhuma foto fornecida
}

// === DADOS DO FORMULÁRIO === //
$cpf      = $_POST['cpf'];
$nome     = $_POST['nome'];
$telefone = $_POST['telefone'];
$orgao    = $_POST['orgao'];
$data     = $_POST['data'];
$hora     = $_POST['hora'];
$servidor = $_POST['servidor'];
$setor    = $_POST['setor'];
$foto     = salvarFoto();

// === VERIFICA SE O VISITANTE JÁ EXISTE PELO CPF === //
$sql = "SELECT id FROM visitantes WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Visitante já existe, obter ID
    $row = $result->fetch_assoc();
    $visitante_id = $row['id'];

    // Atualiza dados, com ou sem foto nova
    if ($foto) {
        $sqlUpdate = "UPDATE visitantes SET nome = ?, telefone = ?, orgao = ?, foto = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssi", $nome, $telefone, $orgao, $foto, $visitante_id);
    } else {
        $sqlUpdate = "UPDATE visitantes SET nome = ?, telefone = ?, orgao = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sssi", $nome, $telefone, $orgao, $visitante_id);
    }

    $stmtUpdate->execute();

} else {
    // Visitante novo: inserir
    $sqlInsert = "INSERT INTO visitantes (cpf, nome, telefone, orgao, foto) VALUES (?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sssss", $cpf, $nome, $telefone, $orgao, $foto);
    $stmtInsert->execute();

    $visitante_id = $stmtInsert->insert_id;
}

// === REGISTRA A VISITA === //


$setor = intval($_POST['setor_id']);

// Exemplo do insert na tabela visitas
$sqlVisita = "INSERT INTO visitas (visitante_id, data, hora, servidor, setor) VALUES (?, ?, ?, ?, ?)";
$stmtVisita = $conn->prepare($sqlVisita);
$stmtVisita->bind_param("isssi", $visitante_id, $data, $hora, $servidor, $setor);
$stmtVisita->execute();


// === REDIRECIONA PARA A PÁGINA INICIAL === //
header("Location: ../02-Inicio/index.php");
exit();
?>
