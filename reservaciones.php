<?php
require_once "models/Reservacion.php";
require_once "models/Evento.php";
require_once "includes/funciones.php";

$reservacion = new Reservacion();
$eventoId = (int)($_GET["evento"] ?? 0);
$reservaciones = $reservacion->listar($eventoId);
$eventos = (new Evento())->listar();

$titulo = "Reservaciones";
$seccion = "reservaciones";
require "includes/header.php";
?>
<div class="contenedor">
    <h1>Gestión de Reservaciones</h1>

    <?= mensaje($_GET["msg"] ?? "") ?>

    <div class="barra">
        <form method="GET" class="buscador">
            <select name="evento" onchange="this.form.submit()">
                <option value="0">Todos los eventos</option>
                <?php foreach ($eventos as $ev): ?>
                    <option value="<?= $ev["evento_id"] ?>" <?= $ev["evento_id"] == $eventoId ? "selected" : "" ?>>
                        <?= e($ev["nombre"]) ?> (<?= fecha($ev["fecha"]) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn">Filtrar</button></noscript>
        </form>
        <a href="reservacion_form.php<?= $eventoId ? "?evento=$eventoId" : "" ?>" class="btn verde">+ Nueva reservación</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th><th>Evento</th><th>Fecha del evento</th><th>Cliente</th>
            <th>Entradas</th><th>Reservado el</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($reservaciones)): ?>
            <tr><td colspan="7" class="centro">No hay reservaciones registradas.</td></tr>
        <?php else: foreach ($reservaciones as $r): ?>
            <tr>
                <td><?= e($r["reservacion_id"]) ?></td>
                <td><?= e($r["evento"]) ?><br><span class="tenue"><?= e($r["ubicacion"]) ?></span></td>
                <td><?= fecha($r["fecha"]) ?></td>
                <td><?= e($r["cliente"]) ?><br><span class="tenue"><?= e($r["email"]) ?></span></td>
                <td><span class="chip"><?= (int)$r["cantidad"] ?></span></td>
                <td><?= fecha($r["fecha_reservacion"]) ?></td>
                <td class="acciones">
                    <a href="reservacion_form.php?id=<?= $r["reservacion_id"] ?>" class="btn azul">Editar</a>
                    <form method="POST" action="reservacion_eliminar.php" onsubmit="return confirm('¿Cancelar esta reservación?');">
                        <input type="hidden" name="id" value="<?= $r["reservacion_id"] ?>">
                        <button type="submit" class="btn rojo">Cancelar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php require "includes/footer.php"; ?>
