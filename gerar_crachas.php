<?php
require __DIR__ . '/libs/phpqrcode/qrlib.php';  // Ajuste o caminho aqui

// Pasta para salvar os QR Codes gerados
$dir = __DIR__ . '/qrcodes/';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

$totalCrachas = 50;

echo "<h2>Gerador de QR Codes para crachás</h2>";
echo "<p>Gerando {$totalCrachas} crachás...</p>";

for ($i = 1; $i <= $totalCrachas; $i++) {
    $codigo = str_pad($i, 2, '0', STR_PAD_LEFT);

    $qrContent = $codigo;

    $filename = $dir . "cracha_$codigo.png";

    QRcode::png($qrContent, $filename, QR_ECLEVEL_L, 6, 2);

    echo "Crachá $codigo gerado: <a href='qrcodes/cracha_$codigo.png' target='_blank'>Visualizar QR</a><br>";
}

echo "<p>Pronto! As imagens estão na pasta <code>qrcodes/</code></p>";
