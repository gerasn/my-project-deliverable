-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-03-2025 a las 13:27:42
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
-- Base de datos: `entregable`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre_cargo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre_cargo`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(1, 'GerSan', '$2y$10$URRmD4Gut6Hn83zTRomXCO91P88.NWjNBCfWjDfE4t1UeMV6PKgWS', 'zzzzzzzzzz@gmail.co');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `firma` longtext NOT NULL,
  `contrato` varchar(255) NOT NULL,
  `fecha_eliminacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo_electronico`, `id_rol`, `fecha_ingreso`, `firma`, `contrato`, `fecha_eliminacion`) VALUES
(1, 'ger', 'hhhh2344gmail.com', 1, '1994-05-22', '', 'Indefinido', NULL),
(2, 'gera', 'g123@gmail.com', 2, '2007-06-20', '', 'Indefinido', NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_usuarios` (
`usuario_id` int(11)
,`nombre` varchar(255)
,`correo` varchar(255)
,`cargo` varchar(255)
,`fecha_ingreso` date
,`dias_trabajados` int(7)
,`contrato` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `view_usuarios`
--
DROP TABLE IF EXISTS `view_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_usuarios`  AS SELECT `u`.`id` AS `usuario_id`, `u`.`nombre` AS `nombre`, `u`.`correo_electronico` AS `correo`, `r`.`nombre_cargo` AS `cargo`, `u`.`fecha_ingreso` AS `fecha_ingreso`, to_days(curdate()) - to_days(`u`.`fecha_ingreso`) AS `dias_trabajados`, `u`.`contrato` AS `contrato` FROM (`usuarios` `u` join `roles` `r` on(`u`.`id_rol` = `r`.`id`)) WHERE `u`.`fecha_eliminacion` is null ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
