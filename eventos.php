<?php
require_once "models/Evento.php";
require_once "includes/funciones.php";

$evento = new Evento();
$buscar = trim($_GET["buscar"] ?? "");
$eventos = $evento->listar($buscar);

$titulo = "Eventos";
$seccion = "eventos";
require "includes/header.php";
?>
<div class="contenedor">
    <h1>Gestión de Eventos</h1>

    <?= mensaje($_GET["msg"] ?? "") ?>

    <div class="barra">
        <form method="GET" class="buscador">
            <input type="text" name="buscar" placeholder="Buscar por nombre o ubicación" value="<?= e($buscar) ?>">
            <button type="submit" class="btn">Buscar</button>
            <?php if ($buscar !== ""): ?><a href="eventos.php" class="btn gris">Limpiar</a><?php endif; ?>
        </form>
        <a href="evento_form.php" class="btn verde">+ Nuevo evento</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th><th>Nombre</th><th>Descripción</th><th>Fecha</th>
            <th>Ubicación</th><th>Reservados</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($eventos)): ?>
            <tr><td colspan="7" class="centro">No hay eventos registrados.</td></tr>
        <?php else: foreach ($eventos as $ev): ?>
            <tr>
                <td><?= e($ev["evento_id"]) ?></td>
                <td><?= e($ev["nombre"]) ?></td>
                <td class="tenue"><?= e($ev["descripcion"]) ?></td>
                <td><?= fecha($ev["fecha"]) ?></td>
                <td><?= e($ev["ubicacion"]) ?></td>
                <td><a href="reservaciones.php?evento=<?= $ev["evento_id"] ?>" class="chip"><?= (int)$ev["reservados"] ?></a></td>
                <td class="acciones">
                    <a href="evento_form.php?id=<?= $ev["evento_id"] ?>" class="btn azul">Editar</a>
                    <form method="POST" action="evento_eliminar.php" onsubmit="return confirm('¿Eliminar este evento?');">
                        <input type="hidden" name="id" value="<?= $ev["evento_id"] ?>">
                        <button type="submit" class="btn rojo">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php require "includes/footer.php"; ?>
