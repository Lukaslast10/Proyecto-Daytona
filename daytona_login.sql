-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-10-2026 a las 03:23:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `daytona_login`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id_acceso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_acceso` datetime DEFAULT current_timestamp(),
  `ip_usuario` varchar(45) NOT NULL,
  `resultado` varchar(20) NOT NULL DEFAULT 'exito'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accesos`
--

INSERT INTO `accesos` (`id_acceso`, `id_usuario`, `fecha_acceso`, `ip_usuario`, `resultado`) VALUES
(23, 5, '2026-10-18 22:14:06', '::1', 'exito'),
(24, 5, '2026-10-18 22:14:59', '::1', 'exito'),
(25, 5, '2026-10-18 22:17:02', '::1', 'fallido'),
(26, 7, '2026-10-18 22:20:19', '::1', 'exito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `titulo`, `descripcion`) VALUES
(1, '1. Introducción al Phishing', 'Conceptos básicos para entender qué es el phishing y cómo funciona.'),
(2, '2. Phishing por Correo Electrónico', 'Aprende a identificar correos falsos, enlaces engañosos y archivos adjuntos peligrosos.'),
(3, '3. Spear Phishing y Ataques Dirigidos', 'Cómo los atacantes investigan a los empleados para crear ataques personalizados.'),
(4, '4. Ingeniería Social y Prevención', 'Técnicas de manipulación psicológica y cómo reportar incidentes de seguridad.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `titulo_modulo` varchar(100) NOT NULL,
  `contenido` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `id_curso`, `titulo_modulo`, `contenido`) VALUES
(1, 1, '¿Qué es el Phishing?', 'El phishing es una técnica de engaño donde el atacante se hace pasar por una entidad de confianza...'),
(2, 1, 'El impacto en la empresa', 'Si un empleado cae en phishing, puede comprometer la red interna...'),
(3, 2, 'Analizando el remitente', 'No te fíes del nombre que aparece, revisa siempre la dirección de correo real...'),
(4, 2, 'Cuidado con los enlaces', 'Antes de hacer clic, pasa el cursor sobre el enlace para ver a dónde te dirige realmente...'),
(5, 3, 'Spear Phishing vs Phishing masivo', 'Mientras el masivo se envía a miles, el Spear Phishing apunta a ti usando tu nombre y datos personales...'),
(6, 4, 'Sentido de urgencia', 'Los atacantes siempre intentan que actúes rápido y con miedo. Mantén la calma y verifica.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'admin'),
(2, 'usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_rol` int(11) NOT NULL DEFAULT 2,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `clave`, `email`, `id_rol`, `fecha_registro`) VALUES
(5, 'admin', '$2y$10$KUkj/oxCrMGKY5yzKlFVYefYDPCDtge/5TU6t/Hmy7Pq6XHURcZW6', 'test@daytona.com', 1, '2026-10-19 01:13:53'),
(6, 'usuario', '$2y$10$4U3tknXqSnXvdT03wRD6OutVAje1v4Omg7e6JESm4iXgUDlzAvWue', 'usuario@gmail.com', 2, '2026-10-19 01:16:03'),
(7, 'admin2', '$2y$10$ZLyHibH1U/9kvFvpqBWvluELPGUSCixhBgXOFbiXNKhBjHZiJ5ZBK', 'admin@daytona.com', 1, '2026-10-19 01:19:58');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id_acceso`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id_acceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `accesos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
