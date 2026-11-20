<?php
require_once "models/Cliente.php";
require_once "includes/funciones.php";

$cliente = new Cliente();
$buscar = trim($_GET["buscar"] ?? "");
$clientes = $cliente->listar($buscar);

$titulo = "Clientes";
$seccion = "clientes";
require "includes/header.php";
?>
<div class="contenedor">
    <h1>Gestión de Clientes</h1>

    <?= mensaje($_GET["msg"] ?? "") ?>

    <div class="barra">
        <form method="GET" class="buscador">
            <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email" value="<?= e($buscar) ?>">
            <button type="submit" class="btn">Buscar</button>
            <?php if ($buscar !== ""): ?><a href="clientes.php" class="btn gris">Limpiar</a><?php endif; ?>
        </form>
        <a href="cliente_form.php" class="btn verde">+ Nuevo cliente</a>
    </div>

    <table>
        <thead>
        <tr><th>#</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Teléfono</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php if (empty($clientes)): ?>
            <tr><td colspan="6" class="centro">No hay clientes registrados.</td></tr>
        <?php else: foreach ($clientes as $c): ?>
            <tr>
                <td><?= e($c["cliente_id"]) ?></td>
                <td><?= e($c["nombre"]) ?></td>
                <td><?= e($c["apellido"]) ?></td>
                <td><?= e($c["email"]) ?></td>
                <td><?= e($c["telefono"]) ?></td>
                <td class="acciones">
                    <a href="cliente_form.php?id=<?= $c["cliente_id"] ?>" class="btn azul">Editar</a>
                    <form method="POST" action="cliente_eliminar.php" onsubmit="return confirm('¿Eliminar este cliente?');">
                        <input type="hidden" name="id" value="<?= $c["cliente_id"] ?>">
                        <button type="submit" class="btn rojo">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php require "includes/footer.php"; ?>
