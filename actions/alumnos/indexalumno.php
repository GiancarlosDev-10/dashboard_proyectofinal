<?php
session_start();
require __DIR__ . '/../../includes/csrf.php';
include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <!-- ALERTA: Alumno eliminado -->
                <?php if (isset($_GET['delete']) && $_GET['delete'] === 'ok'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Alumno eliminado correctamente.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "indexalumno.php";
                        }, 1500);
                    </script>
                <?php endif; ?>

                <!-- ALERTA: Error -->
                <?php if (isset($_GET['delete_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['delete_error']) ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "indexalumno.php";
                        }, 2000);
                    </script>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Alumnos</h3>

                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar alumno
                    </button>
                </div>

                <!-- ========================================================= -->
                <!-- PANEL DE FILTROS Y BÚSQUEDA                              -->
                <!-- ========================================================= -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filtros y Búsqueda
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="indexalumno.php" id="formFiltros">
                            <div class="row">

                                <!-- Búsqueda por texto -->
                                <div class="col-md-4 mb-3">
                                    <label for="busqueda">Buscar por nombre, DNI o email:</label>
                                    <input type="text"
                                        name="busqueda"
                                        id="busqueda"
                                        class="form-control"
                                        placeholder="Escribe para buscar..."
                                        value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                                </div>

                                <!-- Filtro por curso -->
                                <div class="col-md-3 mb-3">
                                    <label for="curso_filter">Filtrar por curso:</label>
                                    <select name="curso_filter" id="curso_filter" class="form-control">
                                        <option value="">Todos los cursos</option>
                                        <?php
                                        include("../../db.php");
                                        $cursos = $conn->query("SELECT id, nombre FROM curso ORDER BY nombre");
                                        while ($curso = $cursos->fetch_assoc()):
                                            $selected = (isset($_GET['curso_filter']) && $_GET['curso_filter'] == $curso['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $curso['id'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($curso['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Filtro por estado -->
                                <div class="col-md-2 mb-3">
                                    <label for="estado_filter">Filtrar por estado:</label>
                                    <select name="estado_filter" id="estado_filter" class="form-control">
                                        <option value="">Todos los estados</option>
                                        <?php $estados = ['Activo', 'Inactivo'];
                                        foreach ($estados as $est):
                                            $sel = (isset($_GET['estado_filter']) && $_GET['estado_filter'] === $est) ? 'selected' : '';
                                        ?>
                                            <option value="<?= htmlspecialchars($est, ENT_QUOTES, 'UTF-8') ?>" <?= $sel ?>><?= htmlspecialchars($est, ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Ordenar por -->
                                <div class="col-md-3 mb-3">
                                    <label for="ordenar">Ordenar por:</label>
                                    <select name="ordenar" id="ordenar" class="form-control">
                                        <option value="id_desc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'id_desc') ? 'selected' : '' ?>>Más recientes</option>
                                        <option value="id_asc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'id_asc') ? 'selected' : '' ?>>Más antiguos</option>
                                        <option value="nombre_asc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'nombre_asc') ? 'selected' : '' ?>>Nombre (A-Z)</option>
                                        <option value="nombre_desc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'nombre_desc') ? 'selected' : '' ?>>Nombre (Z-A)</option>
                                        <option value="dni_asc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'dni_asc') ? 'selected' : '' ?>>DNI (menor a mayor)</option>
                                        <option value="dni_desc" <?= (isset($_GET['ordenar']) && $_GET['ordenar'] == 'dni_desc') ? 'selected' : '' ?>>DNI (mayor a menor)</option>
                                    </select>
                                </div>

                                <!-- Botones -->
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>

                            </div>

                            <!-- Botón limpiar filtros -->
                            <?php if (!empty($_GET['busqueda']) || !empty($_GET['curso_filter']) || !empty($_GET['ordenar']) || !empty($_GET['estado_filter'])): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <a href="indexalumno.php" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Limpiar filtros
                                        </a>
                                        <span class="text-muted ml-2">
                                            <i class="fas fa-info-circle"></i> Filtros activos
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Email</th>
                                        <th>Celular</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // ============================================
                                    // CONSTRUCCIÓN DE LA CONSULTA CON FILTROS
                                    // ============================================

                                    $registrosPorPagina = 10;
                                    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                    if ($pagina < 1) $pagina = 1;
                                    $inicio = ($pagina - 1) * $registrosPorPagina;

                                    // Consulta base
                                    $sql = "SELECT DISTINCT a.* FROM alumno a";
                                    $where = [];
                                    $params = [];
                                    $types = "";

                                    // Filtro por búsqueda de texto
                                    if (!empty($_GET['busqueda'])) {
                                        $busqueda = "%" . $_GET['busqueda'] . "%";
                                        $where[] = "(a.nombre LIKE ? OR a.dni LIKE ? OR a.email LIKE ?)";
                                        $params[] = $busqueda;
                                        $params[] = $busqueda;
                                        $params[] = $busqueda;
                                        $types .= "sss";
                                    }

                                    // Filtro por curso
                                    if (!empty($_GET['curso_filter'])) {
                                        $sql .= " INNER JOIN matricula m ON a.id = m.alumno_id";
                                        $where[] = "m.curso_id = ?";
                                        $params[] = (int)$_GET['curso_filter'];
                                        $types .= "i";
                                    }

                                    // Filtro por estado
                                    if (!empty($_GET['estado_filter'])) {
                                        $where[] = "a.estado = ?";
                                        $params[] = $_GET['estado_filter'];
                                        $types .= "s";
                                    }

                                    // Agregar condiciones WHERE
                                    if (!empty($where)) {
                                        $sql .= " WHERE " . implode(" AND ", $where);
                                    }

                                    // Ordenamiento
                                    $ordenar = $_GET['ordenar'] ?? 'id_desc';
                                    switch ($ordenar) {
                                        case 'id_asc':
                                            $sql .= " ORDER BY a.id ASC";
                                            break;
                                        case 'nombre_asc':
                                            $sql .= " ORDER BY a.nombre ASC";
                                            break;
                                        case 'nombre_desc':
                                            $sql .= " ORDER BY a.nombre DESC";
                                            break;
                                        case 'dni_asc':
                                            $sql .= " ORDER BY a.dni ASC";
                                            break;
                                        case 'dni_desc':
                                            $sql .= " ORDER BY a.dni DESC";
                                            break;
                                        default:
                                            $sql .= " ORDER BY a.id DESC";
                                    }

                                    // Contar total de registros
                                    $sqlCount = str_replace("SELECT DISTINCT a.*", "SELECT COUNT(DISTINCT a.id) as total", $sql);
                                    $sqlCount = preg_replace('/ORDER BY.*/', '', $sqlCount);

                                    $stmtCount = $conn->prepare($sqlCount);
                                    if (!empty($params)) {
                                        $stmtCount->bind_param($types, ...$params);
                                    }
                                    $stmtCount->execute();
                                    $totalRegistros = $stmtCount->get_result()->fetch_assoc()['total'];
                                    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
                                    $stmtCount->close();

                                    // Agregar paginación
                                    $sql .= " LIMIT ?, ?";
                                    $params[] = $inicio;
                                    $params[] = $registrosPorPagina;
                                    $types .= "ii";

                                    // Ejecutar consulta
                                    $stmt = $conn->prepare($sql);
                                    if (!empty($params)) {
                                        $stmt->bind_param($types, ...$params);
                                    }
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                    ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['dni']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><?= htmlspecialchars($row['celular']) ?></td>
                                                <td class="text-center">

                                                    <!-- Botón EDITAR -->
                                                    <button class="btn btn-warning btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#editModal"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-dni="<?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-celular="<?= htmlspecialchars($row['celular'], ENT_QUOTES, 'UTF-8') ?>"
                                                        aria-label="Editar <?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <i class="fa fa-edit"></i>
                                                        <span class="ml-1">Editar</span>
                                                    </button>

                                                    <!-- Botón ELIMINAR -->
                                                    <button class="btn btn-danger btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#deleteModal"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-dni="<?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?>"
                                                        data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>"
                                                        aria-label="Eliminar <?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <i class="fa fa-trash"></i>
                                                        <span class="ml-1">Eliminar</span>
                                                    </button>

                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No se encontraron alumnos con los filtros aplicados</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Información de resultados -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Mostrando <?= $result->num_rows ?> de <?= $totalRegistros ?> registros
                                </p>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <?php if ($totalPaginas > 1):
                            // Construir parámetros GET para paginación
                            $getParams = $_GET;
                            unset($getParams['pagina']);
                            $queryString = http_build_query($getParams);
                            $queryString = $queryString ? "&" . $queryString : "";
                        ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                            <a class="page-link" href="indexalumno.php?pagina=<?= $i ?><?= htmlspecialchars($queryString, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

            </div> <!-- container -->

        </div> <!-- content -->
    </div> <!-- content-wrapper -->

</div> <!-- wrapper -->

<!-- ========================================================= -->
<!-- MODAL EDITAR CON VALIDACIONES EN TIEMPO REAL            -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Alumno</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <!-- Mensaje de éxito -->
                <div id="successMessageEdit" class="alert alert-success" style="display: none;">
                    <i class="fa fa-check-circle"></i> Alumno actualizado exitosamente
                </div>

                <form id="formEditarAlumno">
                    <!-- Token CSRF -->
                    <?= csrf_input() ?>

                    <!-- ID oculto -->
                    <input type="hidden" name="id" id="edit-id">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control">
                        <small class="text-danger" id="error-edit-nombre" style="display: none;"></small>
                    </div>

                    <!-- DNI -->
                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" id="edit-dni" class="form-control" maxlength="8">
                        <small class="text-danger" id="error-edit-dni" style="display: none;"></small>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control">
                        <small class="text-danger" id="error-edit-email" style="display: none;"></small>
                    </div>

                    <!-- Celular -->
                    <div class="mb-3">
                        <label>Celular</label>
                        <input type="text" name="celular" id="edit-celular" class="form-control" maxlength="9">
                        <small class="text-danger" id="error-edit-celular" style="display: none;"></small>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="btnGuardarCambios">
                    Guardar cambios
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL AGREGAR CON VALIDACIONES EN TIEMPO REAL            -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Agregar Alumno</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <!-- Mensaje de éxito -->
                <div id="successMessage" class="alert alert-success" style="display: none;">
                    <i class="fa fa-check-circle"></i> Alumno registrado exitosamente
                </div>

                <form id="formAgregarAlumno">
                    <!-- Token CSRF -->
                    <?= csrf_input() ?>

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control"
                            placeholder="Ingrese el nombre completo">
                        <small class="text-danger" id="error-nombre" style="display: none;"></small>
                    </div>

                    <!-- DNI -->
                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" id="dni" class="form-control"
                            placeholder="8 dígitos" maxlength="8">
                        <small class="text-danger" id="error-dni" style="display: none;"></small>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="ejemplo@correo.com">
                        <small class="text-danger" id="error-email" style="display: none;"></small>
                    </div>

                    <!-- Celular -->
                    <div class="mb-3">
                        <label>Celular</label>
                        <input type="text" name="celular" id="celular" class="form-control"
                            placeholder="9 dígitos" maxlength="9">
                        <small class="text-danger" id="error-celular" style="display: none;"></small>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="btnRegistrar">
                    Registrar alumno
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL CONFIRMAR ELIMINACIÓN                              -->
<!-- ========================================================= -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center py-4">

                <div class="mb-3">
                    <i class="fa fa-trash fa-3x text-danger"></i>
                </div>

                <h5 class="mb-3">¿Estás seguro de eliminar este alumno?</h5>

                <div class="alert alert-warning">
                    <strong id="delete-alumno-nombre"></strong><br>
                    <small>DNI: <span id="delete-alumno-dni"></span></small><br>
                    <small>Email: <span id="delete-alumno-email"></span></small>
                </div>

                <p class="text-muted">
                    <i class="fa fa-info-circle"></i> Esta acción no se puede deshacer
                </p>

                <input type="hidden" id="delete-alumno-id">

            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fa fa-trash"></i> Sí, eliminar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- SCRIPTS NECESARIOS                                       -->
<!-- ========================================================= -->
<!-- Scripts principales cargados desde includes/footer.php (evitar duplicados) -->

<style>
    /* Estilos para campos con error */
    .form-control.is-invalid {
        border-color: #dc3545;
    }
</style>

<script>
    (function() {
        function whenJQuery(cb) {
            if (window.jQuery) {
                cb();
            } else {
                setTimeout(function() {
                    whenJQuery(cb);
                }, 50);
            }
        }

        whenJQuery(function() {
            $(document).ready(function() {
                try {

                    // ============================================
                    // FUNCIONES AUXILIARES PARA VALIDACIONES
                    // ============================================

                    function limpiarErrores() {
                        $('.text-danger').hide().text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#successMessage').hide();
                        $('#successMessageEdit').hide();
                    }

                    function mostrarErrores(errores, prefijo = '') {
                        $.each(errores, function(campo, mensaje) {
                            $('#error-' + prefijo + campo).text('⚠️ ' + mensaje).show();
                            $('#' + prefijo + campo).addClass('is-invalid');
                        });
                    }

                    // Limpiar error cuando el usuario escribe en AGREGAR
                    $('#formAgregarAlumno input').on('input', function() {
                        const campo = $(this).attr('id');
                        $('#error-' + campo).hide();
                        $(this).removeClass('is-invalid');
                    });

                    // Limpiar error cuando el usuario escribe en EDITAR
                    $('#formEditarAlumno input').on('input', function() {
                        const campo = $(this).attr('id');
                        $('#error-' + campo).hide();
                        $(this).removeClass('is-invalid');
                    });

                    // ============================================
                    // EVENTO REGISTRAR ALUMNO CON AJAX
                    // ============================================

                    $('#btnRegistrar').click(function() {
                        limpiarErrores();

                        const formData = {
                            csrf_token: $('input[name="csrf_token"]').val(),
                            nombre: $('#nombre').val().trim(),
                            dni: $('#dni').val().trim(),
                            email: $('#email').val().trim(),
                            celular: $('#celular').val().trim()
                        };

                        // Deshabilitar botón mientras procesa
                        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registrando...');

                        $.ajax({
                            url: 'addalumno.php',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Mostrar mensaje de éxito
                                    $('#successMessage').show();
                                    $('#formAgregarAlumno')[0].reset();

                                    // Recargar página después de 1.5 segundos
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);

                                } else {
                                    // Mostrar errores debajo de cada campo
                                    mostrarErrores(response.errors);
                                    $('#btnRegistrar').prop('disabled', false).html('Registrar alumno');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX:', error);
                                alert('Error de conexión. Por favor, intenta nuevamente.');
                                $('#btnRegistrar').prop('disabled', false).html('Registrar alumno');
                            }
                        });
                    });

                    // ============================================
                    // EVENTO GUARDAR CAMBIOS (EDITAR) CON AJAX
                    // ============================================

                    $('#btnGuardarCambios').click(function() {
                        limpiarErrores();

                        const formData = {
                            csrf_token: $('#formEditarAlumno input[name="csrf_token"]').val(),
                            id: $('#edit-id').val(),
                            nombre: $('#edit-nombre').val().trim(),
                            dni: $('#edit-dni').val().trim(),
                            email: $('#edit-email').val().trim(),
                            celular: $('#edit-celular').val().trim()
                        };

                        // Deshabilitar botón mientras procesa
                        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

                        $.ajax({
                            url: 'editaralumno.php',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Mostrar mensaje de éxito
                                    $('#successMessageEdit').show();

                                    // Recargar página después de 1.5 segundos
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);

                                } else {
                                    // Mostrar errores debajo de cada campo
                                    mostrarErrores(response.errors, 'edit-');
                                    $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX:', error);
                                alert('Error de conexión. Por favor, intenta nuevamente.');
                                $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
                            }
                        });
                    });

                    // ============================================
                    // MODAL ELIMINAR - CARGAR DATOS
                    // ============================================

                    $('#deleteModal').on('show.bs.modal', function(event) {
                        const button = $(event.relatedTarget);
                        $('#delete-alumno-id').val(button.data('id'));
                        $('#delete-alumno-nombre').text(button.data('nombre'));
                        $('#delete-alumno-dni').text(button.data('dni'));
                        $('#delete-alumno-email').text(button.data('email'));
                    });

                    // ============================================
                    // EVENTO CONFIRMAR ELIMINACIÓN CON AJAX
                    // ============================================

                    $('#btnConfirmarEliminar').click(function() {

                        const id = $('#delete-alumno-id').val();
                        const nombre = $('#delete-alumno-nombre').text();

                        // Deshabilitar botón mientras procesa
                        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Eliminando...');

                        $.ajax({
                            url: 'deletealumno.php',
                            type: 'POST',
                            data: {
                                id: id,
                                csrf_token: $('input[name="csrf_token"]').val(),
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Cerrar modal
                                    $('#deleteModal').modal('hide');

                                    // Mostrar alerta de éxito
                                    const alertHtml = `
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <i class="fa fa-check-circle"></i> ${response.message}
                                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            </div>
                                        `;
                                    $('.container.mt-4').prepend(alertHtml);

                                    // Recargar página después de 1.5 segundos
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);

                                } else {
                                    // Cerrar modal
                                    $('#deleteModal').modal('hide');

                                    // Mostrar alerta de error
                                    const alertHtml = `
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <i class="fa fa-exclamation-triangle"></i> ${response.message}
                                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            </div>
                                        `;
                                    $('.container.mt-4').prepend(alertHtml);

                                    $('#btnConfirmarEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX:', error);
                                $('#deleteModal').modal('hide');
                                alert('Error de conexión. Por favor, intenta nuevamente.');
                                $('#btnConfirmarEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
                            }
                        });
                    });

                    // Resetear botón al cerrar modal
                    $('#deleteModal').on('hidden.bs.modal', function() {
                        $('#btnConfirmarEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
                    });

                    // ============================================
                    // LIMPIAR AL CERRAR MODALES
                    // ============================================

                    $('#addModal').on('hidden.bs.modal', function() {
                        $('#formAgregarAlumno')[0].reset();
                        limpiarErrores();
                        $('#btnRegistrar').prop('disabled', false).html('Registrar alumno');
                    });

                    $('#editModal').on('hidden.bs.modal', function() {
                        limpiarErrores();
                        $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
                    });

                    // ============================================
                    // MODAL EDITAR - CARGAR DATOS
                    // ============================================

                    $('#editModal').on('show.bs.modal', function(event) {
                        const button = $(event.relatedTarget);
                        $('#edit-id').val(button.data('id'));
                        $('#edit-nombre').val(button.data('nombre'));
                        $('#edit-dni').val(button.data('dni'));
                        $('#edit-email').val(button.data('email'));
                        $('#edit-celular').val(button.data('celular'));
                    });

                    // ============================================
                    // BUSCADOR EN TABLA (cliente) - conserva comportamiento previo
                    // ============================================

                    var busquedaEl = document.getElementById('busqueda');
                    if (busquedaEl) {
                        busquedaEl.addEventListener('input', function() {
                            const filtro = this.value.toLowerCase();
                            document.querySelectorAll('table tbody tr').forEach(fila => {
                                fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
                            });
                        });
                    }

                } catch (e) {
                    console.error('Error en scripts de indexalumno.php:', e);
                }
            });
        });
    })();
</script>

<?php include(__DIR__ . '/../../includes/footer.php'); ?>