CREATE DATABASE daytona_login; 
USE daytona_login;

-- ==========================================
-- 1. CREACIÓN DE TABLAS
-- ==========================================

-- tabla de roles
CREATE TABLE roles (
    id_rol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) NOT NULL UNIQUE
);

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
    resultado VARCHAR(20) NOT NULL DEFAULT 'exito',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Crear tabla de cursos
CREATE TABLE cursos (
    id_curso INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL
);

-- Crear tabla de módulos
CREATE TABLE modulos (
    id_modulo INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    titulo_modulo VARCHAR(100) NOT NULL,
    contenido TEXT NOT NULL,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso)
);

-- ==========================================
-- 2. INSERCIÓN DE DATOS POR DEFECTO
-- ==========================================

-- roles del sistema
INSERT INTO roles (nombre_rol) VALUES ('admin');
INSERT INTO roles (nombre_rol) VALUES ('usuario');

-- Insertar los 4 cursos
INSERT INTO cursos (titulo, descripcion) VALUES 
('1. Introducción al Phishing', 'Conceptos básicos para entender qué es el phishing y cómo funciona.'),
('2. Phishing por Correo Electrónico', 'Aprende a identificar correos falsos, enlaces engañosos y archivos adjuntos peligrosos.'),
('3. Spear Phishing y Ataques Dirigidos', 'Cómo los atacantes investigan a los empleados para crear ataques personalizados.'),
('4. Ingeniería Social y Prevención', 'Técnicas de manipulación psicológica y cómo reportar incidentes de seguridad.');

-- Insertar algunos módulos de ejemplo para los cursos
INSERT INTO modulos (id_curso, titulo_modulo, contenido) VALUES 
(1, '¿Qué es el Phishing?', 'El phishing es una técnica de engaño donde el atacante se hace pasar por una entidad de confianza...'),
(1, 'El impacto en la empresa', 'Si un empleado cae en phishing, puede comprometer la red interna...'),
(2, 'Analizando el remitente', 'No te fíes del nombre que aparece, revisa siempre la dirección de correo real...'),
(2, 'Cuidado con los enlaces', 'Antes de hacer clic, pasa el cursor sobre el enlace para ver a dónde te dirige realmente...'),
(3, 'Spear Phishing vs Phishing masivo', 'Mientras el masivo se envía a miles, el Spear Phishing apunta a ti usando tu nombre y datos personales...'),
(4, 'Sentido de urgencia', 'Los atacantes siempre intentan que actúes rápido y con miedo. Mantén la calma y verifica.');
