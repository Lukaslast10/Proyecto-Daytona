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
