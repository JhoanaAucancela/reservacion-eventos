<?php // Encabezado común: recibe $titulo y $seccion desde cada página ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Reservación de Eventos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<nav class="menu">
    <span class="marca">Reservación de Eventos</span>
    <a href="index.php" class="<?= $seccion === "inicio" ? "activo" : "" ?>">Inicio</a>
    <a href="eventos.php" class="<?= $seccion === "eventos" ? "activo" : "" ?>">Eventos</a>
    <a href="clientes.php" class="<?= $seccion === "clientes" ? "activo" : "" ?>">Clientes</a>
    <a href="reservaciones.php" class="<?= $seccion === "reservaciones" ? "activo" : "" ?>">Reservaciones</a>
</nav>
