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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Docentes</h3>

                    <?php
                    $rol_usuario = $_SESSION['admin_rol'] ?? 'alumno';
                    ?>
                    <?php if ($rol_usuario === 'admin'): ?>
                        <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                            <i class="fa fa-plus"></i> Agregar docente
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar docente...">
                </div>

                <?php
                include("../../db.php");

                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM docente")->fetch_row()[0];
                $paginas = ceil($total / $registros);

                // Obtener especialidades únicas para los desplegables
                $especialidades_result = $conn->query("SELECT DISTINCT especialidad FROM docente WHERE especialidad IS NOT NULL AND especialidad != '' ORDER BY especialidad");
                $especialidades = [];
                while ($row = $especialidades_result->fetch_assoc()) {
                    $especialidades[] = $row['especialidad'];
                }

                $result = $conn->query("SELECT * FROM docente LIMIT $inicio, $registros");
                ?>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th>DNI</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['especialidad'] ?? 'Sin especialidad', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($rol_usuario === 'admin'): ?>
                                        <!-- BOTÓN EDITAR -->
                                        <button class="btn btn-warning btn-sm"
                                            data-toggle="modal"
                                            data-target="#editModal"
                                            data-id="<?= (int)$row['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-especialidad="<?= htmlspecialchars($row['especialidad'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-dni="<?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </button>

                                        <!-- BOTÓN ELIMINAR -->
                                        <button class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#deleteModal"
                                            data-id="<?= (int)$row['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </button>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Solo lectura</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- PAGINACIÓN -->
                <?php if ($paginas > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="indexdocentes.php?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL EDITAR DOCENTE -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Docente</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div id="successEdit" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Docente actualizado correctamente
                </div>

                <form id="formEditarDocente">
                    <?= csrf_input() ?>
                    <input type="hidden" id="edit-id">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" id="edit-nombre" class="form-control" placeholder="Nombre completo">
                        <small class="text-danger" id="error-edit-nombre" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <select id="edit-especialidad-select" class="form-control">
                            <option value="">Seleccione una especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= htmlspecialchars($esp) ?>"><?= htmlspecialchars($esp) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error-edit-especialidad" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" id="edit-dni" class="form-control" maxlength="8" placeholder="8 dígitos">
                        <small class="text-danger" id="error-edit-dni" style="display:none;"></small>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="btnGuardarCambios">Guardar cambios</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL AGREGAR DOCENTE -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Docente</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div id="successAdd" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Docente registrado correctamente
                </div>

                <form id="formAgregarDocente">
                    <?= csrf_input() ?>

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" id="nombre" class="form-control" placeholder="Nombre completo">
                        <small class="text-danger" id="error-nombre" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <select id="especialidad-select" class="form-control">
                            <option value="">Seleccione una especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= htmlspecialchars($esp) ?>"><?= htmlspecialchars($esp) ?></option>
                            <?php endforeach; ?>
                            <option value="nueva">➕ Agregar nueva especialidad...</option>
                        </select>
                        <small class="text-danger" id="error-especialidad" style="display:none;"></small>

                        <!-- Campo para nueva especialidad (oculto inicialmente) -->
                        <div id="nueva-especialidad-container" style="display:none; margin-top:10px;">
                            <input type="text" id="nueva-especialidad" class="form-control" placeholder="Nombre de la nueva especialidad">
                            <small class="text-danger" id="error-nueva-especialidad" style="display:none;"></small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" id="dni" class="form-control" maxlength="8" placeholder="8 dígitos">
                        <small class="text-danger" id="error-dni" style="display:none;"></small>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="btnRegistrar">Registrar docente</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL ELIMINAR DOCENTE -->
<!-- ========================================================= -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirmar eliminación</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center py-4">
                <i class="fa fa-trash fa-3x text-danger mb-3"></i>
                <h5>¿Estás seguro de eliminar este docente?</h5>
                <div class="alert alert-warning mt-3">
                    <strong id="delete-nombre"></strong>
                </div>
                <p class="text-muted"><i class="fa fa-info-circle"></i> Esta acción no se puede deshacer</p>
                <input type="hidden" id="delete-id">
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                <button class="btn btn-danger" id="btnEliminar"><i class="fa fa-trash"></i> Sí, eliminar</button>
            </div>

        </div>
    </div>
</div>

<?php include(__DIR__ . '/../../includes/footer.php'); ?>

<style>
    .form-control.is-invalid {
        border-color: #dc3545;
    }
</style>

<script>
    $(document).ready(function() {

        function limpiarErrores() {
            $('.text-danger').hide().text('');
            $('.form-control').removeClass('is-invalid');
            $('#successAdd, #successEdit').hide();
        }

        function mostrarErrores(errores, prefijo = '') {
            $.each(errores, function(campo, mensaje) {
                $('#error-' + prefijo + campo).text('⚠️ ' + mensaje).show();
                $('#' + prefijo + campo).addClass('is-invalid');
            });
        }

        // Limpiar error al escribir (AGREGAR)
        $('#formAgregarDocente input, #formAgregarDocente select').on('input change', function() {
            const id = $(this).attr('id');
            $('#error-' + id).hide();
            $(this).removeClass('is-invalid');
        });

        // Limpiar error al escribir (EDITAR)
        $('#formEditarDocente input, #formEditarDocente select').on('input change', function() {
            const id = $(this).attr('id');
            $('#error-' + id).hide();
            $(this).removeClass('is-invalid');
        });

        // BUSCADOR
        $('#busqueda').on('input', function() {
            const f = this.value.toLowerCase();
            $('table tbody tr').each(function() {
                $(this).toggle($(this).text().toLowerCase().includes(f));
            });
        });

        // ===========================
        // MOSTRAR/OCULTAR CAMPO NUEVA ESPECIALIDAD (AGREGAR)
        // ===========================
        $('#especialidad-select').change(function() {
            if ($(this).val() === 'nueva') {
                $('#nueva-especialidad-container').slideDown();
                $('#nueva-especialidad').focus();
            } else {
                $('#nueva-especialidad-container').slideUp();
                $('#nueva-especialidad').val('');
                $('#error-nueva-especialidad').hide();
            }
        });

        // ===========================
        // MOSTRAR/OCULTAR CAMPO NUEVA ESPECIALIDAD (EDITAR)
        // ===========================
        $('#edit-especialidad-select').change(function() {
            if ($(this).val() === 'nueva') {
                $('#edit-nueva-especialidad-container').slideDown();
                $('#edit-nueva-especialidad').focus();
            } else {
                $('#edit-nueva-especialidad-container').slideUp();
                $('#edit-nueva-especialidad').val('');
                $('#error-edit-nueva-especialidad').hide();
            }
        });

        // ===========================
        // AGREGAR DOCENTE
        // ===========================
        $('#btnRegistrar').click(function() {
            limpiarErrores();

            const selectVal = $('#especialidad-select').val();
            const especialidadFinal = selectVal === 'nueva' ? $('#nueva-especialidad').val().trim() : selectVal;

            const formData = {
                nombre: $('#nombre').val().trim(),
                especialidad: especialidadFinal,
                dni: $('#dni').val().trim(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registrando...');

            $.ajax({
                url: 'adddocentes.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#successAdd').show();
                        $('#formAgregarDocente')[0].reset();
                        $('#nueva-especialidad-container').hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarErrores(resp.errors || {});
                        $('#btnRegistrar').prop('disabled', false).html('Registrar docente');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnRegistrar').prop('disabled', false).html('Registrar docente');
                }
            });
        });

        // ===========================
        // CARGAR DATOS EN MODAL EDITAR
        // ===========================
        $('#editModal').on('show.bs.modal', function(e) {
            const b = $(e.relatedTarget);
            limpiarErrores();

            $('#edit-id').val(b.data('id'));
            $('#edit-nombre').val(b.data('nombre'));
            $('#edit-especialidad-select').val(b.data('especialidad'));
            $('#edit-dni').val(b.data('dni'));
        });

        // ===========================
        // EDITAR DOCENTE
        // ===========================
        $('#btnGuardarCambios').click(function() {
            limpiarErrores();

            const formData = {
                id: $('#edit-id').val(),
                nombre: $('#edit-nombre').val().trim(),
                especialidad: $('#edit-especialidad-select').val(),
                dni: $('#edit-dni').val().trim(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: 'editardocentes.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#successEdit').show();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarErrores(resp.errors || {}, 'edit-');
                        $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
                }
            });
        });

        // ===========================
        // ELIMINAR DOCENTE
        // ===========================
        $('#deleteModal').on('show.bs.modal', function(e) {
            const b = $(e.relatedTarget);
            $('#delete-id').val(b.data('id'));
            $('#delete-nombre').text(b.data('nombre'));
        });

        $('#btnEliminar').click(function() {
            const id = $('#delete-id').val();

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Eliminando...');

            $.ajax({
                url: 'deletedocentes.php',
                type: 'POST',
                data: {
                    id: id,
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#deleteModal').modal('hide');
                        setTimeout(() => location.reload(), 300);
                    } else {
                        alert(resp.message || 'No se pudo eliminar.');
                        $('#btnEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
                }
            });
        });

        // ===========================
        // RESETEAR MODALES AL CERRAR
        // ===========================
        $('#addModal').on('hidden.bs.modal', function() {
            $('#formAgregarDocente')[0].reset();
            $('#nueva-especialidad-container').hide();
            limpiarErrores();
            $('#btnRegistrar').prop('disabled', false).html('Registrar docente');
        });

        $('#editModal').on('hidden.bs.modal', function() {
            limpiarErrores();
            $('#btnGuardarCambios').prop('disabled', false).html('Guardar cambios');
        });

        $('#deleteModal').on('hidden.bs.modal', function() {
            $('#btnEliminar').prop('disabled', false).html('<i class="fa fa-trash"></i> Sí, eliminar');
        });

    });
</script>