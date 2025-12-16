-- ============================================
-- CERSA - Sistema de Gestión Académica
-- Archivo: schema.sql
-- Descripción: Estructura completa de la base de datos
-- Autor: Giancarlos
-- Fecha: 2025-12-16
-- ============================================

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS proyecto_final 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE proyecto_final;

-- ============================================
-- ELIMINAR TABLAS SI EXISTEN (orden inverso por dependencias)
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `matricula`;
DROP TABLE IF EXISTS `curso`;
DROP TABLE IF EXISTS `categoria`;
DROP TABLE IF EXISTS `modalidad`;
DROP TABLE IF EXISTS `docente`;
DROP TABLE IF EXISTS `alumno`;
DROP TABLE IF EXISTS `admin`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- TABLA: admin
-- Descripción: Usuarios administradores y alumnos del sistema
-- ============================================

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(25) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: alumno
-- Descripción: Estudiantes registrados en el sistema
-- ============================================

CREATE TABLE `alumno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `foto` varchar(25) DEFAULT NULL,
  `password` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: categoria
-- Descripción: Categorías de cursos (Programación, Diseño, etc.)
-- ============================================

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: modalidad
-- Descripción: Modalidades de cursos (Virtual, Presencial, etc.)
-- ============================================

CREATE TABLE `modalidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: docente
-- Descripción: Profesores que dictan los cursos
-- ============================================

CREATE TABLE `docente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: curso
-- Descripción: Cursos ofrecidos por la institución
-- Relaciones: categoria (1:N), modalidad (1:N), docente (1:N)
-- ============================================

CREATE TABLE `curso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `modalidad_id` int(11) NOT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `duracion` int(11) DEFAULT NULL,
  `cupos` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `estado` enum('Activo','Próximo','En curso','Finalizado','Cerrado inscripciones') NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `modalidad_id` (`modalidad_id`),
  KEY `docente_id` (`docente_id`),
  CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidad` (`id`),
  CONSTRAINT `curso_ibfk_3` FOREIGN KEY (`docente_id`) REFERENCES `docente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- TABLA: matricula
-- Descripción: Relación N:M entre alumnos y cursos
-- Relaciones: alumno (N:M), curso (N:M)
-- ============================================

CREATE TABLE `matricula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha_inscripcion` date NOT NULL,
  `estado` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_id` (`alumno_id`, `curso_id`),
  KEY `matricula_ibfk_2` (`curso_id`),
  CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================