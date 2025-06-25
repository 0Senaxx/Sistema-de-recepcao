<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registrar Saída</title>
<style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #121214;
  color: #e1e1e6;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 100vh;
  margin: 0;
  padding: 20px;
}
h1 {
  margin-bottom: 15px;
  font-weight: 600;
  font-size: 1.8rem;
  color: #00bfa5;
  text-shadow: 0 0 8px #00bfa5;
}
#video-container {
  position: relative;
  width: 320px;
  height: 320px;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 0 25px #00bfa5aa;
  background: #000;
}
video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
#video-container::before,
#video-container::after {
  content: ""; position: absolute; border: 4px solid #00bfa5;
  width: 50px; height: 50px; border-radius: 8px;
}
#video-container::before {
  top: 0; left: 0;
  border-right: none; border-bottom: none;
}
#video-container::after {
  bottom: 0; right: 0;
  border-left: none; border-top: none;
}
#resultado {
  margin-top: 25px;
  font-weight: 700;
  font-size: 1.1rem;
  min-height: 2em;
  color: #00e676;
  text-shadow: 0 0 6px #00e676aa;
}
#animacao-leitura {
  margin-top: 20px;
  color: #00e676;
  font-weight: 700;
  font-size: 1.2rem;
  opacity: 0;
  transition: opacity 0.5s ease;
}
#animacao-leitura.show {
  opacity: 1;
}
#btn-reset {
  margin-top: 15px;
  background-color: #00bfa5;
  color: #121214;
  border: none;
  border-radius: 8px;
  padding: 10px 18px;
  font-weight: 600;
  cursor: pointer;
}
#btn-reset:hover {
  background-color: #00e676;
  color: #000;
}
</style>
</head>
<body>

<h1>Registrar Saída</h1>

<div id="video-container">
  <video id="video" autoplay playsinline></video>
</div>

<div id="resultado">Aguardando leitura...</div>
<div id="animacao-leitura">Leitura realizada, atualizando registro de saída...</div>
<button id="btn-reset" style="display: none">Reiniciar Leitura</button>

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
const video = document.getElementById('video');
const resultado = document.getElementById('resultado');
const animacao = document.getElementById('animacao-leitura');
const btnReset = document.getElementById('btn-reset');

let stream = null;
let scanning = false;

function iniciarCamera() {
  navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
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
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `cracha_id=${encodeURIComponent(crachaId)}`
  })
    .then(response => response.text())
    .then(data => {
      if (data.trim() === "OK") {
        resultado.textContent = "Saída registrada com sucesso.";
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 2000);
      } else if (data.trim() === "JA_REGISTRADO") {
        resultado.textContent = "Saída já registrada.";
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 2000);
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
