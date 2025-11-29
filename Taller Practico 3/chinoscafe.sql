-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 21:44:36
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
-- Base de datos: `chinoscafe`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'xam', 'xam@gmail.com', '1111-0000', 'aiudaaaa', '2025-10-28 17:51:40'),
(2, 'hola', 'hola@gmail.com', 'hola', 'hola', '2025-10-30 20:18:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `supplier_id`, `code`, `name`, `description`, `price`, `stock`, `created_at`) VALUES
(1, 1, '1', 'lapis', 'un lapiz bien cool', 99.00, 2, '2025-10-28 17:51:19'),
(2, 1, '2', 'borradores', 'un boradorr', 55.00, 63, '2025-10-30 20:17:04'),
(3, 1, 'CF001', 'Café Americano', 'Café negro filtrado, tamaño mediano.', 1.75, 60, '2025-10-30 20:42:13'),
(4, 1, 'CF002', 'Café Expreso', 'Shot de café concentrado.', 1.50, 80, '2025-10-30 20:42:13'),
(5, 1, 'CF003', 'Capuccino', 'Café con leche vaporizada y espuma.', 2.25, 50, '2025-10-30 20:42:13'),
(6, 1, 'CF004', 'Latte Vainilla', 'Café espresso con leche vaporizada y toque de vainilla.', 2.50, 45, '2025-10-30 20:42:13'),
(7, 1, 'CF005', 'Moka Frappé', 'Café helado con chocolate y crema.', 3.00, 30, '2025-10-30 20:42:13'),
(8, 3, 'BJ001', 'Jugo de Naranja Natural', 'Jugo 100% natural sin azúcar añadido.', 1.80, 40, '2025-10-30 20:42:13'),
(9, 3, 'BJ002', 'Limonada con Hierbabuena', 'Bebida fría con limón y hierbabuena.', 1.90, 35, '2025-10-30 20:42:13'),
(10, 3, 'BJ003', 'Té Helado Durazno', 'Té negro con sabor a durazno.', 1.60, 50, '2025-10-30 20:42:13'),
(11, 3, 'BJ004', 'Batido de Fresa', 'Batido con leche entera y fresa natural.', 2.20, 25, '2025-10-30 20:42:13'),
(12, 4, 'SN001', 'Empanada de Pollo', 'Empanada artesanal rellena de pollo.', 1.25, 60, '2025-10-30 20:42:13'),
(13, 4, 'SN002', 'Panini de Jamón y Queso', 'Panini tostado con jamón y queso derretido.', 2.80, 40, '2025-10-30 20:42:13'),
(14, 5, 'SN003', 'Brownie de Chocolate', 'Porción de brownie casero.', 1.50, 50, '2025-10-30 20:42:13'),
(15, 5, 'SN004', 'Galletas de Avena', 'Galletas de avena y miel.', 1.00, 70, '2025-10-30 20:42:13'),
(16, 5, 'SN005', 'Cheesecake de Fresa', 'Porción de pastel de queso con fresa.', 2.50, 25, '2025-10-30 20:42:13'),
(17, 6, 'UT001', 'Vaso Desechable 12oz', 'Vaso térmico para bebidas calientes.', 0.10, 500, '2025-10-30 20:42:13'),
(18, 6, 'UT002', 'Servilleta Blanca (paquete 100)', 'Servilletas absorbentes para mesas.', 1.20, 100, '2025-10-30 20:42:13'),
(19, 6, 'UT003', 'Caja para Postres', 'Caja de cartón para llevar postres.', 0.50, 150, '2025-10-30 20:42:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `invoice_number`, `total`, `date`, `user_id`, `customer_name`) VALUES
(1, 'INV1761673947', 198.00, '2025-10-28 17:52:27', 1, 'juancho'),
(2, 'INV1761855522', 264.00, '2025-10-30 20:18:42', 1, 'jolge'),
(3, 'INV1761855554', 55.00, '2025-10-30 20:19:14', 1, ''),
(4, 'INV1761855669', 55.00, '2025-10-30 20:21:09', 1, 'jolge'),
(5, 'INV1761856048', 0.00, '2025-10-30 20:27:28', 1, ''),
(6, 'INV1761856176', 110.00, '2025-10-30 20:29:36', 2, 'una persona');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `qty`, `price`, `subtotal`) VALUES
(1, 1, 1, 2, 99.00, 198.00),
(2, 2, 2, 3, 55.00, 165.00),
(3, 2, 1, 1, 99.00, 99.00),
(4, 3, 2, 1, 55.00, 55.00),
(5, 4, 2, 1, 55.00, 55.00),
(6, 6, 2, 2, 55.00, 110.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'xam', '1111-0000', 'xam@gmail.com', 'un lugar', '2025-10-28 17:50:41'),
(2, 'Testers', '1234-5678', 'testings123@gmail.com', 'es espacio', '2025-10-30 20:17:40'),
(3, 'Distribuidora La Aroma', '6789-1234', 'contacto@laaroma.com', 'Calle 50, Ciudad de Panamá', '2025-10-30 20:41:41'),
(4, 'Lácteos del Valle', '6790-4433', 'ventas@lacteosdelvalle.com', 'Via Tocumen, Panamá', '2025-10-30 20:41:41'),
(5, 'Bebidas Tropicales S.A.', '6900-8822', 'pedidos@bebidastropicales.com', 'Avenida Balboa, Panamá', '2025-10-30 20:41:41'),
(6, 'Panadería El Buen Trigo', '6655-7721', 'panes@elbuentrigo.com', 'Calle Uruguay, Panamá', '2025-10-30 20:41:41'),
(7, 'Repostería Dulce Tentación', '6780-9988', 'ventas@dulcetentacion.com', 'San Miguelito, Panamá', '2025-10-30 20:41:41'),
(8, 'Suministros FoodService', '6987-3311', 'pedidos@foodservice.com', 'Via España, Panamá', '2025-10-30 20:41:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `created_at`) VALUES
(1, 'admin', '$2a$12$4jzrbOw.HXbX0jUllaKrTe.EFQu2NVbqrRQzuO7o4iTfJ5u2cF58G', 'Administrador', '2025-10-28 03:22:50'),
(2, 'Jorge', '$2a$12$5avaXSIEjALCJKKN4jvZD.iP3CSiYmrXmg7tMrOYX15CCuykRaAX2', 'Jorge Jimenez', '2025-10-30 20:29:07');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
