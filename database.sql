-- Ejecutar en phpMyAdmin (http://localhost/phpmyadmin) -> pestaña SQL
CREATE DATABASE IF NOT EXISTS reservacion_eventos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE reservacion_eventos;

CREATE TABLE IF NOT EXISTS eventos (
    evento_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    fecha DATETIME NOT NULL,
    ubicacion VARCHAR(200) NOT NULL
);

CREATE TABLE IF NOT EXISTS clientes (
    cliente_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telefono VARCHAR(15)
);

-- Tabla intermedia: un cliente reserva cupos en un evento
CREATE TABLE IF NOT EXISTS reservaciones (
    reservacion_id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    cliente_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_reservacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservacion_evento FOREIGN KEY (evento_id) REFERENCES eventos(evento_id),
    CONSTRAINT fk_reservacion_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
    CONSTRAINT uq_evento_cliente UNIQUE (evento_id, cliente_id)
);

INSERT INTO eventos (nombre, descripcion, fecha, ubicacion) VALUES
('Concierto Sinfónico', 'Orquesta Sinfónica Nacional interpretando obras clásicas.', '2026-12-12 19:30:00', 'Quito, Teatro Sucre'),
('Feria de Tecnología', 'Exposición de startups, charlas y talleres de programación.', '2027-01-20 09:00:00', 'Guayaquil, Centro de Convenciones'),
('Taller de Fotografía', 'Taller práctico de fotografía de paisaje.', '2027-02-05 15:00:00', 'Cuenca, Parque Calderón');

INSERT INTO clientes (nombre, apellido, email, telefono) VALUES
('María', 'López', 'maria.lopez@mail.com', '0991234567'),
('Carlos', 'Pérez', 'carlos.perez@mail.com', '0987654321'),
('Ana', 'Torres', 'ana.torres@mail.com', '0974561230');

INSERT INTO reservaciones (evento_id, cliente_id, cantidad) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 3, 3);
