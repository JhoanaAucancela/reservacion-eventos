<?php
require_once __DIR__ . "/../config/Database.php";

// Clase modelo Cliente: encapsula atributos y operaciones CRUD
class Cliente
{
    // Atributos privados (encapsulamiento)
    private ?int $id = null;
    private string $nombre = "";
    private string $apellido = "";
    private string $email = "";
    private string $telefono = "";

    private PDO $db;
    private string $tabla = "clientes";

    public function __construct()
    {
        $this->db = (new Database())->conectar();
    }

    // ---------- Getters y Setters ----------
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): void { $this->nombre = trim($nombre); }

    public function getApellido(): string { return $this->apellido; }
    public function setApellido(string $apellido): void { $this->apellido = trim($apellido); }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = trim($email); }

    public function getTelefono(): string { return $this->telefono; }
    public function setTelefono(string $telefono): void { $this->telefono = trim($telefono); }

    // ---------- Creación de clientes ----------
    public function crear(): bool
    {
        $sql = "INSERT INTO {$this->tabla} (nombre, apellido, email, telefono)
                VALUES (:nombre, :apellido, :email, :telefono)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->datos());
    }

    // ---------- Lectura de clientes ----------
    public function listar(string $buscar = ""): array
    {
        if ($buscar !== "") {
            $sql = "SELECT * FROM {$this->tabla}
                    WHERE nombre LIKE :b OR apellido LIKE :b OR email LIKE :b
                    ORDER BY apellido, nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([":b" => "%$buscar%"]);
        } else {
            $stmt = $this->db->query("SELECT * FROM {$this->tabla} ORDER BY apellido, nombre");
        }
        return $stmt->fetchAll();
    }

    // ---------- Busqueda por id ----------
    public function buscarPorId(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE cliente_id = :id");
        $stmt->execute([":id" => $id]);
        $fila = $stmt->fetch();
        if (!$fila) {
            return false;
        }
        $this->setId((int)$fila["cliente_id"]);
        $this->setNombre($fila["nombre"]);
        $this->setApellido($fila["apellido"]);
        $this->setEmail($fila["email"]);
        $this->setTelefono($fila["telefono"] ?? "");
        return true;
    }

    // ---------- Actualizar Cliente ----------
    public function actualizar(): bool
    {
        $sql = "UPDATE {$this->tabla}
                SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono
                WHERE cliente_id = :id";
        $stmt = $this->db->prepare($sql);
        $datos = $this->datos();
        $datos[":id"] = $this->id;
        return $stmt->execute($datos);
    }

    // ---------- Borrar Cliente (solo si no tiene reservaciones) ----------
    public function eliminar(int $id): bool
    {
        if ($this->tieneReservaciones($id)) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM {$this->tabla} WHERE cliente_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    private function tieneReservaciones(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservaciones WHERE cliente_id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetchColumn() > 0;
    }

    // ---------- Validaciones del Formulario ----------
    public function validar(): array
    {
        $errores = [];
        if ($this->nombre === "") $errores[] = "El nombre es obligatorio.";
        if ($this->apellido === "") $errores[] = "El apellido es obligatorio.";
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) $errores[] = "El email no es válido.";
        if ($this->telefono !== "" && !preg_match('/^\d{7,10}$/', $this->telefono)) $errores[] = "El teléfono debe tener entre 7 y 10 dígitos.";
        if ($this->emailExiste()) $errores[] = "Ya existe un cliente con ese email.";
        return $errores;
    }

    // ---------- Validación de Email Existente ----------
    private function emailExiste(): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->tabla} WHERE email = :e AND cliente_id <> :id");
        $stmt->execute([":e" => $this->email, ":id" => $this->id ?? 0]);
        return $stmt->fetchColumn() > 0;
    }

    private function datos(): array
    {
        return [
            ":nombre"   => $this->nombre,
            ":apellido" => $this->apellido,
            ":email"    => $this->email,
            ":telefono" => $this->telefono,
        ];
    }
}
