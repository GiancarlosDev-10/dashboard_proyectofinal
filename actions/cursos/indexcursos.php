<?php
session_start();
include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <!-- ALERTA ELIMINADO (fallback por si usas redirect) -->
                <?php if (isset($_GET['delete']) && $_GET['delete'] === 'ok'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Curso eliminado correctamente.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => location.href = "indexcursos.php", 1500);
                    </script>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Cursos</h3>

                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar curso
                    </button>
                </div>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar curso...">
                </div>

                <?php
                include("../../db.php");

                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];
                $totalPaginas = ceil($total / $registros);

                // Selects
                $categorias = $conn->query("SELECT id, nombre FROM categoria");
                $modalidades = $conn->query("SELECT id, nombre FROM modalidad");

                $sql = "
                    SELECT c.*,
                           cat.nombre AS categoria,
                           m.nombre AS modalidad
                    FROM curso c
                    JOIN categoria cat ON c.categoria_id = cat.id
                    JOIN modalidad m ON c.modalidad_id = m.id
                    LIMIT $inicio, $registros
                ";
                $result = $conn->query($sql);
                ?>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Modalidad</th>
                            <th>Fecha Inicio</th>
                            <th>Cupos</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td><?= htmlspecialchars($row['categoria']) ?></td>
                                <td><?= htmlspecialchars($row['modalidad']) ?></td>
                                <td><?= htmlspecialchars($row['fecha_inicio']) ?></td>
                                <td><?= (int)$row['cupos'] ?></td>
                                <td>S/. <?= number_format((float)$row['precio'], 2) ?></td>
                                <td><?= htmlspecialchars($row['estado']) ?></td>
                                <td>
                                    <!-- EDITAR -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                                        data-categoria="<?= $row['categoria_id'] ?>"
                                        data-modalidad="<?= $row['modalidad_id'] ?>"
                                        data-fecha="<?= $row['fecha_inicio'] ?>"
                                        data-cupos="<?= $row['cupos'] ?>"
                                        data-precio="<?= $row['precio'] ?>"
                                        data-estado="<?= $row['estado'] ?>">
                                        <i class="fa fa-edit"></i> Editar
                                    </button>

                                    <!-- ELIMINAR -->
                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre']) ?>">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

                <!-- PAGINACIÓN -->
                <?php if ($totalPaginas > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="indexcursos.php?pagina=<?= $i ?>"><?= $i ?></a>
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
<!-- MODAL EDITAR CURSO (AJAX) -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Curso</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <div id="successMessageEditCurso" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Curso actualizado exitosamente
                </div>

                <form id="formEditarCurso">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label>Nombre del curso</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control">
                        <small class="text-danger" id="error-edit-nombre" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" id="edit-categoria_id" class="form-control">
                            <?php
                            $categorias->data_seek(0);
                            while ($cat = $categorias->fetch_assoc()):
                            ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-edit-categoria_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Modalidad</label>
                        <select name="modalidad_id" id="edit-modalidad_id" class="form-control">
                            <?php
                            $modalidades->data_seek(0);
                            while ($mod = $modalidades->fetch_assoc()):
                            ?>
                                <option value="<?= $mod['id'] ?>"><?= htmlspecialchars($mod['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-edit-modalidad_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" id="edit-fecha_inicio" class="form-control">
                        <small class="text-danger" id="error-edit-fecha_inicio" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Cupos</label>
                        <input type="number" name="cupos" id="edit-cupos" class="form-control">
                        <small class="text-danger" id="error-edit-cupos" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="precio" id="edit-precio" class="form-control">
                        <small class="text-danger" id="error-edit-precio" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="edit-estado" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <small class="text-danger" id="error-edit-estado" style="display:none;"></small>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="btnGuardarCambiosCurso">Guardar cambios</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL AGREGAR CURSO (AJAX) -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Agregar Curso</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <div id="successMessageAddCurso" class="alert alert-success" style="display:none;">
                    <i class="fa fa-check-circle"></i> Curso registrado exitosamente
                </div>

                <form id="formAgregarCurso">

                    <div class="mb-3">
                        <label>Nombre del curso</label>
                        <input type="text" name="nombre" id="nombre" class="form-control">
                        <small class="text-danger" id="error-nombre" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-control">
                            <?php
                            $categorias->data_seek(0);
                            while ($cat = $categorias->fetch_assoc()):
                            ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-categoria_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Modalidad</label>
                        <select name="modalidad_id" id="modalidad_id" class="form-control">
                            <?php
                            $modalidades->data_seek(0);
                            while ($mod = $modalidades->fetch_assoc()):
                            ?>
                                <option value="<?= $mod['id'] ?>"><?= htmlspecialchars($mod['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-modalidad_id" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                        <small class="text-danger" id="error-fecha_inicio" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Cupos</label>
                        <input type="number" name="cupos" id="cupos" class="form-control">
                        <small class="text-danger" id="error-cupos" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="precio" id="precio" class="form-control">
                        <small class="text-danger" id="error-precio" style="display:none;"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <small class="text-danger" id="error-estado" style="display:none;"></small>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="btnRegistrarCurso">Registrar curso</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL ELIMINAR CURSO (AJAX) -->
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
                <h5 class="mb-3">¿Estás seguro de eliminar este curso?</h5>
                <div class="alert alert-warning">
                    <strong id="delete-curso-nombre"></strong>
                </div>
                <input type="hidden" id="delete-curso-id">
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    Sí, eliminar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- SCRIPTS -->
<!-- ========================================================= -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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
            $('#successMessageAddCurso').hide();
            $('#successMessageEditCurso').hide();
        }

        function mostrarErrores(errores, prefijo = '') {
            $.each(errores, function(campo, mensaje) {
                $('#error-' + prefijo + campo).text('⚠️ ' + mensaje).show();
                $('#' + prefijo + campo).addClass('is-invalid');
            });
        }

        // limpiar error al escribir (ADD)
        $('#formAgregarCurso input, #formAgregarCurso select').on('input change', function() {
            const id = $(this).attr('id');
            $('#error-' + id).hide();
            $(this).removeClass('is-invalid');
        });

        // limpiar error al escribir (EDIT)
        $('#formEditarCurso input, #formEditarCurso select').on('input change', function() {
            const id = $(this).attr('id'); // ej: edit-nombre, edit-categoria_id...
            $('#error-' + id).hide();
            $(this).removeClass('is-invalid');
        });

        // ===========================
        // ADD CURSO (AJAX)
        // ===========================
        $('#btnRegistrarCurso').click(function() {
            limpiarErrores();

            const formData = {
                nombre: $('#nombre').val().trim(),
                categoria_id: $('#categoria_id').val(),
                modalidad_id: $('#modalidad_id').val(),
                fecha_inicio: $('#fecha_inicio').val(),
                cupos: $('#cupos').val(),
                precio: $('#precio').val(),
                estado: $('#estado').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registrando...');

            $.ajax({
                url: 'addcursos.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#successMessageAddCurso').show();
                        $('#formAgregarCurso')[0].reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarErrores(resp.errors || {});
                        $('#btnRegistrarCurso').prop('disabled', false).html('Registrar curso');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnRegistrarCurso').prop('disabled', false).html('Registrar curso');
                }
            });
        });

        // ===========================
        // CARGAR DATOS EN EDIT MODAL
        // ===========================
        $('#editModal').on('show.bs.modal', function(event) {
            limpiarErrores();
            const b = $(event.relatedTarget);

            $('#edit-id').val(b.data('id'));
            $('#edit-nombre').val(b.data('nombre'));
            $('#edit-categoria_id').val(b.data('categoria'));
            $('#edit-modalidad_id').val(b.data('modalidad'));
            $('#edit-fecha_inicio').val(b.data('fecha'));
            $('#edit-cupos').val(b.data('cupos'));
            $('#edit-precio').val(b.data('precio'));
            $('#edit-estado').val(b.data('estado'));
        });

        // ===========================
        // EDIT CURSO (AJAX)
        // ===========================
        $('#btnGuardarCambiosCurso').click(function() {
            limpiarErrores();

            const formData = {
                id: $('#edit-id').val(),
                nombre: $('#edit-nombre').val().trim(),
                categoria_id: $('#edit-categoria_id').val(),
                modalidad_id: $('#edit-modalidad_id').val(),
                fecha_inicio: $('#edit-fecha_inicio').val(),
                cupos: $('#edit-cupos').val(),
                precio: $('#edit-precio').val(),
                estado: $('#edit-estado').val()
            };

            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: 'editarcursos.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#successMessageEditCurso').show();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        mostrarErrores(resp.errors || {}, 'edit-');
                        $('#btnGuardarCambiosCurso').prop('disabled', false).html('Guardar cambios');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnGuardarCambiosCurso').prop('disabled', false).html('Guardar cambios');
                }
            });
        });

        // ===========================
        // DELETE MODAL (AJAX)
        // ===========================
        $('#deleteModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            $('#delete-curso-id').val(button.data('id'));
            $('#delete-curso-nombre').text(button.data('nombre'));
        });

        $('#btnConfirmarEliminar').click(function() {
            const id = $('#delete-curso-id').val();
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Eliminando...');

            $.ajax({
                url: 'deletecursos.php',
                type: 'POST',
                data: {
                    id
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#deleteModal').modal('hide');
                        setTimeout(() => location.reload(), 300);
                    } else {
                        alert(resp.message || 'No se pudo eliminar.');
                        $('#btnConfirmarEliminar').prop('disabled', false).html('Sí, eliminar');
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta nuevamente.');
                    $('#btnConfirmarEliminar').prop('disabled', false).html('Sí, eliminar');
                }
            });
        });

        // Reset botones al cerrar modales
        $('#addModal').on('hidden.bs.modal', function() {
            $('#formAgregarCurso')[0].reset();
            limpiarErrores();
            $('#btnRegistrarCurso').prop('disabled', false).html('Registrar curso');
        });

        $('#editModal').on('hidden.bs.modal', function() {
            limpiarErrores();
            $('#btnGuardarCambiosCurso').prop('disabled', false).html('Guardar cambios');
        });

        $('#deleteModal').on('hidden.bs.modal', function() {
            $('#btnConfirmarEliminar').prop('disabled', false).html('Sí, eliminar');
        });

        // Buscador
        document.getElementById('busqueda').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(fila => {
                fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            });
        });

    });
</script>

<?php include(__DIR__ . '/../../includes/footer.php'); ?>