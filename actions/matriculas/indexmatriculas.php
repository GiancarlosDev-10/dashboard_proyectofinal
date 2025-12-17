<?php
session_start();
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
                                <td><?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['fecha_inscripcion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <!-- EDITAR -->
                                    <button class="btn btn-warning"
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
                                    <button class="btn btn-danger"
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
                    Matrícula actualizada correctamente
                </div>

                <form id="formEditarMatricula">
                    <input type="hidden" id="edit-id">

                    <div class="mb-3">
                        <label>Alumno</label>
                        <input type="text" id="edit-alumno" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select id="edit-curso" class="form-control">
                            <?php while ($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-edit-curso_id"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" id="edit-fecha" class="form-control">
                        <small class="text-danger" id="error-edit-fecha_inscripcion"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select id="edit-estado" class="form-control">
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                        <small class="text-danger" id="error-edit-estado"></small>
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
                    Matrícula registrada correctamente
                </div>

                <form id="formAgregarMatricula">

                    <div class="mb-3">
                        <label>Alumno</label>
                        <select id="alumno_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php $alumnos->data_seek(0);
                            while ($a = $alumnos->fetch_assoc()): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-alumno_id"></small>
                    </div>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select id="curso_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php $cursos->data_seek(0);
                            while ($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-danger" id="error-curso_id"></small>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" id="fecha_inscripcion" class="form-control">
                        <small class="text-danger" id="error-fecha_inscripcion"></small>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select id="estado" class="form-control">
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                        <small class="text-danger" id="error-estado"></small>
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
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                ¿Eliminar la matrícula de <strong id="delete-alumno"></strong>
                en el curso <strong id="delete-curso"></strong>?
                <input type="hidden" id="delete-id">
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="btnEliminar">Eliminar</button>
            </div>

        </div>
    </div>
</div>

<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {

        function limpiarErrores() {
            $('.text-danger').text('');
            $('#successAdd, #successEdit').hide();
        }

        // BUSCADOR
        $('#busqueda').on('input', function() {
            const f = this.value.toLowerCase();
            $('table tbody tr').each(function() {
                $(this).toggle($(this).text().toLowerCase().includes(f));
            });
        });

        // AGREGAR
        $('#btnRegistrar').click(function() {
            limpiarErrores();

            $.post('addmatriculas.php', {
                alumno_id: $('#alumno_id').val(),
                curso_id: $('#curso_id').val(),
                fecha_inscripcion: $('#fecha_inscripcion').val(),
                estado: $('#estado').val()
            }, function(resp) {
                if (resp.success) {
                    $('#successAdd').show();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    $.each(resp.errors, (c, m) => $('#error-' + c).text(m));
                }
            }, 'json');
        });

        // CARGAR EDIT
        $('#editModal').on('show.bs.modal', function(e) {
            const b = $(e.relatedTarget);
            limpiarErrores();
            $('#edit-id').val(b.data('id'));
            $('#edit-alumno').val(b.data('alumno'));
            $('#edit-curso').val(b.data('curso'));
            $('#edit-fecha').val(b.data('fecha'));
            $('#edit-estado').val(b.data('estado'));
        });

        // EDITAR
        $('#btnGuardarCambios').click(function() {
            limpiarErrores();

            $.post('editarmatriculas.php', {
                id: $('#edit-id').val(),
                curso_id: $('#edit-curso').val(),
                fecha_inscripcion: $('#edit-fecha').val(),
                estado: $('#edit-estado').val()
            }, function(resp) {
                if (resp.success) {
                    $('#successEdit').show();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    $.each(resp.errors, (c, m) => $('#error-edit-' + c).text(m));
                }
            }, 'json');
        });

        // ELIMINAR
        $('#deleteModal').on('show.bs.modal', function(e) {
            const b = $(e.relatedTarget);
            $('#delete-id').val(b.data('id'));
            $('#delete-alumno').text(b.data('alumno'));
            $('#delete-curso').text(b.data('curso'));
        });

        $('#btnEliminar').click(function() {
            $.post('deletematriculas.php', {
                id: $('#delete-id').val()
            }, function(resp) {
                if (resp.success) {
                    location.reload();
                } else {
                    alert(resp.message);
                }
            }, 'json');
        });

    });
</script>

<?php include(__DIR__ . '/../../includes/footer.php'); ?>