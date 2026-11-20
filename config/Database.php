<?php
// Clase de conexión a MySQL (XAMPP) usando PDO
class Database
{
    private string $host = "localhost";
    private string $dbName = "reservacion_eventos";
    private string $user = "root";      // usuario por defecto de XAMPP
    private string $password = "";      // contraseña por defecto de XAMPP
    private static ?PDO $conexion = null;

    public function conectar(): PDO
    {
        // Se reutiliza una sola conexión para todos los modelos
        if (self::$conexion === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
                self::$conexion = new PDO($dsn, $this->user, $this->password);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conexion;
    }
}
