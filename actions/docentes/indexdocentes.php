<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Docentes</h3>

                    <!-- BOTÓN AGREGAR -->
                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar Docente
                    </button>
                </div>

                <?php
                include("../../db.php");

                $registrosPorPagina = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registrosPorPagina;

                $total = $conn->query("SELECT COUNT(*) FROM docente")->fetch_row()[0];
                $paginas = ceil($total / $registrosPorPagina);

                $result = $conn->query("SELECT * FROM docente LIMIT $inicio, $registrosPorPagina");
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
                                <td><?= $row['nombre'] ?></td>
                                <td><?= $row['especialidad'] ?></td>
                                <td><?= $row['dni'] ?></td>
                                <td>

                                    <!-- BOTÓN EDITAR -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nombre="<?= $row['nombre'] ?>"
                                        data-especialidad="<?= $row['especialidad'] ?>"
                                        data-dni="<?= $row['dni'] ?>">
                                        Editar
                                    </button>

                                    <!-- BOTÓN ELIMINAR -->
                                    <a href="deletedocentes.php?id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que desea eliminar?');">
                                        Eliminar
                                    </a>

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

</div> <!-- WRAPPER -->


<!-- ========================================================= -->
<!-- MODAL EDITAR DOCENTE -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="editardocentes.php" method="POST">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Docente</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" name="especialidad" id="edit-especialidad" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" id="edit-dni" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- MODAL AGREGAR DOCENTE -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="adddocentes.php" method="POST">

                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Agregar Docente</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Registrar docente</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- SCRIPTS -->
<!-- ========================================================= -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $('#editModal').on('show.bs.modal', function(event) {
        let b = $(event.relatedTarget);

        $('#edit-id').val(b.data('id'));
        $('#edit-nombre').val(b.data('nombre'));
        $('#edit-especialidad').val(b.data('especialidad'));
        $('#edit-dni').val(b.data('dni'));
    });
</script>