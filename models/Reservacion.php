<?php
require_once __DIR__ . "/../config/Database.php";

// Clase modelo Reservacion: relaciona un Cliente con un Evento
class Reservacion
{
    // Atributos privados (encapsulamiento)
    private ?int $id = null;
    private int $eventoId = 0;
    private int $clienteId = 0;
    private int $cantidad = 1;

    private PDO $db;
    private string $tabla = "reservaciones";

    public function __construct()
    {
        $this->db = (new Database())->conectar();
    }

    // ---------- Getters y Setters ----------
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getEventoId(): int { return $this->eventoId; }
    public function setEventoId(int $eventoId): void { $this->eventoId = $eventoId; }

    public function getClienteId(): int { return $this->clienteId; }
    public function setClienteId(int $clienteId): void { $this->clienteId = $clienteId; }

    public function getCantidad(): int { return $this->cantidad; }
    public function setCantidad(int $cantidad): void { $this->cantidad = $cantidad; }

    // ---------- Creación de reservaciones ----------
    public function crear(): bool
    {
        $sql = "INSERT INTO {$this->tabla} (evento_id, cliente_id, cantidad)
                VALUES (:evento, :cliente, :cantidad)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->datos());
    }

    // ---------- Lectura de reservaciones (JOIN con eventos y clientes) ----------
    public function listar(int $eventoId = 0): array
    {
        $sql = "SELECT r.reservacion_id, r.cantidad, r.fecha_reservacion,
                       e.evento_id, e.nombre AS evento, e.fecha, e.ubicacion,
                       c.cliente_id, CONCAT(c.nombre, ' ', c.apellido) AS cliente, c.email
                FROM {$this->tabla} r
                INNER JOIN eventos e ON e.evento_id = r.evento_id
                INNER JOIN clientes c ON c.cliente_id = r.cliente_id";
        $params = [];
        if ($eventoId > 0) {
            $sql .= " WHERE r.evento_id = :evento";
            $params[":evento"] = $eventoId;
        }
        $sql .= " ORDER BY e.fecha, cliente";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ---------- Busqueda por id ----------
    public function buscarPorId(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE reservacion_id = :id");
        $stmt->execute([":id" => $id]);
        $fila = $stmt->fetch();
        if (!$fila) {
            return false;
        }
        $this->setId((int)$fila["reservacion_id"]);
        $this->setEventoId((int)$fila["evento_id"]);
        $this->setClienteId((int)$fila["cliente_id"]);
        $this->setCantidad((int)$fila["cantidad"]);
        return true;
    }

    // ---------- Actualizar Reservación ----------
    public function actualizar(): bool
    {
        $sql = "UPDATE {$this->tabla}
                SET evento_id = :evento, cliente_id = :cliente, cantidad = :cantidad
                WHERE reservacion_id = :id";
        $stmt = $this->db->prepare($sql);
        $datos = $this->datos();
        $datos[":id"] = $this->id;
        return $stmt->execute($datos);
    }

    // ---------- Cancelar (borrar) Reservación ----------
    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabla} WHERE reservacion_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    // ---------- Validaciones del Formulario ----------
    public function validar(): array
    {
        $errores = [];
        if (!$this->existe("eventos", "evento_id", $this->eventoId)) $errores[] = "Seleccione un evento válido.";
        if (!$this->existe("clientes", "cliente_id", $this->clienteId)) $errores[] = "Seleccione un cliente válido.";
        if ($this->cantidad < 1 || $this->cantidad > 10) $errores[] = "La cantidad de entradas debe estar entre 1 y 10.";
        if ($this->duplicada()) $errores[] = "Este cliente ya tiene una reservación para ese evento.";
        return $errores;
    }

    private function existe(string $tabla, string $campo, int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM $tabla WHERE $campo = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetchColumn() > 0;
    }

    // Un cliente solo puede tener una reservación por evento
    private function duplicada(): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->tabla}
                WHERE evento_id = :evento AND cliente_id = :cliente AND reservacion_id <> :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":evento" => $this->eventoId, ":cliente" => $this->clienteId, ":id" => $this->id ?? 0]);
        return $stmt->fetchColumn() > 0;
    }

    private function datos(): array
    {
        return [
            ":evento"   => $this->eventoId,
            ":cliente"  => $this->clienteId,
            ":cantidad" => $this->cantidad,
        ];
    }
}
