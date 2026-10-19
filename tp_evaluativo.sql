-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2026 a las 20:24:24
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
-- Base de datos: `tp_evaluativo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `DNI` int(8) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Telefono` varchar(50) NOT NULL,
  `Año` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`DNI`, `Nombre`, `Apellido`, `Telefono`, `Año`) VALUES
(12345678, 'manuel', ' de la cruz', '55555555', '2026-07-10'),
(12364577, 'pedroel', 'mascapo', '6456546546', '2026-07-28'),
(21364578, 'julio', 'roca', '456456456', '2035-07-26'),
(98765432, 'fidel', 'castro', '1324678954', '2026-07-12'),
(98764321, 'mirko', 'de marley', '456321654', '2026-07-05'),
(13456789, 'belen', 'extra', '42372456', '2026-07-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `DNI` int(8) NOT NULL,
  `Materia` varchar(50) NOT NULL,
  `Año` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`DNI`, `Materia`, `Año`) VALUES
(98764321, 'Matematica', '2026-07-05'),
(12364577, 'base de datos', '2026-07-28'),
(21364578, 'base de datos 2', '2035-07-26'),
(98765432, 'lengua 2', '2026-07-12'),
(13456789, 'lengua 3', '2026-07-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `DNI` int(8) NOT NULL,
  `Nota` int(50) NOT NULL,
  `Año` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`DNI`, `Nota`, `Año`) VALUES
(12345678, 2, '2026-07-12'),
(12364577, 34, '2026-07-28'),
(21364578, 5, '2035-07-26'),
(98765432, 6, '2026-07-12'),
(98764321, 7, '2026-07-05'),
(13456789, 1, '2026-07-29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD KEY `Año` (`Año`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD KEY `Año` (`Año`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD KEY `Año` (`Año`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`Año`) REFERENCES `notas` (`Año`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`Año`) REFERENCES `alumnos` (`Año`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
