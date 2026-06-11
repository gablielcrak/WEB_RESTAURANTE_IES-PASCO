// Abrir Ventana Emergente del Menú
function abrirModal(titulo, ingredientes) {
    const modal = document.getElementById('modal-ingredientes');
    document.getElementById('modal-titulo').innerText = titulo;
    document.getElementById('modal-cuerpo').innerText = "Detalles: " + ingredientes;
    modal.style.display = 'flex';
}

// Cerrar Ventana Emergente del Menú
function cerrarModal() {
    document.getElementById('modal-ingredientes').style.display = 'none';
}

// Cerrar haciendo clic fuera de la caja blanca
window.onclick = function(event) {
    const modal = document.getElementById('modal-ingredientes');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}