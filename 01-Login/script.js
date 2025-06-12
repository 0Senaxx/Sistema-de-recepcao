document.getElementById('matricula').addEventListener('input', function(e) {
    let value = e.target.value.toUpperCase().replace(/[^0-9A-Z]/g, ''); // Só números e letras maiúsculas

    // Separando partes da matrícula: 000.000-0A
    let formatted = '';

    if (value.length > 0) formatted += value.substring(0, 3);
    if (value.length > 3) formatted += '.' + value.substring(3, 6);
    if (value.length > 6) formatted += '-' + value.substring(6, 7);
    if (value.length > 7) formatted += value.substring(7, 8);

    e.target.value = formatted;
});
