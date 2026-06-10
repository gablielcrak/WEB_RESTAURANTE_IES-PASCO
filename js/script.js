/* ==========================================================================
   INTERACTIVIDAD Y ANIMACIONES - RESTAURANTE SABORES DEL MUNDO
   ========================================================================== */

document.addEventListener("DOMContentLoaded", function () {
    
    // ==========================================================================
    // 1. BLOQUEAR FECHAS PASADAS EN EL CALENDARIO (Página: contacto.html)
    // ==========================================================================
    const inputFecha = document.getElementById("fecha");
    
    if (inputFecha) {
        // Obtener la fecha actual del sistema
        const hoy = new Date();
        const yyyy = hoy.getFullYear();
        let mm = hoy.getMonth() + 1; // Los meses en JavaScript se indexan desde 0
        let dd = hoy.getDate();

        // Forzar formato de dos dígitos para meses y días (ej: 09 en vez de 9)
        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;

        // Construir la cadena en formato estándar HTML5 (YYYY-MM-DD)
        const fechaMinima = `${yyyy}-${mm}-${dd}`;
        
        // Aplicar la restricción al calendario
        inputFecha.setAttribute("min", fechaMinima);
    }


    // ==========================================================================
    // 2. EFECTO DE NAVEGACIÓN FLOTANTE / SCROLL EN EL ENCABEZADO (Todas las páginas)
    // ==========================================================================
    const header = document.querySelector("header");

    window.addEventListener("scroll", function () {
        // Si el usuario baja más de 50 píxeles, la barra se vuelve más oscura y compacta
        if (window.scrollY > 50) {
            header.style.backgroundColor = "#0a0a0a"; 
            header.style.boxShadow = "0 4px 15px rgba(0, 0, 0, 0.5)"; 
            header.style.transition = "all 0.4s ease";
        } else {
            // Regresa a su estado original al estar en el tope de la pantalla
            header.style.backgroundColor = "#1a1a1a"; 
            header.style.boxShadow = "none";
        }
    });


    // ==========================================================================
    // 3. ANIMACIÓN MÁQUINA DE ESCRIBIR (Página: index.html)
    // ==========================================================================
    const textoCambiante = document.querySelector(".hero-content h2");
    
    if (textoCambiante) {
        // Frases dinámicas que se alternarán en el banner principal
        const frases = ["Alta Cocina en tu Mesa", "Momentos Inolvidables", "Sabores del Mundo"];
        let fraseIndex = 0;
        let caracterIndex = 0;
        let borrando = false;

        function animarTexto() {
            const fraseActual = frases[fraseIndex];
            
            if (!borrando) {
                // Modo: Escribiendo caracteres
                textoCambiante.textContent = fraseActual.substring(0, caracterIndex + 1);
                caracterIndex++;
                
                // Si completó la frase, hace una pausa de 2 segundos antes de borrar
                if (caracterIndex === fraseActual.length) {
                    borrando = true;
                    setTimeout(animarTexto, 2000);
                    return;
                }
            } else {
                // Modo: Borrando caracteres
                textoCambiante.textContent = fraseActual.substring(0, caracterIndex - 1);
                caracterIndex--;
                
                // Si borró toda la frase, avanza al siguiente elemento del array
                if (caracterIndex === 0) {
                    borrando = false;
                    fraseIndex = (fraseIndex + 1) % frases.length;
                }
            }
            
            // Define velocidades diferentes (más lento al escribir, veloz al borrar)
            const velocidadEfecto = borrando ? 50 : 100;
            setTimeout(animarTexto, velocidadEfecto);
        }
        
        // Inicializar el bucle de la animación
        animarTexto();
    }


    // ==========================================================================
    // 4. ANIMACIÓN SCROLL REVEAL / REVELADO DINÁMICO (Todas las páginas)
    // ==========================================================================
    // Captura todos los objetos que tengan la clase 'revelar' en el HTML
    const elementosARevelar = document.querySelectorAll(".revelar");
    
    const opcionesConfig = {
        threshold: 0.15, // Ejecuta la animación cuando se visualice el 15% del elemento
        rootMargin: "0px 0px -50px 0px" // Margen inferior para anticipar el efecto antes del choque visual
    };

    // Uso de IntersectionObserver para un rendimiento óptimo de memoria en el navegador
    const observadorEfectos = new IntersectionObserver(function(entradas, observador) {
        entradas.forEach(entrada => {
            if (entrada.isIntersecting) {
                // Añade la clase CSS que activa la transición fluida hacia arriba
                entrada.target.classList.add("activo");
                // Deja de escuchar el elemento una vez ejecutado para ahorrar recursos
                observador.unobserve(entrada.target);
            }
        });
    }, opcionesConfig);

    // Asignar el observador a cada elemento configurado
    elementosARevelar.forEach(elemento => {
        observadorEfectos.observe(elemento);
    });

});