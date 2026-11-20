<?php
require_once "models/Reservacion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["id"])) {
    $reservacion = new Reservacion();
    $reservacion->eliminar((int)$_POST["id"]);
    header("Location: reservaciones.php?msg=eliminado");
    exit;
}

header("Location: reservaciones.php");
exit;
