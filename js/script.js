/* ==========================================================================
   MOTOR INTERACTIVO GLOBAL - SABORES DEL MUNDO (script.js)
   ========================================================================== */

document.addEventListener("DOMContentLoaded", function () {
    
    // ---------------------------------------------------------
    // 1. MARCAR ENLACE ACTIVO EN LA BARRA DE NAVEGACIÓN
    // ---------------------------------------------------------
    const ubicacionActual = window.location.pathname.split("/").pop();
    const enlacesNav = document.querySelectorAll("nav ul li a");

    enlacesNav.forEach(enlace => {
        const hrefAtributo = enlace.getAttribute("href");
        if (ubicacionActual === hrefAtributo) {
            enlacesNav.forEach(el => el.classList.remove("active"));
            enlace.classList.add("active");
        }
    });

    // ---------------------------------------------------------
    // 2. ANIMACIÓN "REVELAR" AL HACER SCROLL
    // ---------------------------------------------------------
    const elementosParaRevelar = document.querySelectorAll("#entradas, #fondos, #postres, #bebidas, .card-destacado, .nosotros-section, .evento-box");

    elementosParaRevelar.forEach(el => {
        el.style.opacity = "0";
        el.style.transform = "translateY(25px)";
        el.style.transition = "opacity 0.7s ease-out, transform 0.7s ease-out";
    });

    const verificarScroll = () => {
        const puntoDeCorte = window.innerHeight * 0.88;

        elementosParaRevelar.forEach(elemento => {
            const distanciaDelTecho = elemento.getBoundingClientRect().top;

            if (distanciaDelTecho < puntoDeCorte) {
                elemento.style.opacity = "1";
                elemento.style.transform = "translateY(0)";
            }
        });
    };

    window.addEventListener("scroll", verificarScroll);
    verificarScroll(); // Disparo inicial por si ya hay elementos visibles en pantalla

    // ---------------------------------------------------------
    // 3. CONTROLADOR DE LA VENTANA EMERGENTE (MODAL DE DETALLES)
    // ---------------------------------------------------------
    const modal = document.getElementById("modal-info-plato");
    const botonesDetalles = document.querySelectorAll(".btn-detalles");
    const botonCerrar = document.querySelector(".cerrar-modal");

    if (modal && botonesDetalles.length > 0) {
        
        // Al hacer clic en "Ver Detalles e Ingredientes"
        botonesDetalles.forEach(boton => {
            boton.addEventListener("click", function() {
                // Extraer datos de los atributos personalizados del HTML
                const titulo = this.getAttribute("data-titulo");
                const descripcion = this.getAttribute("data-desc");
                const precio = this.getAttribute("data-precio");
                const rutaImagen = this.getAttribute("data-img");

                // Inyectar la información en la ventana flotante
                document.getElementById("modal-titulo").innerText = titulo;
                document.getElementById("modal-descripcion").innerText = descripcion;
                document.getElementById("modal-precio").innerText = precio;
                document.getElementById("modal-img").src = rutaImagen;
                document.getElementById("modal-input-item").value = titulo;

                // Mostrar el modal centrado con Flexbox
                modal.style.display = "flex";
            });
        });

        // Cerrar el modal al hacer clic en la (X)
        if (botonCerrar) {
            botonCerrar.addEventListener("click", function() {
                modal.style.display = "none";
            });
        }

        // Cerrar el modal automáticamente si el usuario hace clic fuera de la caja blanca
        window.addEventListener("click", function(evento) {
            if (evento.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    // ---------------------------------------------------------
    // 4. VALIDACIÓN DE FORMULARIOS DE PEDIDOS (MESA Y CANTIDAD)
    // ---------------------------------------------------------
    // Funciona de forma automática tanto para los formularios normales como para el del modal.
    const interceptarPedidos = (formulario) => {
        formulario.addEventListener("submit", function (evento) {
            const inputMesa = formulario.querySelector("input[name='numero_mesa']");
            const inputCantidad = formulario.querySelector("input[name='cantidad']");
            
            if (inputMesa && inputCantidad) {
                const mesaValue = parseInt(inputMesa.value);
                const cantidadValue = parseInt(inputCantidad.value);

                if (mesaValue <= 0 || isNaN(mesaValue)) {
                    evento.preventDefault(); // Detiene el envío
                    alert("🚨 Error: Introduce un número de mesa válido (Mayor a 0).");
                    inputMesa.focus();
                    return false;
                }

                if (cantidadValue <= 0 || isNaN(cantidadValue)) {
                    evento.preventDefault(); // Detiene el envío
                    alert("🚨 Error: La cantidad solicitada debe ser mínimo 1.");
                    inputCantidad.focus();
                    return false;
                }
                
                // Efecto visual en el botón durante la carga hacia el servidor PHP
                const botonEnvio = formulario.querySelector("button");
                if (botonEnvio) {
                    botonEnvio.innerHTML = "⌛ Enviando...";
                    botonEnvio.style.backgroundColor = "#111111";
                    botonEnvio.style.color = "#ffffff";
                }
            }
        });
    };

    // Aplicar la validación a todos los formularios del sitio
    const todosLosFormularios = document.querySelectorAll(".form-pedido, #form-pedido-modal");
    todosLosFormularios.forEach(formulario => {
        interceptarPedidos(formulario);
    });

});