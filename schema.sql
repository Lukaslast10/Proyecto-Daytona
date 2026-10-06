CREATE DATEBASE daytona_login.php; 
USE daytona_login.php;

-- tabla de roles
CREATE TABLE roles (
    id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) NOT NULL UNIQUE
);

-- roles del sistema
INSERT INTO roles (nombre_rol) VALUES ('admin');
INSERT INTO roles (nombre_rol) VALUES ('usuario');

-- tabla de usuarios
CREATE TABLE usuarios ( 
    id_usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    nombre_usuario VARCHAR(50) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    id_rol INT NOT NULL DEFAULT 2,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- tabla de accesos / auditoria de logs
CREATE TABLE accesos ( 
    id_acceso INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_acceso DATETIME DEFAULT CURRENT_TIMESTAMP, 
    ip_usuario VARCHAR(45) NOT NULL,
    resultado VARCHAR(20) NOT NULL DEFAULT 'exito'
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
-- avisen si agregan o sacan algo