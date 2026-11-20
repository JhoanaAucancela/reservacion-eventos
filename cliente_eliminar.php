<?php
require_once "models/Cliente.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["id"])) {
    $cliente = new Cliente();
    $ok = $cliente->eliminar((int)$_POST["id"]);
    header("Location: clientes.php?msg=" . ($ok ? "eliminado" : "en_uso"));
    exit;
}

header("Location: clientes.php");
exit;
