<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Matrículas</h3>

                    <!-- BOTÓN AGREGAR -->
                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar Matrícula
                    </button>
                </div>

                <?php
                include("../../db.php");

                $registrosPorPagina = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registrosPorPagina;

                $total = $conn->query("SELECT COUNT(*) FROM matricula")->fetch_row()[0];
                $paginas = ceil($total / $registrosPorPagina);

                $sql = "
                    SELECT m.*, a.nombre AS alumno, c.nombre AS curso, c.precio
                    FROM matricula m
                    JOIN alumno a ON m.alumno_id = a.id
                    JOIN curso c  ON m.curso_id = c.id
                    ORDER BY m.id DESC
                    LIMIT $inicio, $registrosPorPagina
                ";

                $result = $conn->query($sql);
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
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['alumno'] ?></td>
                                <td><?= $row['curso'] ?> (S/.<?= $row['precio'] ?>)</td>
                                <td><?= $row['estado'] ?></td>
                                <td><?= $row['fecha_inscripcion'] ?></td>

                                <td>
                                    <!-- BOTÓN EDITAR -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-alumno="<?= $row['alumno'] ?>"
                                        data-curso="<?= $row['curso_id'] ?>"
                                        data-estado="<?= $row['estado'] ?>"
                                        data-fecha="<?= $row['fecha_inscripcion'] ?>">
                                        Editar
                                    </button>

                                    <!-- BOTÓN ELIMINAR -->
                                    <a href="deletematriculas.php?id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que desea eliminar esta matrícula?');">
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
                                    <a class="page-link" href="indexmatriculas.php?pagina=<?= $i ?>"><?= $i ?></a>
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
<!-- MODAL EDITAR MATRÍCULA -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="editarmatriculas.php" method="POST">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Matrícula</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label>Alumno</label>
                        <input type="text" class="form-control" id="edit-alumno" readonly>
                    </div>

                    <?php
                    $cursos2 = $conn->query("SELECT id, nombre FROM curso ORDER BY nombre");
                    ?>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select name="curso_id" id="edit-curso" class="form-control" required>
                            <?php while ($c = $cursos2->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" name="fecha_inscripcion" id="edit-fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="edit-estado" class="form-control" required>
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
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
<!-- MODAL AGREGAR MATRÍCULA -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="addmatriculas.php" method="POST">

                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Agregar Matrícula</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">

                    <?php
                    $alumnos = $conn->query("SELECT id, nombre FROM alumno ORDER BY nombre");
                    $cursos  = $conn->query("SELECT id, nombre FROM curso ORDER BY nombre");
                    ?>

                    <div class="mb-3">
                        <label>Alumno</label>
                        <select name="alumno_id" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <?php while ($a = $alumnos->fetch_assoc()): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Curso</label>
                        <select name="curso_id" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <?php while ($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inscripción</label>
                        <input type="date" name="fecha_inscripcion" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="Matriculado">Matriculado</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- SCRIPTS (IGUAL QUE DOCENTES) -->
<!-- ========================================================= -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $('#editModal').on('show.bs.modal', function(event) {
        let b = $(event.relatedTarget);

        $('#edit-id').val(b.data('id'));
        $('#edit-alumno').val(b.data('alumno'));
        $('#edit-curso').val(b.data('curso'));
        $('#edit-fecha').val(b.data('fecha'));
        $('#edit-estado').val(b.data('estado'));
    });
</script>