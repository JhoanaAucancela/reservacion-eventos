<?php
require_once "models/Evento.php";
require_once "includes/funciones.php";

$evento = new Evento();
$errores = [];
$esEdicion = false;

// Si viene un id por GET, cargamos el evento para editar
if (isset($_GET["id"])) {
    $esEdicion = $evento->buscarPorId((int)$_GET["id"]);
    if (!$esEdicion) {
        header("Location: eventos.php");
        exit;
    }
}

// Procesar envío del formulario (crear o actualizar)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = !empty($_POST["id"]) ? (int)$_POST["id"] : null;
    $esEdicion = $id !== null;

    $evento->setId($id);
    $evento->setNombre($_POST["nombre"] ?? "");
    $evento->setDescripcion($_POST["descripcion"] ?? "");
    $evento->setFecha($_POST["fecha"] ?? "");
    $evento->setUbicacion($_POST["ubicacion"] ?? "");

    $errores = $evento->validar();

    if (empty($errores)) {
        if ($esEdicion) {
            $evento->actualizar();
            header("Location: eventos.php?msg=actualizado");
        } else {
            $evento->crear();
            header("Location: eventos.php?msg=creado");
        }
        exit;
    }
}

$titulo = $esEdicion ? "Editar evento" : "Nuevo evento";
$seccion = "eventos";
require "includes/header.php";
?>
<div class="contenedor angosto">
    <h1><?= $titulo ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alerta error">
            <ul><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <input type="hidden" name="id" value="<?= e($evento->getId()) ?>">

        <label>Nombre *</label>
        <input type="text" name="nombre" maxlength="120" required value="<?= e($evento->getNombre()) ?>">

        <label>Descripción</label>
        <textarea name="descripcion" rows="3"><?= e($evento->getDescripcion()) ?></textarea>

        <label>Fecha y hora *</label>
        <input type="datetime-local" name="fecha" required value="<?= e($evento->getFechaInput()) ?>">

        <label>Ubicación *</label>
        <input type="text" name="ubicacion" maxlength="200" required value="<?= e($evento->getUbicacion()) ?>">

        <div class="botones">
            <button type="submit" class="btn verde"><?= $esEdicion ? "Actualizar" : "Guardar" ?></button>
            <a href="eventos.php" class="btn gris">Cancelar</a>
        </div>
    </form>
</div>
<?php require "includes/footer.php"; ?>
