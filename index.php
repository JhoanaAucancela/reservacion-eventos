<?php
require_once "models/Evento.php";
require_once "models/Cliente.php";
require_once "models/Reservacion.php";
require_once "includes/funciones.php";

$eventos = (new Evento())->listar();
$totalClientes = count((new Cliente())->listar());
$reservaciones = (new Reservacion())->listar();
$totalEntradas = array_sum(array_column($reservaciones, "cantidad"));

// Próximos eventos: los que aún no han ocurrido
$proximos = array_filter($eventos, fn($ev) => strtotime($ev["fecha"]) >= time());

$titulo = "Inicio";
$seccion = "inicio";
require "includes/header.php";
?>
<div class="contenedor">
    <h1>Panel de control</h1>

    <div class="tarjetas">
        <a href="eventos.php" class="tarjeta"><div class="numero"><?= count($eventos) ?></div>Eventos</a>
        <a href="clientes.php" class="tarjeta"><div class="numero"><?= $totalClientes ?></div>Clientes</a>
        <a href="reservaciones.php" class="tarjeta"><div class="numero"><?= count($reservaciones) ?></div>Reservaciones</a>
        <div class="tarjeta"><div class="numero"><?= $totalEntradas ?></div>Entradas reservadas</div>
    </div>

    <h2>Próximos eventos</h2>
    <table>
        <thead>
        <tr><th>Evento</th><th>Fecha</th><th>Ubicación</th><th>Entradas reservadas</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($proximos)): ?>
            <tr><td colspan="5" class="centro">No hay eventos próximos.</td></tr>
        <?php else: foreach ($proximos as $ev): ?>
            <tr>
                <td><?= e($ev["nombre"]) ?></td>
                <td><?= fecha($ev["fecha"]) ?></td>
                <td><?= e($ev["ubicacion"]) ?></td>
                <td><span class="chip"><?= (int)$ev["reservados"] ?></span></td>
                <td><a href="reservaciones.php?evento=<?= $ev["evento_id"] ?>" class="btn azul">Ver reservaciones</a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php require "includes/footer.php"; ?>
