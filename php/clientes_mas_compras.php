<?php
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes y cantidad de compras</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div style="max-width: 800px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); padding: 32px 24px;">
        <h2 style="color: #1976d2; text-align: center; margin-bottom: 24px;">Clientes y cantidad de compras</h2>
        <table style="width:100%; border-collapse: collapse;">
            <tr style="background: #1976d2; color: #fff;">
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Número de compras</th>
            </tr>
            <?php
            $sql = "SELECT cl.nombre, cl.email, cl.telefono, cl.direccion, COUNT(c.id) AS num_compras
                    FROM clientes cl
                    LEFT JOIN compras c ON cl.id = c.cliente_id
                    GROUP BY cl.id
                    ORDER BY num_compras DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo '<tr style="border-bottom: 1px solid #e0e0e0;">';
                echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['telefono']) . '</td>';
                echo '<td>' . htmlspecialchars($row['direccion']) . '</td>';
                echo '<td style="text-align:center; font-weight:bold; color:#1976d2;">' . $row['num_compras'] . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
        <div style="text-align:center; margin-top:24px;">
            <a href="../index.php" class="btn-pedido-nav">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
