-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 07-06-2025 a las 16:57:07
-- Versión del servidor: 10.11.10-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u403921824_GestionProyect`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anteproyectos`
--

CREATE TABLE `anteproyectos` (
  `id_anteproyecto` int(11) NOT NULL,
  `codigo_anteproyecto` varchar(6) NOT NULL,
  `titulo_anteproyecto` varchar(255) NOT NULL,
  `palabras_claves` varchar(255) DEFAULT NULL,
  `id_facultad` int(11) NOT NULL,
  `id_programa` int(11) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Revisión',
  `modalidad` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Asignar_asesor_anteproyecto_proyecto`
--

CREATE TABLE `Asignar_asesor_anteproyecto_proyecto` (
  `id_asignacion` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignar_estudiante_anteproyecto`
--

CREATE TABLE `asignar_estudiante_anteproyecto` (
  `id_asignacion` int(11) NOT NULL,
  `codigo_anteproyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignar_estudiante_proyecto`
--

CREATE TABLE `asignar_estudiante_proyecto` (
  `id_asignacion` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Asignar_jurados_proyecto`
--

CREATE TABLE `Asignar_jurados_proyecto` (
  `id_asignacion` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `opcion_jurado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Asignar_usuario_facultades`
--

CREATE TABLE `Asignar_usuario_facultades` (
  `id_usuario_facultad` int(11) NOT NULL,
  `numero_documento` varchar(10) NOT NULL,
  `id_facultad` int(11) NOT NULL,
  `id_programa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Asignar_usuario_facultades`
--

INSERT INTO `Asignar_usuario_facultades` (`id_usuario_facultad`, `numero_documento`, `id_facultad`, `id_programa`) VALUES
(87, '100597056', 3, 18),
(89, '3597131316', 3, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_anteproyectos`
--

CREATE TABLE `auditoria_anteproyectos` (
  `id_auditoria` int(11) NOT NULL,
  `old_id_anteproyecto` text DEFAULT NULL,
  `old_codigo_anteproyecto` text DEFAULT NULL,
  `old_titulo_anteproyecto` text DEFAULT NULL,
  `old_palabras_claves` text DEFAULT NULL,
  `old_id_facultad` text DEFAULT NULL,
  `old_id_programa` text DEFAULT NULL,
  `old_fecha_creacion` text DEFAULT NULL,
  `old_estado` text DEFAULT NULL,
  `old_modalidad` text DEFAULT NULL,
  `new_id_anteproyecto` text DEFAULT NULL,
  `new_codigo_anteproyecto` text DEFAULT NULL,
  `new_titulo_anteproyecto` text DEFAULT NULL,
  `new_palabras_claves` text DEFAULT NULL,
  `new_id_facultad` text DEFAULT NULL,
  `new_id_programa` text DEFAULT NULL,
  `new_fecha_creacion` text DEFAULT NULL,
  `new_estado` text DEFAULT NULL,
  `new_modalidad` text DEFAULT NULL,
  `tipo_accion` varchar(10) DEFAULT NULL,
  `fecha_auditoria` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_jurados`
--

CREATE TABLE `calificaciones_jurados` (
  `id` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `resumen_parte1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`resumen_parte1`)),
  `resumen_parte2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`resumen_parte2`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargar_documento_anteproyectos`
--

CREATE TABLE `cargar_documento_anteproyectos` (
  `id` int(11) NOT NULL,
  `codigo_anteproyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `documento` varchar(300) NOT NULL,
  `nombre_archivo_word` varchar(300) NOT NULL,
  `estado` char(1) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargar_documento_proyectos`
--

CREATE TABLE `cargar_documento_proyectos` (
  `id` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `documento` varchar(300) NOT NULL,
  `nombre_archivo_word` varchar(300) NOT NULL,
  `estado` char(1) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_aplicacion`
--

CREATE TABLE `configuracion_aplicacion` (
  `consecutivo` int(11) NOT NULL,
  `numero_estudiantes_proyectos` int(3) NOT NULL DEFAULT 0,
  `numero_jurados_proyectos` int(3) NOT NULL DEFAULT 0,
  `nombre_logo` varchar(255) NOT NULL DEFAULT 'logo-autonoma.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_aplicacion`
--

INSERT INTO `configuracion_aplicacion` (`consecutivo`, `numero_estudiantes_proyectos`, `numero_jurados_proyectos`, `nombre_logo`) VALUES
(1, 2, 2, 'logo-autonoma.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_proyectos`
--

CREATE TABLE `evaluaciones_proyectos` (
  `id` int(11) NOT NULL,
  `codigo_proyecto` varchar(50) NOT NULL,
  `resumen_general` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`resumen_general`)),
  `evaluacion_jurado1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`evaluacion_jurado1`)),
  `evaluacion_jurado2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`evaluacion_jurado2`)),
  `fecha` varchar(45) NOT NULL,
  `calificacion_jurado1` int(11) DEFAULT 0,
  `calificacion_jurado2` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_reuniones_anteproyectos`
--

CREATE TABLE `evidencia_reuniones_anteproyectos` (
  `id` int(11) NOT NULL,
  `codigo_anteproyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `imagenes` varchar(300) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_reuniones_proyectos`
--

CREATE TABLE `evidencia_reuniones_proyectos` (
  `id` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `imagenes` varchar(300) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultades`
--

CREATE TABLE `facultades` (
  `id_facultad` int(11) NOT NULL,
  `nombre_facultad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facultades`
--

INSERT INTO `facultades` (`id_facultad`, `nombre_facultad`) VALUES
(1, 'Facultad de Ciencias Sociales'),
(2, 'Facultad de Ciencias Humanas y de la Salud'),
(3, 'Facultad de Ciencias Aplicada, Ingeniería y Diseño');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `firma_digital_usuarios`
--

CREATE TABLE `firma_digital_usuarios` (
  `id` int(11) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `firma` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_sesiones`
--

CREATE TABLE `historial_sesiones` (
  `id_sesion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `numero_documento` varchar(10) NOT NULL,
  `inicio_sesion` datetime NOT NULL,
  `cierre_sesion` datetime DEFAULT NULL,
  `ip_usuario` varchar(45) DEFAULT NULL,
  `navegador_usuario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_sesiones`
--

INSERT INTO `historial_sesiones` (`id_sesion`, `id_usuario`, `numero_documento`, `inicio_sesion`, `cierre_sesion`, `ip_usuario`, `navegador_usuario`) VALUES
(103, 1, '1077451868', '2025-04-24 18:46:49', '2025-04-24 13:53:58', '181.51.34.86', 'Google Chrome'),
(104, 1, '1077451868', '2025-04-25 16:44:42', NULL, '181.51.34.86', 'Google Chrome'),
(105, 1, '1077451868', '2025-05-09 18:23:33', '2025-05-09 14:33:48', '181.51.34.86', 'Google Chrome'),
(106, 1, '1077451868', '2025-05-10 01:28:44', NULL, '181.51.34.86', 'Google Chrome'),
(107, 1, '1077451868', '2025-05-10 15:59:02', '2025-05-10 14:20:03', '181.51.34.86', 'Google Chrome'),
(108, 1, '1077451868', '2025-05-21 18:14:04', '2025-05-21 13:37:18', '181.51.34.86', 'Google Chrome'),
(109, 1, '1077451868', '2025-05-23 20:53:14', NULL, '181.51.34.86', 'Google Chrome'),
(110, 1, '1077451868', '2025-05-24 15:01:21', '2025-05-24 10:54:29', '181.51.34.86', 'Google Chrome'),
(111, 1, '1077451868', '2025-05-24 17:42:57', '2025-05-24 17:11:31', '181.51.34.86', 'Google Chrome'),
(112, 1, '1077451868', '2025-05-25 16:59:37', '2025-05-25 12:53:35', '181.51.34.86', 'Google Chrome'),
(113, 1, '1077451868', '2025-05-25 18:58:14', '2025-05-25 14:04:00', '181.51.34.86', 'Google Chrome'),
(114, 1, '1077451868', '2025-05-28 14:44:37', '2025-05-28 14:42:13', '181.51.34.86', 'Google Chrome');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_portada`
--

CREATE TABLE `imagenes_portada` (
  `id` int(11) NOT NULL,
  `nombre_imagenes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`nombre_imagenes`)),
  `estado` char(1) NOT NULL CHECK (`estado` in ('A','I'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes_portada`
--

INSERT INTO `imagenes_portada` (`id`, `nombre_imagenes`, `estado`) VALUES
(3, ' [\"img_67e881e851e9b8.38538812.jpg\",\"img_67e881e852d441.79226666.jpg\"]', 'A'),
(4, ' [\"img_67ebf6c3b7d3c2.96726529.jpg\",\"img_67ebf6c3b83e52.88544119.jpg\",\"img_67ebf6c3b8ad26.56352414.jpg\",\"img_67ebf6c3b90232.33562669.jpg\"]', 'I');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL,
  `id_receptor` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NULL DEFAULT current_timestamp(),
  `leido` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidad_grados`
--

CREATE TABLE `modalidad_grados` (
  `id_modalidad` int(11) NOT NULL,
  `nombre_modalidad` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modalidad_grados`
--

INSERT INTO `modalidad_grados` (`id_modalidad`, `nombre_modalidad`) VALUES
(1, 'TRABAJO DE GRADO'),
(2, 'PASANTIAS'),
(3, 'PARTICIPACIÓN EN GRUPOS DE INVESTIGACIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_academicos`
--

CREATE TABLE `programas_academicos` (
  `id_programa` int(11) NOT NULL,
  `id_facultad` int(11) DEFAULT NULL,
  `nombre_programa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas_academicos`
--

INSERT INTO `programas_academicos` (`id_programa`, `id_facultad`, `nombre_programa`) VALUES
(9, 1, 'Contaduría Pública'),
(10, 1, 'Administración de Empresas'),
(11, 1, 'Derecho'),
(14, 2, 'Profesional en Seguridad y salud en el trabajo'),
(15, 2, 'Tecnología en la gestión de la seguridad y salud en el trabajo'),
(16, 3, 'Ingeniería informática'),
(17, 3, 'Diseño Visual'),
(18, 3, 'Tecnología en decoración de  Interiores');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id_proyecto` int(11) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `titulo_proyecto` varchar(255) NOT NULL,
  `palabras_claves` varchar(255) DEFAULT NULL,
  `id_facultad` int(11) NOT NULL,
  `id_programa` int(11) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Revisión',
  `modalidad` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasena`
--

CREATE TABLE `recuperacion_contrasena` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `estado` varchar(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_calificados_programas`
--

CREATE TABLE `registros_calificados_programas` (
  `id` int(11) NOT NULL,
  `id_programa` int(3) NOT NULL,
  `nombre_registro` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros_calificados_programas`
--

INSERT INTO `registros_calificados_programas` (`id`, `id_programa`, `nombre_registro`, `fecha_creacion`) VALUES
(3, 9, 'FR-DA-GDE-0012', '2025-03-20 23:40:01'),
(4, 10, 'FR-DA-GDE-0020', '2025-03-20 23:40:17'),
(5, 11, 'FR-DA-GDE-0035', '2025-03-20 23:42:08'),
(6, 12, 'FR-DA-GDE-0036', '2025-03-20 23:42:16'),
(7, 13, 'FR-DA-GDE-0055', '2025-03-20 23:42:25'),
(8, 14, 'FR-DA-GDE-0077', '2025-03-20 23:42:33'),
(9, 16, 'FR-DA-GDE-0079', '2025-04-01 14:46:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retroalimentacion_anteproyecto`
--

CREATE TABLE `retroalimentacion_anteproyecto` (
  `id_retroalimentacion` int(11) NOT NULL,
  `id` int(11) DEFAULT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `codigo_anteproyecto` varchar(6) NOT NULL,
  `observacion_general` longtext DEFAULT NULL,
  `estado` varchar(45) NOT NULL,
  `documento` varchar(300) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_entrega_avances` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retroalimentacion_proyecto`
--

CREATE TABLE `retroalimentacion_proyecto` (
  `id_retroalimentacion` int(11) NOT NULL,
  `id` int(11) DEFAULT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `codigo_proyecto` varchar(6) NOT NULL,
  `observacion_general` longtext DEFAULT NULL,
  `estado` varchar(45) NOT NULL,
  `documento` varchar(300) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_entrega_avances` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_usuarios`
--

CREATE TABLE `roles_usuarios` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles_usuarios`
--

INSERT INTO `roles_usuarios` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Coordinador '),
(3, 'Estudiante Anteproyecto'),
(4, 'Estudiante Proyecto'),
(5, 'Director'),
(6, 'Director Externo ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `numero_documento` varchar(10) NOT NULL,
  `nombre_usuario` varchar(45) NOT NULL,
  `apellidos_usuario` varchar(45) NOT NULL,
  `correo_usuario` varchar(100) NOT NULL,
  `telefono_usuario` varchar(10) DEFAULT NULL,
  `id_rol` int(11) NOT NULL,
  `contrasena_usuario` varchar(45) NOT NULL,
  `estado` int(1) NOT NULL,
  `imagen_usuario` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `estado_conexion` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `numero_documento`, `nombre_usuario`, `apellidos_usuario`, `correo_usuario`, `telefono_usuario`, `id_rol`, `contrasena_usuario`, `estado`, `imagen_usuario`, `created_at`, `estado_conexion`) VALUES
(1, '1077451868', 'Jhon', 'Stewar Moreno Murillo', 'jhonstiwarmorenomurillo@gmail.com', '3012701075', 1, 'U2t5RzJwdy9pdkllYzZLUkJ1MmRQZz09', 1, '680a876d161ff_15.jpg', '2025-02-11 04:21:11', 0),
(2, '1119889037', 'Leidy Tatiana', 'Cespedes', 'leidy.cespedes@aunarvillavicencio.edu.co', '3102175386', 1, 'U2t5RzJwdy9pdkllYzZLUkJ1MmRQZz09', 1, 'AvatarNone.png', '2025-04-24 10:12:22', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anteproyectos`
--
ALTER TABLE `anteproyectos`
  ADD PRIMARY KEY (`id_anteproyecto`),
  ADD UNIQUE KEY `codigo_anteproyecto` (`codigo_anteproyecto`),
  ADD KEY `id_programa` (`id_programa`),
  ADD KEY `id_facultad` (`id_facultad`);

--
-- Indices de la tabla `Asignar_asesor_anteproyecto_proyecto`
--
ALTER TABLE `Asignar_asesor_anteproyecto_proyecto`
  ADD PRIMARY KEY (`id_asignacion`);

--
-- Indices de la tabla `asignar_estudiante_anteproyecto`
--
ALTER TABLE `asignar_estudiante_anteproyecto`
  ADD PRIMARY KEY (`id_asignacion`);

--
-- Indices de la tabla `asignar_estudiante_proyecto`
--
ALTER TABLE `asignar_estudiante_proyecto`
  ADD PRIMARY KEY (`id_asignacion`);

--
-- Indices de la tabla `Asignar_jurados_proyecto`
--
ALTER TABLE `Asignar_jurados_proyecto`
  ADD PRIMARY KEY (`id_asignacion`);

--
-- Indices de la tabla `Asignar_usuario_facultades`
--
ALTER TABLE `Asignar_usuario_facultades`
  ADD PRIMARY KEY (`id_usuario_facultad`),
  ADD KEY `id_facultad` (`id_facultad`),
  ADD KEY `id_programa` (`id_programa`);

--
-- Indices de la tabla `auditoria_anteproyectos`
--
ALTER TABLE `auditoria_anteproyectos`
  ADD PRIMARY KEY (`id_auditoria`);

--
-- Indices de la tabla `calificaciones_jurados`
--
ALTER TABLE `calificaciones_jurados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cargar_documento_anteproyectos`
--
ALTER TABLE `cargar_documento_anteproyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cargar_documento_proyectos`
--
ALTER TABLE `cargar_documento_proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion_aplicacion`
--
ALTER TABLE `configuracion_aplicacion`
  ADD PRIMARY KEY (`consecutivo`);

--
-- Indices de la tabla `evaluaciones_proyectos`
--
ALTER TABLE `evaluaciones_proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evidencia_reuniones_anteproyectos`
--
ALTER TABLE `evidencia_reuniones_anteproyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evidencia_reuniones_proyectos`
--
ALTER TABLE `evidencia_reuniones_proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facultades`
--
ALTER TABLE `facultades`
  ADD PRIMARY KEY (`id_facultad`);

--
-- Indices de la tabla `firma_digital_usuarios`
--
ALTER TABLE `firma_digital_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_sesiones`
--
ALTER TABLE `historial_sesiones`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `imagenes_portada`
--
ALTER TABLE `imagenes_portada`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_emisor` (`id_emisor`),
  ADD KEY `id_receptor` (`id_receptor`);

--
-- Indices de la tabla `modalidad_grados`
--
ALTER TABLE `modalidad_grados`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `programas_academicos`
--
ALTER TABLE `programas_academicos`
  ADD PRIMARY KEY (`id_programa`),
  ADD KEY `id_facultad` (`id_facultad`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD UNIQUE KEY `codigo_proyecto` (`codigo_proyecto`),
  ADD KEY `id_programa` (`id_programa`),
  ADD KEY `id_facultad` (`id_facultad`);

--
-- Indices de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `registros_calificados_programas`
--
ALTER TABLE `registros_calificados_programas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `retroalimentacion_anteproyecto`
--
ALTER TABLE `retroalimentacion_anteproyecto`
  ADD PRIMARY KEY (`id_retroalimentacion`),
  ADD KEY `id` (`id`);

--
-- Indices de la tabla `retroalimentacion_proyecto`
--
ALTER TABLE `retroalimentacion_proyecto`
  ADD PRIMARY KEY (`id_retroalimentacion`),
  ADD KEY `id` (`id`);

--
-- Indices de la tabla `roles_usuarios`
--
ALTER TABLE `roles_usuarios`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anteproyectos`
--
ALTER TABLE `anteproyectos`
  MODIFY `id_anteproyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `Asignar_asesor_anteproyecto_proyecto`
--
ALTER TABLE `Asignar_asesor_anteproyecto_proyecto`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `asignar_estudiante_anteproyecto`
--
ALTER TABLE `asignar_estudiante_anteproyecto`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `asignar_estudiante_proyecto`
--
ALTER TABLE `asignar_estudiante_proyecto`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Asignar_jurados_proyecto`
--
ALTER TABLE `Asignar_jurados_proyecto`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `Asignar_usuario_facultades`
--
ALTER TABLE `Asignar_usuario_facultades`
  MODIFY `id_usuario_facultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `auditoria_anteproyectos`
--
ALTER TABLE `auditoria_anteproyectos`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `calificaciones_jurados`
--
ALTER TABLE `calificaciones_jurados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargar_documento_anteproyectos`
--
ALTER TABLE `cargar_documento_anteproyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cargar_documento_proyectos`
--
ALTER TABLE `cargar_documento_proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion_aplicacion`
--
ALTER TABLE `configuracion_aplicacion`
  MODIFY `consecutivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_proyectos`
--
ALTER TABLE `evaluaciones_proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `evidencia_reuniones_anteproyectos`
--
ALTER TABLE `evidencia_reuniones_anteproyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `evidencia_reuniones_proyectos`
--
ALTER TABLE `evidencia_reuniones_proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `facultades`
--
ALTER TABLE `facultades`
  MODIFY `id_facultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `firma_digital_usuarios`
--
ALTER TABLE `firma_digital_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `historial_sesiones`
--
ALTER TABLE `historial_sesiones`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `imagenes_portada`
--
ALTER TABLE `imagenes_portada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `modalidad_grados`
--
ALTER TABLE `modalidad_grados`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `programas_academicos`
--
ALTER TABLE `programas_academicos`
  MODIFY `id_programa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id_proyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `registros_calificados_programas`
--
ALTER TABLE `registros_calificados_programas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `retroalimentacion_anteproyecto`
--
ALTER TABLE `retroalimentacion_anteproyecto`
  MODIFY `id_retroalimentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `retroalimentacion_proyecto`
--
ALTER TABLE `retroalimentacion_proyecto`
  MODIFY `id_retroalimentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles_usuarios`
--
ALTER TABLE `roles_usuarios`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=328;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anteproyectos`
--
ALTER TABLE `anteproyectos`
  ADD CONSTRAINT `anteproyectos_ibfk_1` FOREIGN KEY (`id_programa`) REFERENCES `programas_academicos` (`id_programa`),
  ADD CONSTRAINT `anteproyectos_ibfk_2` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`);

--
-- Filtros para la tabla `Asignar_usuario_facultades`
--
ALTER TABLE `Asignar_usuario_facultades`
  ADD CONSTRAINT `Asignar_usuario_facultades_ibfk_1` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`),
  ADD CONSTRAINT `Asignar_usuario_facultades_ibfk_2` FOREIGN KEY (`id_programa`) REFERENCES `programas_academicos` (`id_programa`);

--
-- Filtros para la tabla `historial_sesiones`
--
ALTER TABLE `historial_sesiones`
  ADD CONSTRAINT `historial_sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_emisor`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_receptor`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `programas_academicos`
--
ALTER TABLE `programas_academicos`
  ADD CONSTRAINT `programas_academicos_ibfk_1` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`id_programa`) REFERENCES `programas_academicos` (`id_programa`),
  ADD CONSTRAINT `proyectos_ibfk_2` FOREIGN KEY (`id_facultad`) REFERENCES `facultades` (`id_facultad`);

--
-- Filtros para la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD CONSTRAINT `recuperacion_contrasena_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `retroalimentacion_anteproyecto`
--
ALTER TABLE `retroalimentacion_anteproyecto`
  ADD CONSTRAINT `retroalimentacion_anteproyecto_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cargar_documento_anteproyectos` (`id`);

--
-- Filtros para la tabla `retroalimentacion_proyecto`
--
ALTER TABLE `retroalimentacion_proyecto`
  ADD CONSTRAINT `retroalimentacion_proyecto_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cargar_documento_proyectos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles_usuarios` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
