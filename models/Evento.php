<?php
require_once __DIR__ . "/../config/Database.php";

// Clase modelo Evento: encapsula atributos y operaciones CRUD
class Evento
{
    // Atributos privados (encapsulamiento)
    private ?int $id = null;
    private string $nombre = "";
    private string $descripcion = "";
    private string $fecha = "";
    private string $ubicacion = "";

    private PDO $db;
    private string $tabla = "eventos";

    public function __construct()
    {
        $this->db = (new Database())->conectar();
    }

    // ---------- Getters y Setters ----------
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): void { $this->nombre = trim($nombre); }

    public function getDescripcion(): string { return $this->descripcion; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = trim($descripcion); }

    public function getFecha(): string { return $this->fecha; }
    public function setFecha(string $fecha): void { $this->fecha = trim($fecha); }

    public function getUbicacion(): string { return $this->ubicacion; }
    public function setUbicacion(string $ubicacion): void { $this->ubicacion = trim($ubicacion); }

    // Fecha en el formato que espera <input type="datetime-local">
    public function getFechaInput(): string
    {
        return $this->fecha !== "" ? date("Y-m-d\TH:i", strtotime($this->fecha)) : "";
    }

    // ---------- Creación de eventos ----------
    public function crear(): bool
    {
        $sql = "INSERT INTO {$this->tabla} (nombre, descripcion, fecha, ubicacion)
                VALUES (:nombre, :descripcion, :fecha, :ubicacion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->datos());
    }

    // ---------- Lectura de eventos (con total de cupos reservados) ----------
    public function listar(string $buscar = ""): array
    {
        $sql = "SELECT e.*, COALESCE(SUM(r.cantidad), 0) AS reservados
                FROM {$this->tabla} e
                LEFT JOIN reservaciones r ON r.evento_id = e.evento_id";
        $params = [];
        if ($buscar !== "") {
            $sql .= " WHERE e.nombre LIKE :b OR e.ubicacion LIKE :b";
            $params[":b"] = "%$buscar%";
        }
        $sql .= " GROUP BY e.evento_id ORDER BY e.fecha";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ---------- Busqueda por id ----------
    public function buscarPorId(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE evento_id = :id");
        $stmt->execute([":id" => $id]);
        $fila = $stmt->fetch();
        if (!$fila) {
            return false;
        }
        $this->setId((int)$fila["evento_id"]);
        $this->setNombre($fila["nombre"]);
        $this->setDescripcion($fila["descripcion"] ?? "");
        $this->setFecha($fila["fecha"]);
        $this->setUbicacion($fila["ubicacion"]);
        return true;
    }

    // ---------- Actualizar Evento ----------
    public function actualizar(): bool
    {
        $sql = "UPDATE {$this->tabla}
                SET nombre = :nombre, descripcion = :descripcion, fecha = :fecha, ubicacion = :ubicacion
                WHERE evento_id = :id";
        $stmt = $this->db->prepare($sql);
        $datos = $this->datos();
        $datos[":id"] = $this->id;
        return $stmt->execute($datos);
    }

    // ---------- Borrar Evento (solo si no tiene reservaciones) ----------
    public function eliminar(int $id): bool
    {
        if ($this->tieneReservaciones($id)) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM {$this->tabla} WHERE evento_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    private function tieneReservaciones(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservaciones WHERE evento_id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetchColumn() > 0;
    }

    // ---------- Validaciones del Formulario ----------
    public function validar(): array
    {
        $errores = [];
        if ($this->nombre === "") $errores[] = "El nombre del evento es obligatorio.";
        if ($this->ubicacion === "") $errores[] = "La ubicación es obligatoria.";
        if ($this->fecha === "" || strtotime($this->fecha) === false) {
            $errores[] = "La fecha no es válida.";
        } elseif ($this->id === null && strtotime($this->fecha) < time()) {
            $errores[] = "La fecha de un evento nuevo no puede estar en el pasado.";
        }
        return $errores;
    }

    private function datos(): array
    {
        return [
            ":nombre"      => $this->nombre,
            ":descripcion" => $this->descripcion,
            ":fecha"       => date("Y-m-d H:i:s", strtotime($this->fecha)),
            ":ubicacion"   => $this->ubicacion,
        ];
    }
}
