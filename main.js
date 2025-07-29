class Producto {
    constructor({id, nombre, precio, descripcion, descuento = 0}) {
        this.id = id;
        this.nombre = nombre;
        this.precio = parseFloat(precio);
        this.descripcion = descripcion;
        this.descuento = parseInt(descuento);
    }
    getPrecioFinal() {
        if (this.descuento && this.descuento > 0) {
            return (this.precio * (1 - this.descuento / 100)).toFixed(2);
        }
        return this.precio.toFixed(2);
    }
    getMensajePromocion() {
        return this.descuento > 0 ? `¡${this.descuento}% de descuento!` : null;
    }
}

class Resena {
    constructor({id, producto_id, usuario, calificacion, comentario, fecha}) {
        this.id = id;
        this.producto_id = producto_id;
        this.usuario = usuario;
        this.calificacion = calificacion;
        this.comentario = comentario;
        this.fecha = fecha;
    }
}

let productos = [];
let carrito = [];

function mostrarNotificacion(mensaje, tipo = "info") {
    let contenedor = document.getElementById("notificaciones-stack");
    if (!contenedor) {
        contenedor = document.createElement("div");
        contenedor.id = "notificaciones-stack";
        contenedor.style.position = "fixed";
        contenedor.style.top = "20px";
        contenedor.style.right = "20px";
        contenedor.style.zIndex = 2000;
        contenedor.style.display = "flex";
        contenedor.style.flexDirection = "column";
        contenedor.style.gap = "10px";
        document.body.appendChild(contenedor);
    }
    let notif = document.createElement("div");
    notif.className = `notificacion ${tipo}`;
    notif.innerText = mensaje;
    contenedor.appendChild(notif);
    setTimeout(() => {
        notif.remove();
        if (contenedor.childElementCount === 0) contenedor.remove();
    }, 3000);
}

function agregarAlCarrito(idProducto) {
    const producto = productos.find(p => p.id == idProducto);
    let existente = carrito.find(p => p.id == idProducto);
    if (existente) {
        if (!existente.cantidad) existente.cantidad = 1;
        existente.cantidad++;
    } else {
        producto.cantidad = 1;
        carrito.push(producto);
    }
    // Ahora enviamos la cantidad al backend
    fetch('php/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'producto_id=' + encodeURIComponent(idProducto)
    })
    .then(res => res.json())
    .then(() => {
        mostrarNotificacion(`Agregado al carrito: ${producto.nombre}`, "carrito");
        actualizarEstadoCarrito();
        actualizarMedallaCarrito();
    });
}

function actualizarEstadoCarrito() {
    let carritoBar = document.getElementById("carrito-bar");
    let total = carrito.reduce((acc, prod) => acc + Number(prod.getPrecioFinal()), 0);
    if (!carritoBar) {
        carritoBar = document.createElement("div");
        carritoBar.id = "carrito-bar";
        carritoBar.style.position = "fixed";
        carritoBar.style.bottom = "10px";
        carritoBar.style.right = "10px";
        carritoBar.style.background = "#1976d2";
        carritoBar.style.color = "#fff";
        carritoBar.style.padding = "10px 18px";
        carritoBar.style.borderRadius = "8px";
        carritoBar.style.zIndex = 1000;
        document.body.appendChild(carritoBar);
    }
    carritoBar.innerHTML = `🛒 Carrito: <b>${carrito.length}</b> producto(s) | Total: <b>${total.toLocaleString('es-CL', { style: 'currency', currency: 'CLP' })}</b>`;
    if (!document.getElementById('btn-finalizar-compra')) {
        let btnFinalizar = document.createElement('button');
        btnFinalizar.textContent = 'Finalizar compra';
        btnFinalizar.className = 'btn-pedido';
        btnFinalizar.id = 'btn-finalizar-compra';
        btnFinalizar.style.marginLeft = '16px';
        btnFinalizar.onclick = function() {
            document.getElementById('modal-finalizar-compra').style.display = 'block';
        };
        carritoBar.appendChild(btnFinalizar);
    }
}

// Agregar icono de carrito y medalla en la barra de búsqueda
function renderCarritoIcono() {
    const searchContainer = document.querySelector('.search-container');
    if (!document.getElementById('icono-carrito')) {
        const iconoDiv = document.createElement('div');
        iconoDiv.id = 'icono-carrito';
        iconoDiv.style.display = 'inline-block';
        iconoDiv.style.position = 'relative';
        iconoDiv.style.marginLeft = '16px';
        iconoDiv.innerHTML = `
            <span style="font-size: 2rem; cursor:pointer;">🛒</span>
            <span id="medalla-carrito" style="position:absolute;top:0;right:0;background:#d32f2f;color:#fff;font-size:0.9rem;padding:2px 7px;border-radius:50%;font-weight:bold;">0</span>
        `;
        searchContainer.appendChild(iconoDiv);
        // Redirigir al hacer click en el icono de carrito
        iconoDiv.addEventListener('click', function(e) {
            window.location.href = 'carrito.html';
        });
    }
}

function actualizarMedallaCarrito() {
    const medalla = document.getElementById('medalla-carrito');
    if (medalla) {
        medalla.textContent = carrito.length;
    }
}

function mostrarProductos(productosAMostrar, mostrarPromos = false) {
    const resultsContainer = document.getElementById("results-container");
    resultsContainer.innerHTML = "";
    if (productosAMostrar.length > 0) {
        productosAMostrar.forEach(producto => {
            const productDiv = document.createElement("div");
            productDiv.className = "product";
            productDiv.style.cursor = "pointer";
            // Al hacer click en la carta solo se cargan reseñas
            productDiv.addEventListener("click", function() {
                cargarResenas(producto.id);
            });
            let promo = producto.getMensajePromocion();
            productDiv.innerHTML = `
                <h3>${producto.nombre}</h3>
                <p>${producto.descripcion}</p>
                <p>Precio: <span style="color:#388e3c;font-weight:bold;">${Number(producto.getPrecioFinal()).toLocaleString('es-CL', { style: 'currency', currency: 'CLP' })}</span> ${promo ? `<span class='promo'>${promo}</span>` : ""}</p>
                <button onclick="agregarAlCarrito('${producto.id}');event.stopPropagation();">Agregar al carrito</button>
                <button class="btn-resenar" data-producto-id="${producto.id}" style="margin-left:8px;">Reseñar</button>
            `;
            resultsContainer.appendChild(productDiv);
            if (promo && mostrarPromos) mostrarNotificacion(promo, "promo");
        });
        // Delegación de eventos para los botones Reseñar
        resultsContainer.querySelectorAll('.btn-resenar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const productoId = this.getAttribute('data-producto-id');
                cargarResenas(productoId);
                abrirModalResena(productoId);
            });
        });
    } else {
        resultsContainer.innerHTML = "<p>No se encontraron productos.</p>";
    }
}

function mostrarResenas(resenas) {
    const seccion = document.getElementById("seccion-resenas");
    if (!seccion) return;
    seccion.innerHTML = "";
    if (resenas.length === 0) {
        seccion.innerHTML = '<p class="sin-resenas">Este producto aún no tiene reseñas.</p>';
        return;
    }
    resenas.forEach(r => {
        const div = document.createElement("div");
        div.className = "resena";
        div.innerHTML = `
            <div class="resena-header">
                <span class="resena-usuario">${r.usuario}</span>
                <span class="resena-fecha">${new Date(r.fecha).toLocaleDateString()}</span>
            </div>
            <div class="resena-estrellas">${'★'.repeat(r.calificacion)}${'☆'.repeat(5 - r.calificacion)}</div>
            <div class="resena-comentario">${r.comentario}</div>
        `;
        seccion.appendChild(div);
    });
}

function cargarResenas(productoId) {
    fetch(`./php/obtener_resenas.php?producto_id=${productoId}`)
        .then(res => res.json())
        .then(data => mostrarResenas(data));
}

function cargarProductosDesdeBD() {
    fetch('./php/obtener_productos.php')
        .then(response => {
            if (!response.ok) throw new Error('No se pudo obtener productos');
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                mostrarNotificacion('No se encontraron productos en la tienda.', 'error');
                mostrarProductos([], false);
                return;
            }
            productos = data.map(p => new Producto(p));
            mostrarProductos(productos, false);
        })
        .catch(error => {
            mostrarNotificacion('Error al cargar productos: ' + error.message, 'error');
            const resultsContainer = document.getElementById("results-container");
            if (resultsContainer) {
                resultsContainer.innerHTML = '<p style="color:red;">No se pudieron cargar los productos.</p>';
            }
            console.error('Error al cargar productos:', error);
        });
}

function searchProducts() {
    const searchInput = document.getElementById("product-search").value.toLowerCase();
    const filteredProducts = productos.filter(producto => 
        producto.nombre.toLowerCase().includes(searchInput)
    );
    mostrarProductos(filteredProducts, true);
}

function abrirModalResena(productoId) {
    const modal = document.getElementById('modal-resena');
    const inputProductoId = document.getElementById('producto_id_resena');
    if (inputProductoId) inputProductoId.value = productoId;
    if (modal) modal.classList.add('active');
}

function cerrarModalResena() {
    const modal = document.getElementById('modal-resena');
    if (modal) modal.classList.remove('active');
}

document.addEventListener("DOMContentLoaded", function() {
    // Al cargar la página, obtener productos del carrito en sesión PHP
    fetch('php/carrito_productos.php')
        .then(res => res.json())
        .then(data => {
            if (data.productos && data.productos.length > 0) {
                // Limpia el carrito JS y lo rellena con los productos de la sesión
                carrito = data.productos.map(p => new Producto(p));
                actualizarEstadoCarrito();
                actualizarMedallaCarrito();
            }
        });
    cargarProductosDesdeBD();
    document.getElementById("product-search").addEventListener("keyup", searchProducts);
    renderCarritoIcono();
    actualizarMedallaCarrito();
    const cerrarBtn = document.getElementById('cerrar-modal-resena');
    if (cerrarBtn) cerrarBtn.onclick = cerrarModalResena;
    // Cerrar modal al hacer click fuera del contenido
    const modal = document.getElementById('modal-resena');
    if (modal) {
        modal.onclick = function(e) {
            if (e.target === modal) cerrarModalResena();
        };
    }
    const formResena = document.getElementById('form-resena');
    if (formResena) {
        formResena.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(formResena);
            // Asegura que el producto_id correcto se envía como producto_id
            formData.set('producto_id', formData.get('producto_id_resena'));
            fetch('php/guardar_resena.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(msg => {
                mostrarNotificacion('¡Reseña enviada correctamente!', 'carrito');
                cerrarModalResena();
                // Limpiar formulario
                formResena.reset();
                // Recargar reseñas del producto
                const productoId = formData.get('producto_id');
                if (productoId) cargarResenas(productoId);
            })
            .catch(() => {
                mostrarNotificacion('Error al enviar la reseña', 'error');
            });
        };
    }

    // Modal Registrar Pedido
    const btnAbrir = document.getElementById('abrir-modal-pedido');
    const modalPedido = document.getElementById('modal-pedido');
    const cerrarBtnPedido = document.getElementById('cerrar-modal-pedido');
    if(btnAbrir && modalPedido) {
        btnAbrir.onclick = function() {
            modalPedido.style.display = 'block';
        };
    }
    if(cerrarBtnPedido && modalPedido) {
        cerrarBtnPedido.onclick = function() {
            modalPedido.style.display = 'none';
        };
    }
    window.onclick = function(event) {
        if (event.target === modalPedido) {
            modalPedido.style.display = 'none';
        }
    };

    // Modal Ver Pedidos
    const btnVerPedidos = document.getElementById('ver-pedidos-btn');
    const modalVerPedidos = document.getElementById('modal-ver-pedidos');
    const cerrarVerPedidos = document.getElementById('cerrar-modal-ver-pedidos');
    const contenedorPedidos = document.getElementById('contenedor-pedidos');
    if(btnVerPedidos && modalVerPedidos) {
        btnVerPedidos.onclick = function() {
            modalVerPedidos.style.display = 'block';
            fetch('php/obtener_pedidos.php')
                .then(res => res.json())
                .then(data => {
                    if(data.length === 0) {
                        contenedorPedidos.innerHTML = '<p style="text-align:center;color:#888;">No hay pedidos registrados.</p>';
                    } else {
                        let tabla = `<table class='tabla-pedidos'><thead><tr><th>ID</th><th>Producto</th><th>Tipo</th><th>Unidades</th><th>Descripción</th><th>Observaciones</th><th>Fecha</th></tr></thead><tbody>`;
                        data.forEach(p => {
                            tabla += `<tr><td>${p.id}</td><td>${p.producto_nombre || p.producto_id}</td><td>${p.tipo}</td><td>${p.unidades}</td><td>${p.descripcion}</td><td>${p.observaciones || ''}</td><td>${p.fecha ? new Date(p.fecha).toLocaleString('es-CL') : ''}</td></tr>`;
                        });
                        tabla += '</tbody></table>';
                        contenedorPedidos.innerHTML = tabla;
                    }
                })
                .catch(() => {
                    contenedorPedidos.innerHTML = '<p style="color:red;text-align:center;">Error al cargar los pedidos.</p>';
                });
        };
    }
    if(cerrarVerPedidos && modalVerPedidos) {
        cerrarVerPedidos.onclick = function() {
            modalVerPedidos.style.display = 'none';
        };
    }
    window.addEventListener('click', function(event) {
        if (event.target === modalVerPedidos) {
            modalVerPedidos.style.display = 'none';
        }
    });

    // Buscador y select dinámico de productos en el formulario de pedido
    function cargarProductosEnSelect() {
        fetch('php/obtener_productos_simple.php')
            .then(res => res.json())
            .then(productos => {
                const select = document.getElementById('producto_id');
                if (!select) return;
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                productos.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nombre;
                    select.appendChild(opt);
                });
            });
    }
    cargarProductosEnSelect();
    const buscador = document.getElementById('buscador-producto');
    const select = document.getElementById('producto_id');
    if(buscador && select) {
        buscador.addEventListener('input', function() {
            const filtro = buscador.value.toLowerCase();
            for (let i = 0; i < select.options.length; i++) {
                const option = select.options[i];
                if (i === 0) {
                    option.style.display = '';
                    continue;
                }
                option.style.display = option.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            }
            for (let i = 1; i < select.options.length; i++) {
                if (select.options[i].style.display !== 'none') {
                    select.selectedIndex = i;
                    break;
                }
            }
        });
    }

    const formFinalizar = document.getElementById('form-finalizar-compra');
    if (formFinalizar) {
        formFinalizar.onsubmit = function(e) {
            e.preventDefault();
            fetch('php/finalizar_compra.php', {
                method: 'POST',
                body: new FormData(document.getElementById('form-finalizar-compra'))
            })
            .then(res => res.json())
            .then(data => {
                if(data.success && data.compra_id) {
                    window.location.href = `php/detalle_compra.php?id=${data.compra_id}`;
                } else if(data.success) {
                    mostrarNotificacion(data.mensaje, 'carrito');
                    document.getElementById('modal-finalizar-compra').style.display = 'none';
                } else {
                    mostrarNotificacion('Error al registrar la compra', 'error');
                }
            });
        };
    }
});


