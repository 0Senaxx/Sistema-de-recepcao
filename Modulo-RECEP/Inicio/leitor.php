<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registrar Saída</title>
  <link rel="stylesheet" href="qrcode.css">
</head>

<body>
  <header class="cabecalho">
    <h1>Recepção SEAD</h1>
  </header>

  <main>
    <section class="card">

      <h1>Registrar Saída</h1><br>
      <p>Escaneie o QR Code no verso do crachá para registrar a saída do visitante.</p><br>

      <div id="video-container">
        <video id="video" autoplay playsinline></video>

        <div id="checkmark" style="display:none; position:absolute; top:50%; left:50%; 
       transform: translate(-50%, -50%);">

          <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" aria-hidden="true">
            <circle class="checkmark__circle" cx="26" cy="26" r="20" />
            <path class="checkmark__check" fill="none" d="M14 27l7 7 16-16" />
          </svg>

        </div>
      </div>

      <div id="resultado">Aguardando leitura...</div>

      <div id="animacao-leitura">Saída registra com sucesso!</div>

      <a class="voltar" href="index.php">Voltar</a>

      <button id="btn-reset" style="display: none">Reiniciar Leitura</button>

    </section>
  </main>

  <footer class="rodape">
    Copyright © 2025 SEAD | EPP. Todos os direitos reservados
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
  <script>
    const video = document.getElementById('video');
    const resultado = document.getElementById('resultado');
    const animacao = document.getElementById('animacao-leitura');
    const btnReset = document.getElementById('btn-reset');

    let stream = null;
    let scanning = false;

    function iniciarCamera() {
      navigator.mediaDevices.getUserMedia({
          video: {
            facingMode: "environment"
          }
        })
        .then(s => {
          stream = s;
          video.srcObject = stream;
          video.setAttribute("playsinline", true);
          video.play();
          scanning = true;
          animacao.classList.remove('show');
          btnReset.style.display = 'none';
          resultado.textContent = "Aguardando leitura...";
          requestAnimationFrame(scanQRCode);
        })
        .catch(err => {
          resultado.textContent = "Erro ao acessar a câmera: " + err.message;
        });
    }

    function pararCamera() {
      if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
      }
      scanning = false;
    }

    function scanQRCode() {
      if (!scanning) return;

      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });

        if (code) {
          const crachaId = code.data.trim();
          resultado.textContent = "QR Code lido: " + crachaId;

          pararCamera();
          animacao.classList.add('show');
          enviarSaida(crachaId);
          return;
        }
      }
      requestAnimationFrame(scanQRCode);
    }

    function enviarSaida(crachaId) {
      fetch('registrar_saida.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `cracha_id=${encodeURIComponent(crachaId)}`
        })
        .then(response => response.text())
        .then(data => {
          if (data.trim() === "OK" || data.trim() === "JA_REGISTRADO") {
            resultado.textContent = data.trim() === "OK" ? "Saída registrada com sucesso!" : "Saída já registrada!";

            // Esconde o vídeo e animação anterior
            video.style.display = 'none';
            animacao.classList.remove('show');
            btnReset.style.display = 'none';

            // Mostra o check animado
            const checkmark = document.getElementById('checkmark');
            checkmark.style.display = 'block';

            // Espera 10 segundos e redireciona
            setTimeout(() => {
              window.location.href = 'index.php';
            }, 5000);

          } else {
            resultado.textContent = "Erro ao registrar saída.";
            btnReset.style.display = 'inline-block';
          }
        })
        .catch(error => {
          resultado.textContent = "Erro ao registrar saída.";
          btnReset.style.display = 'inline-block';
        });
    }


    btnReset.addEventListener('click', iniciarCamera);

    // Inicia assim que abrir
    iniciarCamera();
  </script>
</body>

</html>