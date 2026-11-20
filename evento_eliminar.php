<?php
require_once "models/Evento.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["id"])) {
    $evento = new Evento();
    $ok = $evento->eliminar((int)$_POST["id"]);
    header("Location: eventos.php?msg=" . ($ok ? "eliminado" : "en_uso"));
    exit;
}

header("Location: eventos.php");
exit;
