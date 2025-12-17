## 🧪 Casos de Prueba (Testing Manual)

### **Prueba 1: Autenticación - Login Exitoso**

**Objetivo:** Verificar que el login funciona con credenciales válidas

**Pasos:**

1. Abrir `http://localhost/admin_php/`
2. Ingresar email: `giancarlos@cersa.com`
3. Ingresar contraseña: `admin123`
4. Hacer clic en "Iniciar Sesión"

**Resultado esperado:**

- ✅ Redirección a `index2.php` (Dashboard)
- ✅ Mensaje de bienvenida: "¡Bienvenido, Giancarlos!"
- ✅ Sesión activa visible en el topbar

---

### **Prueba 2: Autenticación - Login con credenciales incorrectas**

**Objetivo:** Verificar que el sistema rechaza credenciales inválidas

**Pasos:**

1. Abrir `http://localhost/admin_php/`
2. Ingresar email: `test@test.com`
3. Ingresar contraseña: `wrongpassword`
4. Hacer clic en "Iniciar Sesión"

**Resultado esperado:**

- ✅ NO se inicia sesión
- ✅ Mensaje de error: "Usuario o contraseña incorrectos"
- ✅ Permanece en la página de login

---

### **Prueba 3: CRUD - Agregar Alumno (Caso exitoso)**

**Objetivo:** Verificar que se pueden crear alumnos con datos válidos

**Pasos:**

1. Iniciar sesión como admin
2. Ir a "Alumnos" → "Lista de Alumnos"
3. Hacer clic en "Agregar alumno"
4. Llenar formulario:
   - Nombre: `Juan Pérez`
   - DNI: `87654321`
   - Email: `juan.perez@test.com`
   - Celular: `987654321`
5. Hacer clic en "Registrar alumno"

**Resultado esperado:**

- ✅ Mensaje de éxito: "Alumno registrado exitosamente"
- ✅ Página recarga automáticamente
- ✅ Nuevo alumno aparece en la tabla
- ✅ Datos guardados correctamente en la BD

---

### **Prueba 4: CRUD - Validación DNI Duplicado**

**Objetivo:** Verificar que el sistema no permite DNIs duplicados

**Pasos:**

1. Ir a "Alumnos" → "Agregar alumno"
2. Intentar agregar alumno con DNI existente: `12345678`
3. Llenar los demás campos con datos válidos
4. Hacer clic en "Registrar alumno"

**Resultado esperado:**

- ✅ NO se guarda el alumno
- ✅ Mensaje de error debajo del campo DNI: "⚠️ El DNI ya está registrado en el sistema"
- ✅ Campo DNI marcado con borde rojo

---

### **Prueba 5: CRUD - Validación de Formato DNI**

**Objetivo:** Verificar que solo acepta DNIs de 8 dígitos numéricos

**Pasos:**

1. Intentar agregar alumno con DNI: `123` (menos de 8 dígitos)
2. Hacer clic en "Registrar alumno"

**Resultado esperado:**

- ✅ Mensaje de error: "⚠️ El DNI debe tener exactamente 8 dígitos numéricos"

---

### **Prueba 6: CRUD - Editar Alumno**

**Objetivo:** Verificar que se pueden modificar datos de alumnos existentes

**Pasos:**

1. En la lista de alumnos, hacer clic en "Editar" de cualquier alumno
2. Modificar el nombre a: `María López Actualizada`
3. Hacer clic en "Guardar cambios"

**Resultado esperado:**

- ✅ Mensaje de éxito: "Alumno actualizado exitosamente"
- ✅ Página recarga automáticamente
- ✅ Cambios reflejados en la tabla
- ✅ Datos actualizados en la BD

---

### **Prueba 7: CRUD - Eliminar Alumno**

**Objetivo:** Verificar que se pueden eliminar alumnos con confirmación

**Pasos:**

1. En la lista de alumnos, hacer clic en "Eliminar"
2. Verificar que aparece modal de confirmación con datos del alumno
3. Hacer clic en "Sí, eliminar"

**Resultado esperado:**

- ✅ Mensaje de éxito: "Alumno eliminado correctamente"
- ✅ Alumno desaparece de la tabla
- ✅ Registro eliminado de la BD

---

### **Prueba 8: Búsqueda y Filtros - Búsqueda por texto**

**Objetivo:** Verificar que la búsqueda filtra correctamente

**Pasos:**

1. En la lista de alumnos, escribir en el buscador: `Karla`
2. Observar resultados

**Resultado esperado:**

- ✅ Solo muestra alumnos que contengan "Karla" en nombre, DNI o email
- ✅ Otros registros se ocultan automáticamente
- ✅ Filtrado en tiempo real (sin recargar página)

---

### **Prueba 9: Búsqueda y Filtros - Filtrar por curso**

**Objetivo:** Verificar que el filtro por curso funciona

**Pasos:**

1. Seleccionar curso: "Python Básico II"
2. Hacer clic en "Buscar"

**Resultado esperado:**

- ✅ Solo muestra alumnos matriculados en ese curso
- ✅ Paginación se actualiza según resultados

---

### **Prueba 10: Búsqueda y Filtros - Ordenamiento**

**Objetivo:** Verificar que el ordenamiento funciona

**Pasos:**

1. Seleccionar "Ordenar por: Nombre (A-Z)"
2. Hacer clic en "Buscar"

**Resultado esperado:**

- ✅ Alumnos ordenados alfabéticamente por nombre

---

### **Prueba 11: Paginación**

**Objetivo:** Verificar que la paginación funciona correctamente

**Pasos:**

1. Si hay más de 10 alumnos, verificar que aparecen botones de paginación
2. Hacer clic en "Página 2"

**Resultado esperado:**

- ✅ Muestra los siguientes 10 registros
- ✅ Botón "2" marcado como activo
- ✅ URL actualizada: `?pagina=2`

---

### **Prueba 12: Reportes PDF - Generar reporte de alumnos**

**Objetivo:** Verificar que se generan PDFs correctamente

**Pasos:**

1. Ir a "Reportes" → "Reporte de Alumno"
2. Esperar a que se genere el PDF

**Resultado esperado:**

- ✅ PDF se abre en nueva pestaña
- ✅ Contiene logo de CERSA
- ✅ Muestra tabla con todos los alumnos
- ✅ Datos correctos (sin IDs internos)
- ✅ Fecha y usuario que generó el reporte

---

### **Prueba 13: Tickets de Pago - Generar ticket**

**Objetivo:** Verificar que se generan tickets de pago correctamente

**Pasos:**

1. Ir a "Generar Tickets"
2. Buscar alumno: `Karla`
3. Hacer clic en "Ver Cursos"
4. Verificar que aparece modal con cursos matriculados
5. Hacer clic en "Generar Ticket de Pago"

**Resultado esperado:**

- ✅ Modal muestra datos del alumno
- ✅ Lista de cursos con precios
- ✅ Total calculado correctamente
- ✅ PDF se genera con:
  - Número de ticket único
  - Datos del alumno
  - Detalle de cursos
  - Total a pagar
  - Código QR (si está implementado)

---

### **Prueba 14: Subida de Archivos - Foto de perfil**

**Objetivo:** Verificar que se pueden subir fotos de perfil

**Pasos:**

1. Ir a "Perfil" (menú usuario arriba a la derecha)
2. Hacer clic en "Seleccionar archivo"
3. Elegir una imagen JPG menor a 2MB
4. Hacer clic en "Guardar"

**Resultado esperado:**

- ✅ Mensaje de éxito
- ✅ Foto se muestra en el perfil
- ✅ Archivo guardado en `/img/fotos/`
- ✅ Ruta guardada en BD (tabla `admin`, campo `foto`)

---

### **Prueba 15: Subida de Archivos - Validación de tamaño**

**Objetivo:** Verificar que rechaza archivos grandes

**Pasos:**

1. Intentar subir imagen mayor a 2MB

**Resultado esperado:**

- ✅ Mensaje de error: "La imagen no debe superar 2MB"
- ✅ Archivo NO se guarda

---

### **Prueba 16: Roles - Acceso de Admin**

**Objetivo:** Verificar que admin tiene acceso completo

**Pasos:**

1. Iniciar sesión como admin: `giancarlos@cersa.com`
2. Verificar el sidebar

**Resultado esperado:**

- ✅ Ve todas las opciones:
  - Alumnos
  - Cursos
  - Docentes
  - Matrículas
  - Reportes
  - Generar Tickets
  - Almacenar Recibos

---

### **Prueba 17: Roles - Acceso de Alumno (Restricción)**

**Objetivo:** Verificar que alumno tiene acceso limitado

**Pasos:**

1. Cerrar sesión
2. Iniciar sesión como alumno: `alumno@cersa.com`
3. Verificar el sidebar

**Resultado esperado:**

- ✅ Solo ve opciones permitidas:
  - Documentación
  - Cursos (solo ver)
  - Docentes (solo ver)
- ✅ NO ve:
  - Alumnos
  - Matrículas
  - Reportes
  - Generar Tickets

---

### **Prueba 18: Seguridad - Protección CSRF**

**Objetivo:** Verificar que formularios tienen token CSRF

**Pasos:**

1. Abrir "Agregar alumno"
2. Inspeccionar elemento (F12)
3. Buscar en el formulario: `<input type="hidden" name="csrf_token">`

**Resultado esperado:**

- ✅ Token CSRF presente en todos los formularios
- ✅ Valor único y aleatorio

---

### **Prueba 19: Seguridad - Protección XSS**

**Objetivo:** Verificar que no se ejecuta código JavaScript malicioso

**Pasos:**

1. Intentar agregar alumno con nombre: `<script>alert('XSS')</script>`
2. Guardar
3. Ver la lista de alumnos

**Resultado esperado:**

- ✅ El código se muestra como TEXTO, no se ejecuta
- ✅ Aparece literalmente: `<script>alert('XSS')</script>`

---

### **Prueba 20: Logout**

**Objetivo:** Verificar que el cierre de sesión funciona

**Pasos:**

1. Hacer clic en el nombre de usuario (arriba a la derecha)
2. Hacer clic en "Cerrar Sesión"
3. Confirmar en el modal

**Resultado esperado:**

- ✅ Redirección a `index.php` (login)
- ✅ Sesión destruida
- ✅ No se puede acceder a páginas internas sin login
- ✅ Botón "Atrás" del navegador no permite volver al dashboard

---

### **Prueba 21: CRUD Cursos - Agregar Curso**

**Objetivo:** Verificar que se pueden crear cursos con datos válidos

**Pasos:**

1. Ir a "Cursos" → "Agregar Curso"
2. Llenar formulario:
   - Nombre: `JavaScript Avanzado`
   - Precio: `500`
   - Categoría: `Programación`
   - Modalidad: `Virtual en vivo`
   - Docente: Seleccionar cualquiera
   - Cupos: `20`
   - Duración: `8 semanas`
3. Hacer clic en "Guardar"

**Resultado esperado:**

- ✅ Mensaje de éxito
- ✅ Curso aparece en la lista
- ✅ Datos guardados en BD

---

### **Prueba 22: CRUD Cursos - Editar Curso**

**Objetivo:** Verificar que se pueden modificar cursos existentes

**Pasos:**

1. En lista de cursos, hacer clic en "Editar"
2. Cambiar precio a: `550`
3. Guardar cambios

**Resultado esperado:**

- ✅ Cambios reflejados en la lista
- ✅ Precio actualizado en BD

---

### **Prueba 23: CRUD Cursos - Eliminar Curso**

**Objetivo:** Verificar eliminación de cursos

**Pasos:**

1. Hacer clic en "Eliminar" en un curso
2. Confirmar en modal

**Resultado esperado:**

- ✅ Curso eliminado
- ✅ Mensaje de éxito

---

### **Prueba 24: CRUD Docentes - Agregar Docente**

**Objetivo:** Verificar creación de docentes

**Pasos:**

1. Ir a "Docentes" → "Agregar Docente"
2. Llenar:
   - Nombre: `Pedro Ramírez`
   - DNI: `45678912`
   - Email: `pedro.ramirez@cersa.com`
   - Celular: `912345678`
3. Guardar

**Resultado esperado:**

- ✅ Docente creado exitosamente
- ✅ Aparece en lista

---

### **Prueba 25: CRUD Matrículas - Crear Matrícula**

**Objetivo:** Verificar que se pueden matricular alumnos en cursos

**Pasos:**

1. Ir a "Matrículas" → "Nueva Matrícula"
2. Seleccionar:
   - Alumno: Cualquiera
   - Curso: Cualquiera
   - Estado: `Matriculado`
3. Guardar

**Resultado esperado:**

- ✅ Matrícula creada
- ✅ Relación N:M establecida
- ✅ Aparece en lista de matrículas mostrando nombre del alumno y curso (no IDs)

### **Prueba 26: Validación - Email inválido**

**Objetivo:** Verificar que solo acepta emails con formato válido

**Pasos:**

1. Intentar agregar alumno con email: `correo_invalido`
2. Hacer clic en guardar

**Resultado esperado:**

- ✅ Mensaje de error: "⚠️ El email no tiene un formato válido"
- ✅ NO se guarda el alumno

---

### **Prueba 27: Validación - Email duplicado**

**Objetivo:** Verificar que no permite emails duplicados

**Pasos:**

1. Intentar agregar alumno con email ya existente
2. Guardar

**Resultado esperado:**

- ✅ Mensaje de error: "⚠️ El email ya está registrado"

### **Prueba 28: Dashboard - Carga de datos en tiempo real**

**Objetivo:** Verificar que las estadísticas se actualizan dinámicamente

**Pasos:**

1. Ver los totales en las 4 cards superiores
2. Agregar un nuevo alumno
3. Volver al dashboard (F5)

**Resultado esperado:**

- ✅ Card "ALUMNOS TOTALES" aumentó en +1
- ✅ Gráficos se actualizan con nuevos datos

---

### **Prueba 29: Dashboard - Gráficos interactivos**

**Objetivo:** Verificar que los gráficos de Chart.js funcionan

**Pasos:**

1. En el dashboard, pasar el mouse sobre los gráficos

**Resultado esperado:**

- ✅ Tooltips aparecen mostrando valores exactos
- ✅ Gráfico de líneas muestra ganancias por mes
- ✅ Gráfico de dona muestra ingresos por categoría

### **Prueba 30: Relaciones - Curso asignado a Docente (1:N)**

**Objetivo:** Verificar relación entre docente y cursos

**Pasos:**

1. Ir a "Docentes" y seleccionar uno
2. Ver sus cursos asignados

**Resultado esperado:**

- ✅ Se muestran todos los cursos que dicta ese docente
- ✅ Relación 1:N funcionando

---

### **Prueba 31: Relaciones - Alumno matriculado en múltiples cursos (N:M)**

**Objetivo:** Verificar relación muchos a muchos

**Pasos:**

1. Matricular al mismo alumno en 3 cursos diferentes
2. Generar ticket de pago para ese alumno

**Resultado esperado:**

- ✅ Modal muestra los 3 cursos
- ✅ Total suma correctamente los 3 precios
- ✅ Relación N:M funcionando

### **Prueba 32: Manejo de errores - Intentar acceder sin login**

**Objetivo:** Verificar protección de rutas

**Pasos:**

1. Cerrar sesión
2. Intentar acceder directamente a: `http://localhost/admin_php/actions/alumnos/indexalumno.php`

**Resultado esperado:**

- ✅ Redirección automática a login
- ✅ O mensaje: "Acceso denegado"

---

### **Prueba 33: Manejo de errores - Conexión a BD fallida**

**Objetivo:** Verificar que hay manejo de errores de conexión

**Pasos:**

1. En `db.php`, cambiar temporalmente la contraseña de MySQL a una incorrecta
2. Intentar acceder al sistema

**Resultado esperado:**

- ✅ Mensaje de error claro (no exposición de datos sensibles)
- ✅ Log de error registrado

## 📊 Resumen de Pruebas

| Categoría          | Pruebas | Estado |
| ------------------ | ------- | ------ |
| Autenticación      | 2       | ✅     |
| CRUD Alumnos       | 5       | ✅     |
| CRUD Cursos        | 3       | ✅     |
| CRUD Docentes      | 1       | ✅     |
| CRUD Matrículas    | 1       | ✅     |
| Validaciones       | 4       | ✅     |
| Búsqueda y Filtros | 3       | ✅     |
| Paginación         | 1       | ✅     |
| Reportes PDF       | 2       | ✅     |
| Subida de archivos | 2       | ✅     |
| Roles y Permisos   | 2       | ✅     |
| Dashboard          | 2       | ✅     |
| Relaciones         | 2       | ✅     |
| Seguridad          | 2       | ✅     |
| Manejo de Errores  | 2       | ✅     |
| Logout             | 1       | ✅     |
| **TOTAL**          | **33**  | ✅     |

## ✅ Validación Final

**Antes de entregar, verificar:**

- [ ] Todas las 20 pruebas pasan exitosamente
- [ ] No hay errores en consola del navegador (F12)
- [ ] No hay errores en logs de PHP
- [ ] Todos los links funcionan
- [ ] Todos los modales abren correctamente
- [ ] Los PDFs se generan sin errores
- [ ] Las imágenes cargan correctamente
