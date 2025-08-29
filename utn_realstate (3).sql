-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-08-2025 a las 18:32:52
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
-- Base de datos: `utn_realstate`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `color_principal` varchar(20) DEFAULT 'azul',
  `color_secundario` varchar(20) DEFAULT 'amarillo',
  `icono_principal` varchar(255) DEFAULT NULL,
  `icono_blanco` varchar(255) DEFAULT NULL,
  `imagen_banner` varchar(255) DEFAULT NULL,
  `mensaje_banner` varchar(255) DEFAULT NULL,
  `quienes_somos` text DEFAULT NULL,
  `imagen_quienes_somos` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `img_quienes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `color_principal`, `color_secundario`, `icono_principal`, `icono_blanco`, `imagen_banner`, `mensaje_banner`, `quienes_somos`, `imagen_quienes_somos`, `facebook`, `instagram`, `youtube`, `direccion`, `telefono`, `email`, `img_quienes`) VALUES
(1, '#25344b', '#ffe600', NULL, NULL, NULL, 'Permitenos realizar tus cambios en nuestra empresa', '', NULL, '', '', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `nombre`, `email`, `telefono`, `mensaje`, `fecha`) VALUES
(1, 'Jefferson', 'rodriguezgonzalezjefferson@gmail.com', '85702873', 'son pruebas', '2025-08-26 03:31:39'),
(2, 'Jefferson', 'rodriguezgonzalezjefferson@gmail.com', '85702873', 'son pruebas', '2025-08-26 03:31:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

CREATE TABLE `propiedades` (
  `id` int(11) NOT NULL,
  `tipo` enum('alquiler','venta') NOT NULL,
  `destacada` tinyint(1) DEFAULT 0,
  `titulo` varchar(100) NOT NULL,
  `descripcion_breve` varchar(255) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `agente_id` int(11) NOT NULL,
  `imagen_destacada` varchar(255) DEFAULT NULL,
  `descripcion_larga` text DEFAULT NULL,
  `mapa` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`id`, `tipo`, `destacada`, `titulo`, `descripcion_breve`, `precio`, `agente_id`, `imagen_destacada`, `descripcion_larga`, `mapa`, `ubicacion`, `fecha_creacion`) VALUES
(1, 'alquiler', 1, 'Casa', '4 cuartos ', 130000.00, 1, 'img/prop_1756151308_580.jpg', 'Casa en buen estado ', '', 'Barrio las Palmas', '2025-08-19 06:06:34'),
(2, 'alquiler', 0, 'Prueba', '2 cuartos y baño ', 100000.00, 1, 'img/prop_1756151530_785.jpg', 'prueba', '', '', '2025-08-23 06:29:05'),
(4, 'venta', 0, 'Apartamento ', '2 cuartos ', 100000.00, 1, 'img/prop_1756151658_753.jpg', 'No se permiten mascotas ', '', '', '2025-08-24 23:00:10'),
(5, 'alquiler', 1, 'Casa 2 Plantas', 'Barrio lujoso en Cañas Guanacaste ', 250000.00, 1, 'img/prop_1756151426_520.jpg', '4 cuartos, 2 baños, cochera bajo techo', '', '', '2025-08-25 15:01:49'),
(6, 'alquiler', 1, 'Casa', 'Barrio lujoso en Cañas Guanacaste ', 15000000.00, 1, 'img/prop_1756134193_925.jpg', 'Casa lujosa en un barrio tranquilo, 4 cuartos,3 baños,cochera, piscina ', NULL, NULL, '2025-08-25 15:03:13'),
(7, 'venta', 0, 'Casa en la playa ', 'Casa en Playa Hermosa', 25000000.00, 1, 'img/prop_1756134315_116.jpg', '50 metros de la playa, ubicacion perfecta para turistas', NULL, NULL, '2025-08-25 15:05:15'),
(8, 'venta', 1, 'Casa de playa ', 'Casa en Playa Hermosa en residencial lujoso', 50000000.00, 3, 'img/prop_1756152289_996.jpg', 'se encuentra en el residencial mas lujoso de la zona, cuenta con detallados impresionantes ', NULL, NULL, '2025-08-25 20:04:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `privilegio` enum('administrador','agente') NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `color_principal` varchar(8) NOT NULL DEFAULT '#25344b',
  `color_secundario` varchar(8) NOT NULL DEFAULT '#ffe600'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `telefono`, `correo`, `email`, `usuario`, `contrasena`, `privilegio`, `imagen`, `color_principal`, `color_secundario`) VALUES
(1, 'Admin', '8890-2030', 'admin@utnrealstate.com', 'admin@utnrealstate.com', 'Admin', '$2y$10$ZhJgY8knL4iJ95dF2/NyM.oGt/3KQoy46NSK4kDY6m5ozyRYxchxC', 'administrador', NULL, '#25344b', '#ffe600'),
(2, 'Andres', '85702873', 'hhh@gmail.com', 'rodriguezgonzalezjefferson@gmail.com', 'Andres', '$2y$10$9jzXtgXAIwUsOKouwnTTC.zy8D7DMdFkvp4WFtgHwohgJzdeK49dm', 'agente', NULL, '#25344b', '#ffe600'),
(3, 'Jefferson', '8570-2873', 'hh2@gmail.com', 'compu2025178@gmail.com', 'Jefferson', '$2y$10$kEvWvj7Mi6oXYMj1o.jroeZkFngMee5bqOK1AuYP0REoqWcoO5cc6', 'agente', NULL, '#25344b', '#ffe600'),
(4, 'Andres', '86702873', 'hh2@gmail.com', '', 'Andres01', '$2y$10$UpDPqHelxtboFvlF2aNU9ukAe7LtFgEIISSBQu0KdlUSBHbZqwuoK', 'administrador', NULL, '#25344b', '#ffe600');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agente_id` (`agente_id`);

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
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
