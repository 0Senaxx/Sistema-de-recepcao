window.addEventListener('DOMContentLoaded', () => {
  const camera = document.getElementById('camera');
  const canvas = document.getElementById('canvas');
  const fotoBase64Input = document.getElementById('foto_base64');
  const fotoCapturada = document.getElementById('fotoCapturada');
  const semFoto = document.getElementById('semFoto');
  const btnAbrirCamera = document.getElementById('btnAbrirCamera');
  const btnCapturarFoto = document.getElementById('btnCapturarFoto');
  const btnRepetirFoto = document.getElementById('btnRepetirFoto');

  let stream;

  async function iniciarCamera() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: true });
      camera.srcObject = stream;
      camera.style.display = 'block';
      canvas.style.display = 'block'; // Mostra o canvas (pode ser usado para captura)

      if (fotoCapturada) fotoCapturada.style.display = 'none';
      if (semFoto) semFoto.style.display = 'none';

      btnAbrirCamera.style.display = 'none';
      btnCapturarFoto.style.display = 'inline-block';
      btnRepetirFoto.style.display = 'none';
    } catch (err) {
      console.error("Erro ao acessar a câmera: ", err);
      alert("Não foi possível acessar a câmera. Verifique as permissões.");
    }
  }

  function capturarFoto() {
    canvas.width = camera.videoWidth;
    canvas.height = camera.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(camera, 0, 0, canvas.width, canvas.height);

    const fotoData = canvas.toDataURL('image/jpeg');
    fotoBase64Input.value = fotoData;

    if (fotoCapturada) {
      fotoCapturada.src = fotoData;
      fotoCapturada.style.display = 'block';
    }
    if (semFoto) semFoto.style.display = 'none';

    camera.style.display = 'none';
    canvas.style.display = 'none'; // Esconde o canvas após captura

    btnCapturarFoto.style.display = 'none';
    btnRepetirFoto.style.display = 'inline-block';

    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
  }

  function resetarCamera() {
    fotoBase64Input.value = '';
    if (fotoCapturada) fotoCapturada.style.display = 'none';
    if (semFoto) semFoto.style.display = 'none';

    btnRepetirFoto.style.display = 'none';

    iniciarCamera();
  }

  btnAbrirCamera.addEventListener('click', iniciarCamera);
  btnCapturarFoto.addEventListener('click', capturarFoto);
  btnRepetirFoto.addEventListener('click', resetarCamera);
});
