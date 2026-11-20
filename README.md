# Sistema de Reservación de Eventos – PHP POO + MySQL (XAMPP)

Aplicación web para **gestionar eventos, clientes y sus reservaciones**, desarrollada con **PHP orientado a objetos**, **PDO**, **MySQL** y **HTML/CSS**, ejecutada en **XAMPP**.

## Tecnologías

- PHP 8 (POO + PDO)
- MySQL / MariaDB (XAMPP)
- HTML5 y CSS3

## Estructura del proyecto

```
reservacion_eventos/
├── config/
│   └── Database.php          # Clase Database: conexión a MySQL con PDO
├── models/
│   ├── Evento.php            # Clase Evento: atributos, getters/setters y CRUD
│   ├── Cliente.php           # Clase Cliente: atributos, getters/setters y CRUD
│   └── Reservacion.php       # Clase Reservacion: relaciona clientes con eventos
├── includes/
│   ├── header.php            # Encabezado y menú de navegación
│   ├── footer.php            # Cierre del HTML
│   └── funciones.php         # Funciones auxiliares (escape, fechas, mensajes)
├── css/
│   └── estilos.css           # Estilos de la interfaz
├── index.php                 # Panel de control con resumen y próximos eventos
├── eventos.php               # Listado y búsqueda de eventos
├── evento_form.php           # Crear / editar evento
├── evento_eliminar.php       # Eliminar evento
├── clientes.php              # Listado y búsqueda de clientes
├── cliente_form.php          # Crear / editar cliente
├── cliente_eliminar.php      # Eliminar cliente
├── reservaciones.php         # Listado de reservaciones (filtro por evento)
├── reservacion_form.php      # Crear / editar reservación
├── reservacion_eliminar.php  # Cancelar reservación
├── database.sql              # Script de la base de datos con datos de prueba
└── README.md
```

## Modelo de datos

```
eventos (1) ──< reservaciones >── (1) clientes
```

**eventos**

| Campo | Tipo |
|---|---|
| evento_id | INT, PK, AUTO_INCREMENT |
| nombre | VARCHAR(120) |
| descripcion | TEXT |
| fecha | DATETIME |
| ubicacion | VARCHAR(200) |

**clientes**

| Campo | Tipo |
|---|---|
| cliente_id | INT, PK, AUTO_INCREMENT |
| nombre | VARCHAR(100) |
| apellido | VARCHAR(100) |
| email | VARCHAR(120), UNIQUE |
| telefono | VARCHAR(15) |

**reservaciones** (tabla intermedia)

| Campo | Tipo |
|---|---|
| reservacion_id | INT, PK, AUTO_INCREMENT |
| evento_id | INT, FK → eventos |
| cliente_id | INT, FK → clientes |
| cantidad | INT (número de entradas) |
| fecha_reservacion | TIMESTAMP |

Restricción `UNIQUE (evento_id, cliente_id)`: un cliente solo puede tener una reservación por evento.

## Programación Orientada a Objetos aplicada

| Concepto | Dónde se aplica |
|---|---|
| **Clases** | `Database`, `Evento`, `Cliente` y `Reservacion` |
| **Encapsulamiento** | Atributos `private`, accesibles mediante getters y setters |
| **Constructor** | Cada modelo abre la conexión en `__construct()` |
| **Métodos** | `crear()`, `listar()`, `buscarPorId()`, `actualizar()`, `eliminar()`, `validar()` |
| **Composición** | Los modelos utilizan un objeto `Database` para conectarse |
| **Atributo estático** | `Database::$conexion` reutiliza una sola conexión PDO |

## Funcionalidades

- **Eventos:** registrar, listar, buscar por nombre o ubicación, editar y eliminar.
- **Clientes:** registrar, listar, buscar por nombre o email, editar y eliminar.
- **Reservaciones:** reservar entradas de un evento para un cliente, filtrar por evento, editar y cancelar.
- **Panel de control:** totales de eventos, clientes, reservaciones y entradas, con la lista de próximos eventos.
- **Validaciones:**
  - Campos obligatorios, email válido y único, teléfono numérico.
  - Un evento nuevo no puede tener fecha pasada.
  - Entre 1 y 10 entradas por reservación; sin reservaciones duplicadas.
  - No se puede eliminar un evento o cliente que tenga reservaciones.
- **Seguridad:** consultas preparadas con PDO (contra inyección SQL), `htmlspecialchars` (contra XSS) y eliminación solo por POST.

## Instalación

1. Instalar [XAMPP](https://www.apachefriends.org/) e iniciar **Apache** y **MySQL**.
2. Copiar la carpeta `reservacion_eventos` en `C:\xampp\htdocs\`.
3. Abrir `http://localhost/phpmyadmin`, ir a la pestaña **SQL** y ejecutar el contenido de `database.sql`.
4. Si MySQL tiene contraseña, actualizarla en `config/Database.php`.
5. Abrir en el navegador: `http://localhost/reservacion_eventos/`

## Autora

Jhoana – Aplicaciones Web, Evaluación Parcial I
