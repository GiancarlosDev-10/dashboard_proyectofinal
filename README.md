# 📊 CERSA – Sistema de Gestión Académica

Sistema web desarrollado en **PHP + MySQL** para la gestión integral de una institución educativa.  
Permite administrar **alumnos, docentes, cursos y matrículas**, generar **reportes dinámicos** y **exportarlos en PDF**, todo desde un **panel administrativo moderno**.

---

## 🚀 Funcionalidades Principales

### 🔐 Autenticación

- Login de administrador
- Manejo de sesiones
- Mensaje de bienvenida personalizado
- Cierre de sesión seguro

### 👨‍🎓 Gestión de Alumnos

- Crear, editar y eliminar alumnos
- Búsqueda y paginación
- Reporte general con gráficos
- Exportación a PDF

### 👩‍🏫 Gestión de Docentes

- Registro y edición de docentes
- Listado general
- Reporte PDF institucional

### 📚 Gestión de Cursos

- Administración de cursos
- Asociación con categorías, modalidades y docentes
- Reportes visuales
- Exportación a PDF

### 📝 Matrículas

- Registro de matrículas
- Estados: _Matriculado_ / _Pendiente_
- Control por fechas
- Reportes y estadísticas

---

## 📈 Dashboard Administrativo

Panel principal con **indicadores en tiempo real**:

- Total de alumnos
- Total de cursos
- Total de docentes
- Ganancias totales
- Gráficos dinámicos:
  - 📉 Ganancias por mes (línea)
  - 🍩 Ingresos por categoría (donut)

> Los gráficos se generan automáticamente a partir de los datos reales registrados en el sistema.

---

## 📄 Reportes en PDF

El sistema permite generar reportes profesionales en PDF con:

- Logo institucional
- Usuario que genera el reporte
- Fecha y hora (zona horaria Perú 🇵🇪)
- Tablas limpias y ordenadas
- Sin IDs internos
- Diseño institucional

Reportes disponibles:

- 📄 Reporte de Alumnos
- 📄 Reporte de Docentes
- 📄 Reporte de Cursos
- 📄 Reporte de Matrículas

---

## 🛠️ Tecnologías Utilizadas

- **PHP 8**
- **MySQL**
- **FPDF** (PDFs)
- **Chart.js** (gráficos)
- **Bootstrap 4**
- **SB Admin 2**
- **HTML5 / CSS3 / JavaScript**
- **Font Awesome**

---

## 🗂️ Estructura del Proyecto

admin_php/
│
├── actions/ # Lógica CRUD (alumnos, docentes, cursos, matrículas)
│ ├── alumnos/
│ ├── docentes/
│ ├── cursos/
│ └── matriculas/
│
├── includes/ # Componentes reutilizables
│ ├── header.php
│ ├── sidebar.php
│ ├── topbar.php
│ └── footer.php
│
├── reportespdf/ # Generación de reportes en PDF
│ ├── reportealumnos.php
│ ├── reportedocentes.php
│ ├── reportecursos.php
│ └── reportematriculas.php
│
├── vendor/ # Librerías externas
│ ├── bootstrap/
│ ├── chart.js/
│ ├── datatables/
│ ├── fontawesome-free/
│ ├── jquery/
│ └── fpdf/
│
├── img/ # Recursos gráficos
│ ├── logo_cersa.png
│ └── undraw_profile.svg
│
├── css/ # Estilos personalizados
├── js/ # Scripts personalizados
├── scss/ # Estilos SCSS (opcional)
│
├── db.php # Conexión a la base de datos
├── index.php # Login
├── index2.php # Dashboard principal
├── blank.php # Página base
└── README.md

---

## ⚙️ Instalación y Configuración

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/cersa-sistema-academico.git
   ```

Importar la base de datos

Abrir phpMyAdmin

Crear una base de datos (por ejemplo: proyecto_final)

Importar el archivo .sql

Configurar la conexión
Editar el archivo db.php:

$conn = new mysqli("localhost", "usuario", "password", "nombre_bd");

Mover el proyecto
Colocar la carpeta dentro de:

htdocs/ (XAMPP)

## Acceder al sistema

http://localhost/admin_php

👤 Usuario de Prueba
Email: giancarlos@cersa.com
Contraseña: admin123

## 📄 Reportes en PDF

El sistema genera reportes en PDF utilizando FPDF, con el siguiente formato:

- Logo institucional (CERSA)
- Título del reporte
- Usuario que genera el reporte
- Correo del usuario logueado
- Fecha y hora (zona horaria Perú 🇵🇪)
- Tablas limpias y ordenadas
- Sin mostrar IDs internos
- Encabezados con fondo gris suave

## Reportes disponibles:

📄 Reporte General de Alumnos
📄 Reporte General de Docentes
📄 Reporte General de Cursos
📄 Reporte General de Matrículas
📈 Dashboard Administrativo

El panel principal incluye:

Cards superiores:

Total de alumnos

Total de cursos

Total de docentes

Ganancias totales

Gráficos dinámicos:

📉 Ganancias por mes (gráfico de líneas)

🍩 Ingresos por categoría (gráfico donut)

Los gráficos se actualizan automáticamente según los datos reales del sistema.

🛠️ Tecnologías Utilizadas

PHP 8

MySQL

FPDF (reportes PDF)

Chart.js (gráficos dinámicos)

Bootstrap 4

SB Admin 2

HTML5 / CSS3

JavaScript

Font Awesome

🔐 Seguridad y Sesiones

Autenticación mediante login

Manejo de sesiones con $\_SESSION

Nombre y correo del usuario visibles en el topbar

Protección de páginas internas

Cierre de sesión seguro

📌 Estado del Proyecto

✅ Funcional
✅ Listo para entrega académica
✅ Preparado para presentación
🛠️ Posibles mejoras futuras:

Roles de usuario

Encriptación de contraseñas

Exportar gráficos a PDF

Dashboard para docentes

Filtros avanzados en reportes

✨ Autor

Giancarlos
Proyecto académico – Sistema de Gestión Académica
🇵🇪 Perú

📜 Licencia

Este proyecto es de uso educativo y académico.
