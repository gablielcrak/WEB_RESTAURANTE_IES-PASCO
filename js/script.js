// Esperar a que todo el HTML de la página esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
    
    // ==========================================================================
    // 1. BLOQUEAR FECHAS PASADAS EN EL FORMULARIO DE RESERVAS
    // ==========================================================================
    const inputFecha = document.getElementById("fecha");
    
    if (inputFecha) {
        // Obtener la fecha actual del sistema
        const hoy = new Date();
        const yyyy = hoy.getFullYear();
        let mm = hoy.getMonth() + 1; // Los meses en JS empiezan desde 0
        let dd = hoy.getDate();

        // Formatear mes y día para que tengan siempre 2 dígitos (ej: 05 en vez de 5)
        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;

        // Crear el formato requerido por el HTML (YYYY-MM-DD)
        const fechaMinima = `${yyyy}-${mm}-${dd}`;
        
        // Asignar el atributo 'min' al input para bloquear días anteriores
        inputFecha.setAttribute("min", fechaMinima);
    }

    // ==========================================================================
    // 2. EFECTO VISUAL EN EL ENCABEZADO (HEADER) AL HACER SCROLL
    // ==========================================================================
    const header = document.querySelector("header");

    window.addEventListener("scroll", function () {
        // Si el usuario baja más de 50 píxeles, el menú cambia sutilmente
        if (window.scrollY > 50) {
            header.style.backgroundColor = "#0a0a0a"; // Un negro más profundo
            header.style.boxShadow = "0 4px 15px rgba(0, 0, 0, 0.5)"; // Sombra elegante
            header.style.transition = "all 0.4s ease";
        } else {
            header.style.backgroundColor = "#1a1a1a"; // Color original
            header.style.boxShadow = "none";
        }
    });

});