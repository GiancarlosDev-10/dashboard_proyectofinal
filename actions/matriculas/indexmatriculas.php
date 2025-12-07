<?php include(__DIR__ . '/../../includes/header.php'); ?>
<?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

        <div class="container mt-4">

            <!-- ALERTAS -->
            <?php if (isset($_GET['add']) && $_GET['add'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Matrícula registrada correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit']) && $_GET['edit'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Matrícula actualizada correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['delete']) && $_GET['delete'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Matrícula eliminada correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- TÍTULO Y BOTÓN -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">Lista de Matrículas</h3>
                <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i> Registrar Matrícula
                </button>
            </div>

            <!-- BUSCADOR -->
            <div class="mb-3">
                <input type="text" id="busqueda" class="form-control" placeholder="Buscar matrícula...">
            </div>

            <?php
            include("../../db.php");

            // PAGINACIÓN
            $registros = 10;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($pagina < 1) $pagina = 1;
            $inicio = ($pagina - 1) * $registros;

            $total = $conn->query("SELECT COUNT(*) FROM matricula")->fetch_row()[0];
            $paginas = ceil($total / $registros);

            // CONSULTA CON JOIN
            $sql = "
            SELECT 
                m.*,
                a.nombre AS alumno,
                c.nombre AS curso
            FROM matricula m
            INNER JOIN alumno a ON m.alumno_id = a.id
            INNER JOIN curso c ON m.curso_id = c.id
            ORDER BY m.id DESC
            LIMIT $inicio, $registros
            ";

            $result = $conn->query($sql);

            // DATOS PARA SELECTS
            $alumnos  = $conn->query("SELECT id, nombre FROM alumno ORDER BY nombre ASC");
            $cursos   = $conn->query("SELECT id, nombre FROM curso ORDER BY nombre ASC");
            ?>

            <!-- TABLA -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Alumno</th>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th>Fecha Inscripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['alumno'] ?></td>
                            <td><?= $row['curso'] ?></td>
                            <td><?= $row['estado'] ?></td>
                            <td><?= $row['fecha_inscripcion'] ?></td>

                            <td>
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

                                <a href="/admin_php/actions/matriculas/deletematriculas.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro de eliminar esta matrícula?');">
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

<!-- MODAL AGREGAR -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="addmatriculas.php" method="POST" class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Registrar Matrícula</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

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
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="Activo">Activo</option>
                        <option value="Retirado">Retirado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha de inscripción</label>
                    <input type="date" name="fecha_inscripcion" class="form-control" required>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Registrar</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="editarmatriculas.php" method="POST" class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Editar Matrícula</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id" id="edit-id">

                <div class="mb-3">
                    <label>Alumno</label>
                    <input type="text" id="edit-alumno" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label>Curso</label>
                    <select name="curso_id" id="edit-curso" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $cursos2 = $conn->query("SELECT * FROM curso ORDER BY nombre ASC");
                        while ($cx = $cursos2->fetch_assoc()):
                        ?>
                            <option value="<?= $cx['id'] ?>"><?= $cx['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" id="edit-estado" class="form-control" required>
                        <option value="Activo">Activo</option>
                        <option value="Retirado">Retirado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha inscripción</label>
                    <input type="date" id="edit-fecha" class="form-control" disabled>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Guardar Cambios</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    // Buscador
    document.getElementById('busqueda').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll("table tbody tr");

        filas.forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? "" : "none";
        });
    });

    // CARGAR DATOS EN MODAL EDITAR
    $('#editModal').on('show.bs.modal', function(event) {

        const btn = $(event.relatedTarget);

        $('#edit-id').val(btn.data('id'));
        $('#edit-alumno').val(btn.data('alumno'));
        $('#edit-curso').val(btn.data('curso'));
        $('#edit-estado').val(btn.data('estado'));
        $('#edit-fecha').val(btn.data('fecha'));
    });
</script>