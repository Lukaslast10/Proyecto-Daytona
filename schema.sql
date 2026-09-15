CREATE DATEBASE IF NOT EXISTS daytona_login.php; 
-- creamos la bd con el nombre "daytona_login"
USE daytona_login.php;

-- creamos las tablas

-- empleados que se registran o logean
CREATE TABLE usuarios ( -- datos necesarios - id, usuario, clave, email, perfil
    id INT AUTO_INCREMENT PRIMARY KEY, 
    usuario VARCHAR(40) UNIQUE NOT NULL,
    clave VARCHAR(200) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    perfil ENUM('usuario','administrador') NOT NULL DEFAULT 'usuario',
    -- fecha_registro ?
);

-- auditoria de los accesos
CREATE TABLE accesos ( -- datos necesarios - id, usuario, fecha
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(40) NOT NULL,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- necesario porque sino sabemos cuando pasó no auditamos nada
    resultado ENUM('existoso','fallido') NOT NULL
);
