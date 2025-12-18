-- ============================================
-- CERSA - Sistema de Gestión Académica
-- Archivo: seed.sql
-- Descripción: Datos de prueba para el sistema
-- Autor: Giancarlos
-- Fecha: 2025-12-16
-- ============================================

USE proyecto_final;

-- ============================================
-- DATOS: admin (Usuarios del sistema)
-- Contraseñas hasheadas con password_hash() en PHP
-- ============================================

INSERT INTO `admin` (`id`, `nombre`, `email`, `descripcion`, `foto`, `password`, `rol`) VALUES
(1, 'Giancarlos', 'giancarlos@cersa.com', 'Administrador de la página', 'IMG-20251216-WA0022_1765898840.jpg', '$2y$10$6zDpma.QmD6Up.QZrD6RIelt5WjqosGbQ7CFbxwDEe4...', 'admin'),
(2, 'Giovanni', 'alumno@cersa.com', NULL, NULL, '$2y$10$P5U6hzb/0YOvgrT1dtPHHexrXF0pRLCD6Z/SzQO7nR1...', 'alumno');

-- ============================================
-- DATOS: categoria (Categorías de cursos)
-- ============================================

INSERT INTO `categoria` (`id`, `nombre`) VALUES
(1, 'Programación'),
(2, 'Diseño'),
(3, 'Idiomas'),
(4, 'Marketing');

-- ============================================
-- DATOS: modalidad (Modalidades de cursos)
-- ============================================

INSERT INTO `modalidad` (`id`, `nombre`) VALUES
(1, 'Virtual en vivo'),
(2, 'Video'),
(3, 'Presencial');

-- ============================================
-- DATOS: docente (Profesores)
-- ============================================

INSERT INTO `docente` (`id`, `nombre`, `especialidad`, `dni`) VALUES
(1, 'Luisa Torres', 'Programación', '11111111'),
(2, 'Luis Pérez', 'Diseño', '22222222'),
(3, 'María Gómez', 'Idiomas', '33333333'),
(4, 'Carlos Ruiz', 'Marketing', '44444444'),
(5, 'Sofía Ramírez', 'Programación', '55555555');

-- ============================================
-- DATOS: alumno (Estudiantes)
-- ============================================

INSERT INTO `alumno` (`id`, `nombre`, `dni`, `email`, `celular`, `foto`, `password`) VALUES
(1, 'Luis López', '12345678', 'luislopez@email.com', '987654321', 'foto1.svg', 'pass123'),
(2, 'Karla Martínez', '23456789', 'lucia@email.com', '987654322', 'foto1.svg', 'pass123'),
(3, 'Pedro Sánchez', '34567890', 'pedro@email.com', '987654323', 'foto2.svg', 'pass123'),
(5, 'Miguel Herrera', '56789012', 'miguel@email.com', '987654325', 'foto3.svg', 'pass123'),
(6, 'Laura Jiménez', '67890123', 'laura@email.com', '987654326', 'foto1.jpg', 'pass123'),
(7, 'Andrés Rojas', '78901234', 'andres@email.com', '987654327', NULL, 'pass123'),
(8, 'Sonia Vargas', '89012345', 'sonia@email.com', '987654328', NULL, 'pass123'),
(9, 'Diego Castro', '90123456', 'diego@email.com', '987654329', NULL, 'pass123'),
(10, 'Paula Romero', '01234567', 'paula@email.com', '987654330', NULL, 'pass123');

-- ============================================
-- DATOS: curso (Cursos disponibles)
-- ============================================

INSERT INTO `curso` (`id`, `nombre`, `categoria_id`, `modalidad_id`, `docente_id`, `fecha_inicio`, `duracion`, `cupos`, `precio`, `estado`) VALUES
(1, 'Python Básico II', 1, 1, 1, '2025-11-10', 30, 20, 150.00, 'Activo'),
(2, 'Diseño Gráfico', 2, 2, 2, '2025-11-15', 45, 15, 200.00, 'Activo'),
(3, 'Inglés Inicial', 3, 1, 3, '2025-11-20', 60, 25, 180.00, 'Activo'),
(4, 'Marketing Digital', 4, 3, 4, '2025-11-25', 40, 18, 220.00, 'Activo'),
(5, 'JavaScript Avanzado II', 1, 2, 5, '2025-12-06', 35, 22, 170.00, 'Activo');

-- ============================================
-- DATOS: matricula (Inscripciones de alumnos)
-- ============================================

INSERT INTO `matricula` (`id`, `alumno_id`, `curso_id`, `fecha_inscripcion`, `estado`) VALUES
(2, 2, 1, '2025-09-12', 'Matriculado'),
(3, 3, 2, '2025-11-03', 'Pendiente'),
(5, 5, 3, '2025-11-05', 'Pendiente'),
(6, 6, 3, '2025-08-18', 'Matriculado'),
(7, 7, 4, '2025-06-06', 'Matriculado'),
(8, 8, 4, '2025-12-05', 'Matriculado'),
(9, 9, 5, '2025-11-09', 'Pendiente'),
(10, 10, 5, '2025-11-10', 'Pendiente'),
(11, 1, 1, '2025-09-05', 'Matriculado'),
(12, 2, 2, '2025-10-09', 'Matriculado'),
(13, 3, 3, '2025-09-19', 'Matriculado'),
(14, 4, 4, '2025-08-13', 'Matriculado');

-- ============================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================

SELECT 'Datos insertados correctamente' AS status;
SELECT COUNT(*) AS total_admin FROM admin;
SELECT COUNT(*) AS total_alumnos FROM alumno;
SELECT COUNT(*) AS total_categorias FROM categoria;
SELECT COUNT(*) AS total_modalidades FROM modalidad;
SELECT COUNT(*) AS total_docentes FROM docente;
SELECT COUNT(*) AS total_cursos FROM curso;
SELECT COUNT(*) AS total_matriculas FROM matricula;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================