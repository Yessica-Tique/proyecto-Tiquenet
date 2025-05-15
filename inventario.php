<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

// Agregar nuevo producto
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, tipo, cantidad, precio) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $tipo, $cantidad, $precio]);

    header("Location: inventario.php");
    exit();
}

// Actualizar producto
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, tipo = ?, cantidad = ?, precio = ? WHERE id = ?");
    $stmt->execute([$nombre, $tipo, $cantidad, $precio, $id]);

    header("Location: inventario.php");
    exit();
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: inventario.php");
    exit();
}

// Cargar productos
$productos = $conexion->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);

// Cargar producto a editar si corresponde
$productoEditar = null;
if (isset($_GET['editar'])) {
    $idEditar = $_GET['editar'];

    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$idEditar]);
    $productoEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Tiquenet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        input {
            width: calc(25% - 12px);
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        .logout a {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .logout a:hover {
            background: #c82333;
        }
    </style>
    <script>
        function confirmarEliminacion() {
            return confirm('¿Estás seguro de que deseas eliminar este producto?');
        }
        function validarFormularioAgregar() {
            // Aquí puedes meter validaciones si quieres antes de enviar
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Cerrar sesión</a>
        </div>
        <h2>Inventario de Productos</h2>

        <h3><?= $productoEditar ? "Editar Producto" : "Agregar Producto" ?></h3>
        <form method="POST" action="" onsubmit="return validarFormularioAgregar()">
            <?php if ($productoEditar): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($productoEditar['id']) ?>">
            <?php endif; ?>

            <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="<?= $productoEditar ? htmlspecialchars($productoEditar['nombre']) : '' ?>" required>
            <input type="text" id="tipo" name="tipo" placeholder="Tipo" value="<?= $productoEditar ? htmlspecialchars($productoEditar['tipo']) : '' ?>" required>
            <input type="number" id="cantidad" name="cantidad" placeholder="Cantidad" value="<?= $productoEditar ? htmlspecialchars($productoEditar['cantidad']) : '' ?>" required>
            <input type="number" step="0.01" id="precio" name="precio" placeholder="Precio" value="<?= $productoEditar ? htmlspecialchars($productoEditar['precio']) : '' ?>" required>

            <button type="submit" name="<?= $productoEditar ? 'actualizar' : 'agregar' ?>">
                <?= $productoEditar ? 'Actualizar' : 'Agregar' ?>
            </button>
        </form>

        <h3>Lista de Productos</h3>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['tipo']) ?></td>
                    <td><?= htmlspecialchars($producto['cantidad']) ?></td>
                    <td>$<?= number_format($producto['precio'], 2) ?></td>
                    <td>
                        <a href="inventario.php?editar=<?= $producto['id'] ?>">Editar</a> |
                        <a href="inventario.php?eliminar=<?= $producto['id'] ?>" onclick="return confirmarEliminacion()">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
