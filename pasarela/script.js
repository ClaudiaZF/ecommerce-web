document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const cardName = document.getElementById('cardName').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const expiry = document.getElementById('expiry').value.trim();
    const cvv = document.getElementById('cvv').value.trim();
    let message = '';

    // Validaciones básicas
    if (cardName.length < 3) {
        message = 'El nombre debe tener al menos 3 caracteres.';
    } else if (!/^\d{16}$/.test(cardNumber)) {
        message = 'El número de tarjeta debe tener 16 dígitos.';
    } else if (!/^\d{2}\/\d{2}$/.test(expiry)) {
        message = 'La fecha de expiración debe tener el formato MM/AA.';
    } else if (!/^\d{3,4}$/.test(cvv)) {
        message = 'El CVV debe tener 3 o 4 dígitos.';
    } else {
        message = '¡Pago procesado exitosamente!';
    }

    const paymentMessage = document.getElementById('paymentMessage');
    paymentMessage.textContent = message;
    paymentMessage.style.color = message.includes('exitosamente') ? 'green' : 'red';
});
