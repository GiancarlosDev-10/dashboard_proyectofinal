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
                    <h3 class="text-primary mb-0">Lista de Docentes</h3>

                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar docente
                    </button>
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
                                <td><?= htmlspecialchars($row['especialidad'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <!-- BOTÓN EDITAR -->
                                    <button class="btn btn-warning"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= (int)$row['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-especialidad="<?= htmlspecialchars($row['especialidad'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-dni="<?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fa fa-edit"></i> Editar
                                    </button>

                                    <!-- BOTÓN ELIMINAR -->
                                    <button class="btn btn-danger"
                                        data-toggle="modal"
                                        data-target="#deleteModal"
                                        data-id="<?= (int)$row['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
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
                    Docente actualizado correctamente
                </div>

                <form id="formEditarDocente">
                    <input type="hidden" id="edit-id">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" id="edit-nombre" class="form-control">
                        <small class="text-danger" id="error-edit-nombre"></small>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" id="edit-especialidad" class="form-control">
                        <small class="text-danger" id="error-edit-especialidad"></small>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" id="edit-dni" class="form-control">
                        <small class="text-danger" id="error-edit-dni"></small>
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
                    Docente registrado correctamente
                </div>

                <form id="formAgregarDocente">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" id="nombre" class="form-control">
                        <small class="text-danger" id="error-nombre"></small>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" id="especialidad" class="form-control">
                        <small class="text-danger" id="error-especialidad"></small>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" id="dni" class="form-control">
                        <small class="text-danger" id="error-dni"></small>
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
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                ¿Eliminar al docente <strong id="delete-nombre"></strong>?
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

            $.post('adddocentes.php', {
                nombre: $('#nombre').val(),
                especialidad: $('#especialidad').val(),
                dni: $('#dni').val()
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
            $('#edit-nombre').val(b.data('nombre'));
            $('#edit-especialidad').val(b.data('especialidad'));
            $('#edit-dni').val(b.data('dni'));
        });

        // EDITAR
        $('#btnGuardarCambios').click(function() {
            limpiarErrores();

            $.post('editardocentes.php', {
                id: $('#edit-id').val(),
                nombre: $('#edit-nombre').val(),
                especialidad: $('#edit-especialidad').val(),
                dni: $('#edit-dni').val()
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
            $('#delete-nombre').text(b.data('nombre'));
        });

        $('#btnEliminar').click(function() {
            $.post('deletedocentes.php', {
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