<?php include(__DIR__ . '/../../includes/header.php'); ?>
<?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

        <div class="container mt-4">

            <!-- ALERTAS -->
            <?php if (isset($_GET['add']) && $_GET['add'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Curso registrado correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit']) && $_GET['edit'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Curso actualizado correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['delete']) && $_GET['delete'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Curso eliminado correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- TITULO + BOTON -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">Lista de Cursos</h3>
                <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i> Agregar Curso
                </button>
            </div>

            <!-- BUSCADOR -->
            <div class="mb-3">
                <input type="text" id="busqueda" class="form-control" placeholder="Buscar curso...">
            </div>

            <?php
            include("../../db.php");

            // PAGINACION
            $registrosPorPagina = 10;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($pagina < 1) $pagina = 1;
            $inicio = ($pagina - 1) * $registrosPorPagina;

            $totalRegistros = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];
            $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

            // CONSULTA CURSOS + JOINS
            $sql = "
            SELECT c.*, 
                   cat.nombre AS categoria,
                   m.nombre AS modalidad,
                   d.nombre AS docente
            FROM curso c
            INNER JOIN categoria cat ON c.categoria_id = cat.id
            INNER JOIN modalidad m ON c.modalidad_id = m.id
            INNER JOIN docente d ON c.docente_id = d.id
            ORDER BY c.id DESC
            LIMIT $inicio, $registrosPorPagina
            ";
            $result = $conn->query($sql);

            // SELECT PARA FORMULARIOS
            $categorias = $conn->query("SELECT * FROM categoria");
            $modalidades = $conn->query("SELECT * FROM modalidad");
            $docentes = $conn->query("SELECT * FROM docente");
            ?>

            <!-- TABLA -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Curso</th>
                        <th>Categoría</th>
                        <th>Modalidad</th>
                        <th>Docente</th>
                        <th>Fecha inicio</th>
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
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['categoria'] ?></td>
                            <td><?= $row['modalidad'] ?></td>
                            <td><?= $row['docente'] ?></td>
                            <td><?= $row['fecha_inicio'] ?></td>
                            <td><?= $row['cupos'] ?></td>
                            <td>S/ <?= number_format($row['precio'], 2) ?></td>
                            <td><?= $row['estado'] ?></td>

                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-nombre="<?= $row['nombre'] ?>"
                                    data-categoria="<?= $row['categoria_id'] ?>"
                                    data-modalidad="<?= $row['modalidad_id'] ?>"
                                    data-docente="<?= $row['docente_id'] ?>"
                                    data-fecha="<?= $row['fecha_inicio'] ?>"
                                    data-duracion="<?= $row['duracion'] ?>"
                                    data-cupos="<?= $row['cupos'] ?>"
                                    data-precio="<?= $row['precio'] ?>"
                                    data-estado="<?= $row['estado'] ?>">
                                    Editar
                                </button>

                                <a href="/admin_php/actions/cursos/deletecursos.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro de eliminar este curso?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- PAGINACION -->
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

<!-- MODAL AGREGAR -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="addcursos.php" method="POST" class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Agregar Curso</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label>Nombre del curso</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Categoría</label>
                    <select name="categoria_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $categorias2 = $conn->query("SELECT * FROM categoria");
                        while ($cat = $categorias2->fetch_assoc()):
                        ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Modalidad</label>
                    <select name="modalidad_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $modalidades2 = $conn->query("SELECT * FROM modalidad");
                        while ($mod = $modalidades2->fetch_assoc()):
                        ?>
                            <option value="<?= $mod['id'] ?>"><?= $mod['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Docente</label>
                    <select name="docente_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $docentes2 = $conn->query("SELECT * FROM docente");
                        while ($doc = $docentes2->fetch_assoc()):
                        ?>
                            <option value="<?= $doc['id'] ?>"><?= $doc['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Duración (horas)</label>
                    <input type="number" name="duracion" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Cupos</label>
                    <input type="number" name="cupos" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Registrar curso</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="editarcursos.php" method="POST" class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Editar Curso</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id" id="edit-id">

                <div class="mb-3">
                    <label>Nombre del curso</label>
                    <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Categoría</label>
                    <select name="categoria_id" id="edit-categoria" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $categorias3 = $conn->query("SELECT * FROM categoria");
                        while ($cat = $categorias3->fetch_assoc()):
                        ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Modalidad</label>
                    <select name="modalidad_id" id="edit-modalidad" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $modalidades3 = $conn->query("SELECT * FROM modalidad");
                        while ($mod = $modalidades3->fetch_assoc()):
                        ?>
                            <option value="<?= $mod['id'] ?>"><?= $mod['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Docente</label>
                    <select name="docente_id" id="edit-docente" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $docentes3 = $conn->query("SELECT * FROM docente");
                        while ($doc = $docentes3->fetch_assoc()):
                        ?>
                            <option value="<?= $doc['id'] ?>"><?= $doc['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" id="edit-fecha" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Duración (horas)</label>
                    <input type="number" name="duracion" id="edit-duracion" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Cupos</label>
                    <input type="number" name="cupos" id="edit-cupos" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" id="edit-precio" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" id="edit-estado" class="form-control" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
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
        const filas = document.querySelectorAll('table tbody tr');

        filas.forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });

    // LLENAR MODAL EDITAR
    $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);

        $('#edit-id').val(button.data('id'));
        $('#edit-nombre').val(button.data('nombre'));
        $('#edit-categoria').val(button.data('categoria'));
        $('#edit-modalidad').val(button.data('modalidad'));
        $('#edit-docente').val(button.data('docente'));
        $('#edit-fecha').val(button.data('fecha'));
        $('#edit-duracion').val(button.data('duracion'));
        $('#edit-cupos').val(button.data('cupos'));
        $('#edit-precio').val(button.data('precio'));
        $('#edit-estado').val(button.data('estado'));
    });
</script>