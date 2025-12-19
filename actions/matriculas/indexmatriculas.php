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
                    <h3 class="text-primary mb-0">Lista de Matrículas</h3>

                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar matrícula
                    </button>
                </div>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar matrícula...">
                </div>

                <?php
                include("../../db.php");

                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM matricula")->fetch_row()[0];
                $paginas = ceil($total / $registros);

                $sql = "
                    SELECT m.*, 
                           a.nombre AS alumno,
                           c.nombre AS curso,
                           c.precio
                    FROM matricula m
                    JOIN alumno a ON m.alumno_id = a.id
                    JOIN curso c ON m.curso_id = c.id
                    ORDER BY m.id DESC
                    LIMIT $inicio, $registros
                ";

                $result = $conn->query($sql);

                // Selects
                $alumnos = $conn->query("SELECT id, nombre FROM alumno ORDER BY nombre");
                $cursos  = $conn->query("SELECT id, nombre FROM curso ORDER BY nombre");
                ?>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Alumno</th>
                            <th>Curso</th>
                            <th>Estado</th>
                            <th>Fecha inscripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= (int)$row['id'] ?></td>
                                <td><?= htmlspecialchars($row['alumno'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['curso'], ENT_QUOTES, 'UTF-8') ?> (S/.<?= number_format($row['precio'], 2) ?>)</td>
                                <td>
                                    <?php if ($row['estado'] === 'Matriculado'): ?>
                                        <span class="badge badge-success">Matriculado</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['fecha_inscripcion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <!-- EDITAR -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= (int)$row['id'] ?>"
                                        data-alumno="<?= htmlspecialchars($row['alumno'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-curso="<?= (int)$row['curso_id'] ?>"
                                        data-fecha="<?= htmlspecialchars($row['fecha_inscripcion'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-estado="<?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fa fa-edit"></i> Editar
                                    </button>

                                    <!-- ELIMINAR -->
                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-id="<?= (int)$row['id'] ?>"
                                        data-alumno="<?= htmlspecialchars($row['alumno'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-curso="<?= htmlspecialchars($row['curso'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
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
                                    <a class="page-link" href="indexmatriculas.php?pagina=<?= $i ?>"><?= $i ?></a>
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
<!-- MODAL EDITAR MATRÍCULA -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Matrícula</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div id="successEdit" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Matrícula actualizada correctamente
                </div>

                <form id="formEditarMatricula">
                    <?= csrf_input() ?>
                    <input type="hidden" id="edit-id">

                    <div class="mb-3">
                        <label>Alumno</label>
                        <input type="text" id="edit-alumno" class="form-control-plaintext" readonly style="background-color: #f8f9fa; padding: 0.375rem 0.75rem; border: 1px solid #ced4da; border-radius: 0.25rem;">
                        <small class="text-muted">El alumno no se puede cambiar</small>
                    </div>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select id="edit-curso" class="form-control">
                            <?php
                            $cursos->data_seek(0);
                            while ($c = $cursos->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-edit-curso_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" id="edit-fecha" class="form-control">
                        <small class="text-danger" id="error-edit-fecha_inscripcion" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select id="edit-estado" class="form-control">
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                        <small class="text-danger" id="error-edit-estado" style="display:none;"></small>
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
<!-- MODAL AGREGAR MATRÍCULA -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Matrícula</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div id="successAdd" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Matrícula registrada correctamente
                </div>

                <form id="formAgregarMatricula">
                    <?= csrf_input() ?>

                    <div class="mb-3">
                        <label>Alumno</label>
                        <select id="alumno_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php
                            $alumnos->data_seek(0);
                            while ($a = $alumnos->fetch_assoc()):
                            ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-alumno_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select id="curso_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php
                            $cursos->data_seek(0);
                            while ($c = $cursos->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-curso_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" id="fecha_inscripcion" class="form-control">
                        <small class="text-danger" id="error-fecha_inscripcion" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select id="estado" class="form-control">
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                        <small class="text-danger" id="error-estado" style="display:none;"></small>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="btnRegistrar">Registrar</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL ELIMINAR MATRÍCULA -->
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
                <h5>¿Eliminar la matrícula?</h5>
                <div class="alert alert-warning mt-3">
                    <strong>Alumno:</strong> <span id="delete-alumno"></span><br>
                    <strong>Curso:</strong> <span id="delete-curso"></span>
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

        // Limpiar errores al escribir
        $('#formAgregarMatricula select, #formAgregarMatricula input').on('change input', function() {
            const id = $(this).attr('id');
            $('#error-' + id).hide();
            $(this).removeClass('is-invalid');
        });

        $('#formEditarMatricula select, #formEditarMatricula input').on('change input', function() {
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
        // AGREGAR MATRÍCULA
        // ===========================
        $('#btnRegistrar').click(function() {
            limpiarErrores();

            const formData = {
                alumno_id: $('#alumno_id').val(),
                curso_id: $('#curso_id').val(),
                fecha_inscripcion: $('#fecha_inscripcion').val(),
                estado: $('#estado').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registrando...');

            $.ajax({
                url: 'addmatriculas.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#successAdd').show();
                        $('#formAgregarMatricula')[0].reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarErrores(resp.errors || {});
                        $('#btnRegistrar').prop('disabled', false).html('Registrar');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnRegistrar').prop('disabled', false).html('Registrar');
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
            $('#edit-alumno').val(b.data('alumno'));
            $('#edit-curso').val(b.data('curso'));
            $('#edit-fecha').val(b.data('fecha'));
            $('#edit-estado').val(b.data('estado'));
        });

        // ===========================
        // EDITAR MATRÍCULA
        // ===========================
        $('#btnGuardarCambios').click(function() {
            limpiarErrores();

            const formData = {
                id: $('#edit-id').val(),
                curso_id: $('#edit-curso').val(),
                fecha_inscripcion: $('#edit-fecha').val(),
                estado: $('#edit-estado').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: 'editarmatriculas.php',
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
        // ELIMINAR MATRÍCULA
        // ===========================
        $('#deleteModal').on('show.bs.modal', function(e) {
            const b = $(e.relatedTarget);
            $('#delete-id').val(b.data('id'));
            $('#delete-alumno').text(b.data('alumno'));
            $('#delete-curso').text(b.data('curso'));
        });

        $('#btnEliminar').click(function() {
            const id = $('#delete-id').val();

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Eliminando...');

            $.ajax({
                url: 'deletematriculas.php',
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
            $('#formAgregarMatricula')[0].reset();
            limpiarErrores();
            $('#btnRegistrar').prop('disabled', false).html('Registrar');
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