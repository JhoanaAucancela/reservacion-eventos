<?php
require_once "models/Reservacion.php";
require_once "models/Evento.php";
require_once "models/Cliente.php";
require_once "includes/funciones.php";

$reservacion = new Reservacion();
$errores = [];
$esEdicion = false;

// Si viene un id por GET, cargamos la reservación para editar
if (isset($_GET["id"])) {
    $esEdicion = $reservacion->buscarPorId((int)$_GET["id"]);
    if (!$esEdicion) {
        header("Location: reservaciones.php");
        exit;
    }
} elseif (isset($_GET["evento"])) {
    // Evento preseleccionado desde el filtro del listado
    $reservacion->setEventoId((int)$_GET["evento"]);
}

// Procesar envío del formulario (crear o actualizar)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = !empty($_POST["id"]) ? (int)$_POST["id"] : null;
    $esEdicion = $id !== null;

    $reservacion->setId($id);
    $reservacion->setEventoId((int)($_POST["evento_id"] ?? 0));
    $reservacion->setClienteId((int)($_POST["cliente_id"] ?? 0));
    $reservacion->setCantidad((int)($_POST["cantidad"] ?? 0));

    $errores = $reservacion->validar();

    if (empty($errores)) {
        if ($esEdicion) {
            $reservacion->actualizar();
            header("Location: reservaciones.php?msg=actualizado");
        } else {
            $reservacion->crear();
            header("Location: reservaciones.php?msg=creado");
        }
        exit;
    }
}

// Listas para los <select>
$eventos = (new Evento())->listar();
$clientes = (new Cliente())->listar();

$titulo = $esEdicion ? "Editar reservación" : "Nueva reservación";
$seccion = "reservaciones";
require "includes/header.php";
?>
<div class="contenedor angosto">
    <h1><?= $titulo ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alerta error">
            <ul><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if (empty($eventos) || empty($clientes)): ?>
        <div class="alerta error">Debe registrar al menos un evento y un cliente antes de crear reservaciones.</div>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <input type="hidden" name="id" value="<?= e($reservacion->getId()) ?>">

        <label>Evento *</label>
        <select name="evento_id" required>
            <option value="">-- Seleccione un evento --</option>
            <?php foreach ($eventos as $ev): ?>
                <option value="<?= $ev["evento_id"] ?>" <?= $ev["evento_id"] == $reservacion->getEventoId() ? "selected" : "" ?>>
                    <?= e($ev["nombre"]) ?> – <?= fecha($ev["fecha"]) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Cliente *</label>
        <select name="cliente_id" required>
            <option value="">-- Seleccione un cliente --</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c["cliente_id"] ?>" <?= $c["cliente_id"] == $reservacion->getClienteId() ? "selected" : "" ?>>
                    <?= e($c["apellido"] . ", " . $c["nombre"]) ?> (<?= e($c["email"]) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label>Número de entradas *</label>
        <input type="number" name="cantidad" min="1" max="10" required value="<?= e($reservacion->getCantidad()) ?>">

        <div class="botones">
            <button type="submit" class="btn verde"><?= $esEdicion ? "Actualizar" : "Reservar" ?></button>
            <a href="reservaciones.php" class="btn gris">Cancelar</a>
        </div>
    </form>
</div>
<?php require "includes/footer.php"; ?>
