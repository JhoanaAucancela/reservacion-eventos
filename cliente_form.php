<?php
require_once "models/Cliente.php";
require_once "includes/funciones.php";

$cliente = new Cliente();
$errores = [];
$esEdicion = false;

// Si viene un id por GET, cargamos el cliente para editar
if (isset($_GET["id"])) {
    $esEdicion = $cliente->buscarPorId((int)$_GET["id"]);
    if (!$esEdicion) {
        header("Location: clientes.php");
        exit;
    }
}

// Procesar envío del formulario (crear o actualizar)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = !empty($_POST["id"]) ? (int)$_POST["id"] : null;
    $esEdicion = $id !== null;

    $cliente->setId($id);
    $cliente->setNombre($_POST["nombre"] ?? "");
    $cliente->setApellido($_POST["apellido"] ?? "");
    $cliente->setEmail($_POST["email"] ?? "");
    $cliente->setTelefono($_POST["telefono"] ?? "");

    $errores = $cliente->validar();

    if (empty($errores)) {
        if ($esEdicion) {
            $cliente->actualizar();
            header("Location: clientes.php?msg=actualizado");
        } else {
            $cliente->crear();
            header("Location: clientes.php?msg=creado");
        }
        exit;
    }
}

$titulo = $esEdicion ? "Editar cliente" : "Nuevo cliente";
$seccion = "clientes";
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
        <input type="hidden" name="id" value="<?= e($cliente->getId()) ?>">

        <label>Nombre *</label>
        <input type="text" name="nombre" maxlength="100" required value="<?= e($cliente->getNombre()) ?>">

        <label>Apellido *</label>
        <input type="text" name="apellido" maxlength="100" required value="<?= e($cliente->getApellido()) ?>">

        <label>Email *</label>
        <input type="email" name="email" maxlength="120" required value="<?= e($cliente->getEmail()) ?>">

        <label>Teléfono</label>
        <input type="text" name="telefono" maxlength="10" value="<?= e($cliente->getTelefono()) ?>">

        <div class="botones">
            <button type="submit" class="btn verde"><?= $esEdicion ? "Actualizar" : "Guardar" ?></button>
            <a href="clientes.php" class="btn gris">Cancelar</a>
        </div>
    </form>
</div>
<?php require "includes/footer.php"; ?>
