<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="main.js"></script>
    <title>Tienda de Comercio Electrónico</title>
</head>

<body>
<?php
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
session_start();
if (isset($_SESSION['flash'])) {
    echo "<div class='flash-message' style='background:#dff0d8;color:#388e3c;padding:12px 18px;border-radius:6px;margin:18px auto;max-width:600px;text-align:center;font-weight:bold;'>" . htmlspecialchars($_SESSION['flash']) . "</div>";
    unset($_SESSION['flash']);
}
?>
    <div class="search-container">
        <input type="text" id="product-search" placeholder="Buscar producto">
        <button onclick="searchProducts()">Buscar</button>
        <button id="abrir-modal-pedido" type="button" class="btn-pedido-nav">Registrar Pedido</button>
        <button id="ver-pedidos-btn" type="button" class="btn-pedido-nav" style="background:#1976d2;margin-left:8px;">Ver Pedidos</button>
        <a href="php/clientes_mas_compras.php" class="btn-pedido-nav" style="background:#ff9800;margin-left:8px;">Clientes frecuentes</a>
        <a href="agregar_producto.html" class="btn-pedido-nav" style="background:#43a047;margin-left:8px;">Agregar producto</a>
        <a href="agregar_cliente.html" class="btn-pedido-nav" style="background:#00897b;margin-left:8px;">Agregar cliente</a>
    </div>
    <div id="results-container">
        
    </div>

    
    <div id="review-resena-grid">
        <div id="seccion-resenas" class="seccion-resenas">
            <h3>Reseñas del producto</h3>
            <p class="sin-resenas">Selecciona un producto para ver sus reseñas.</p>
        </div>
    </div>

    <div id="modal-pedido" class="modal-pedido">
        <div class="modal-content">
            <span class="close-modal" id="cerrar-modal-pedido">&times;</span>
            <form action="php/registrar_pedido.php" method="POST" id="form-pedido" class="form-pedido">
                <h2>Registrar Pedido</h2>
                <div class="form-group">
                    <label for="descripcion">Descripción del pedido:</label>
                    <input type="text" id="descripcion" name="descripcion" required>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo de pedido:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="compra">Compra</option>
                        <option value="reserva">Reserva</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="producto_id">Producto:</label>
                    <input type="text" id="buscador-producto" placeholder="Buscar producto..." autocomplete="off" style="width:100%;margin-bottom:6px;">
                    <select id="producto_id" name="producto_id" required style="width:100%;max-width:100%;">
                        <option value="">Seleccione un producto</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="unidades">Unidades:</label>
                    <input type="number" id="unidades" name="unidades" min="1" required>
                </div>
                <div class="form-group">
                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones" name="observaciones" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-pedido">Registrar Pedido</button>
            </form>
        </div>
    </div>

    <div id="modal-resena" class="modal-resena">
        <div class="modal-content">
            <span class="close-modal" id="cerrar-modal-resena">&times;</span>
            <h2>Deja tu reseña</h2>
            <form id="form-resena" action="php/guardar_resena.php" method="POST">
                <?php
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                ?>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <label for="usuario">Tu nombre:</label>
                <input type="text" id="usuario" name="usuario" required>

                <label>Calificación:</label>
                <div class="stars">
                    <input type="radio" id="star5" name="calificacion" value="5" required><label for="star5">★</label>
                    <input type="radio" id="star4" name="calificacion" value="4"><label for="star4">★</label>
                    <input type="radio" id="star3" name="calificacion" value="3"><label for="star3">★</label>
                    <input type="radio" id="star2" name="calificacion" value="2"><label for="star2">★</label>
                    <input type="radio" id="star1" name="calificacion" value="1"><label for="star1">★</label>
                </div>

                <label for="comentario">Comentario:</label>
                <textarea id="comentario" name="comentario" rows="4" required></textarea>

                <input type="hidden" id="producto_id_resena" name="producto_id_resena" required>

                <input type="submit" value="Enviar reseña">
            </form>
        </div>
    </div>

    <div id="modal-ver-pedidos" class="modal-pedido">
        <div class="modal-content modal-content-pedidos">
            <span class="close-modal" id="cerrar-modal-ver-pedidos">&times;</span>
            <h2 style="text-align:center;margin:18px 0 12px 0;">Lista de Pedidos</h2>
            <div id="contenedor-pedidos" style="max-height:400px;overflow-y:auto;padding:0 10px 18px 10px;"></div>
        </div>
    </div>

    <div id="modal-finalizar-compra" class="modal-pedido">
        <div class="modal-content">
            <span class="close-modal" id="cerrar-modal-finalizar-compra">&times;</span>
            <h2>Finalizar Compra</h2>
            <form id="form-finalizar-compra" action="php/finalizar_compra.php" method="POST">
                <div class="form-group">
                    <label for="cliente_nombre">Nombre:</label>
                    <input type="text" id="cliente_nombre" name="cliente_nombre" required>
                </div>
                <div class="form-group">
                    <label for="cliente_email">Email:</label>
                    <input type="email" id="cliente_email" name="cliente_email" required>
                </div>
                <div class="form-group">
                    <label for="cliente_telefono">Teléfono:</label>
                    <input type="text" id="cliente_telefono" name="cliente_telefono" required>
                </div>
                <div class="form-group">
                    <label for="cliente_direccion">Dirección:</label>
                    <input type="text" id="cliente_direccion" name="cliente_direccion" required>
                </div>
                <button type="submit" class="btn-pedido">Finalizar Compra</button>
            </form>
        </div>
    </div>

</body>

</html>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if(document.getElementById('carrito-bar')) {
        let btnFinalizar = document.createElement('button');
        btnFinalizar.textContent = 'Finalizar compra';
        btnFinalizar.className = 'btn-pedido';
        btnFinalizar.style.marginLeft = '16px';
        btnFinalizar.onclick = function() {
            document.getElementById('modal-finalizar-compra').style.display = 'block';
        };
        document.getElementById('carrito-bar').appendChild(btnFinalizar);
    }
    const cerrarFinalizar = document.getElementById('cerrar-modal-finalizar-compra');
    if(cerrarFinalizar) cerrarFinalizar.onclick = function() {
        document.getElementById('modal-finalizar-compra').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target === document.getElementById('modal-finalizar-compra')) {
            document.getElementById('modal-finalizar-compra').style.display = 'none';
        }
    };
});
</script>