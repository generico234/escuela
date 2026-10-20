-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2026 a las 22:26:06
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
-- Base de datos: `safefood`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `id_lugar` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `id_lugar`, `id_usuario`, `comentario`, `creado_en`) VALUES
(6, 1, 3, 'Hola', '2026-10-02 13:09:32'),
(7, 1, 2, 'Chau', '2026-10-02 13:09:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intolerancias`
--

CREATE TABLE `intolerancias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `intolerancias`
--

INSERT INTO `intolerancias` (`id`, `nombre`) VALUES
(3, 'Sin frutos secos'),
(1, 'Sin gluten'),
(4, 'Sin huevo'),
(2, 'Sin lactosa'),
(6, 'Vegano'),
(5, 'Vegetariano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugares`
--

CREATE TABLE `lugares` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `latitud` decimal(10,7) NOT NULL,
  `longitud` decimal(10,7) NOT NULL,
  `horario` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lugares`
--

INSERT INTO `lugares` (`id`, `id_empresa`, `nombre`, `latitud`, `longitud`, `horario`, `descripcion`) VALUES
(1, NULL, 'Prueba', -38.2704670, -57.8397466, '21:00 20:59', 'Prueba de descripción');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugar_intolerancia`
--

CREATE TABLE `lugar_intolerancia` (
  `id_lugar` int(11) NOT NULL,
  `id_intolerancia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lugar_intolerancia`
--

INSERT INTO `lugar_intolerancia` (`id_lugar`, `id_intolerancia`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'usuario',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasena`, `rol`, `creado_en`) VALUES
(1, 'ChatGPT', '$2y$10$qrp5Nv8G9SP7qsD6nFHN6OXNSQczXGy4wfDlmZBgmgVIuOD9/O.dG', 'admin', '2026-09-25 19:47:39'),
(2, 'Hola', '$2y$10$ycLsWGeS1Xrnb4ZSD7bwj.FQtdykS2qLOmYgyqIJMxETYyFLTkezW', 'usuario', '2026-10-02 12:13:23'),
(3, 'Chau', '$2y$10$JIb85HPmaXzi2t9g.h1b3uTH1zzLJVh0DhXp0lmtR1ZOk49hYlpN6', 'usuario', '2026-10-02 12:20:02'),
(4, 'Prueba Empresa', '$2y$10$UFJIfprGM/ScniXMd8.Q8OItLfD69b2qy4YDM14peDugBCyE5OXZ6', 'empresa', '2026-10-02 19:06:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lugar` (`id_lugar`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `intolerancias`
--
ALTER TABLE `intolerancias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `lugares`
--
ALTER TABLE `lugares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lugares_empresa` (`id_empresa`);

--
-- Indices de la tabla `lugar_intolerancia`
--
ALTER TABLE `lugar_intolerancia`
  ADD PRIMARY KEY (`id_lugar`,`id_intolerancia`),
  ADD KEY `id_intolerancia` (`id_intolerancia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `intolerancias`
--
ALTER TABLE `intolerancias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `lugares`
--
ALTER TABLE `lugares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_lugar`) REFERENCES `lugares` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lugares`
--
ALTER TABLE `lugares`
  ADD CONSTRAINT `fk_lugares_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `lugar_intolerancia`
--
ALTER TABLE `lugar_intolerancia`
  ADD CONSTRAINT `lugar_intolerancia_ibfk_1` FOREIGN KEY (`id_lugar`) REFERENCES `lugares` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lugar_intolerancia_ibfk_2` FOREIGN KEY (`id_intolerancia`) REFERENCES `intolerancias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
