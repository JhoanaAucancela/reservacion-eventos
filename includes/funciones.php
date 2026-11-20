<?php
// Escapa texto antes de mostrarlo en HTML (protección contra XSS)
function e($txt) { return htmlspecialchars((string)$txt, ENT_QUOTES, "UTF-8"); }

// Formatea una fecha de MySQL como dd/mm/aaaa hh:mm
function fecha($valor) { return date("d/m/Y H:i", strtotime($valor)); }

// Mensajes que se muestran después de redirigir con ?msg=
function mensaje(string $msg): string
{
    $mensajes = [
        "creado"      => ["exito", "Registro guardado correctamente."],
        "actualizado" => ["exito", "Registro actualizado correctamente."],
        "eliminado"   => ["exito", "Registro eliminado correctamente."],
        "en_uso"      => ["error", "No se puede eliminar: tiene reservaciones asociadas."],
    ];
    if (!isset($mensajes[$msg])) {
        return "";
    }
    [$tipo, $texto] = $mensajes[$msg];
    return "<div class=\"alerta $tipo\">$texto</div>";
}
