<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">Lista de Cursos</h3>

                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Agregar Curso
                    </button>
                </div>

                <?php
                include("../../db.php");

                // Paginación
                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];
                $totalPaginas = ceil($total / $registros);

                // Obtener categorías y modalidades para los select
                $categorias = $conn->query("SELECT id, nombre FROM categoria");
                $modalidades = $conn->query("SELECT id, nombre FROM modalidad");

                // Consulta de cursos
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
                                <td><?= $row['nombre'] ?></td>
                                <td><?= $row['categoria'] ?></td>
                                <td><?= $row['modalidad'] ?></td>
                                <td><?= $row['fecha_inicio'] ?></td>
                                <td><?= $row['cupos'] ?></td>
                                <td>S/. <?= number_format($row['precio'], 2) ?></td>
                                <td><?= $row['estado'] ?></td>
                                <td>

                                    <!-- Botón editar -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nombre="<?= $row['nombre'] ?>"
                                        data-categoria="<?= $row['categoria_id'] ?>"
                                        data-modalidad="<?= $row['modalidad_id'] ?>"
                                        data-fecha="<?= $row['fecha_inicio'] ?>"
                                        data-cupos="<?= $row['cupos'] ?>"
                                        data-precio="<?= $row['precio'] ?>"
                                        data-estado="<?= $row['estado'] ?>">Editar</button>

                                    <!-- Botón eliminar -->
                                    <a href="deletecursos.php?id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que desea eliminar este curso?');">
                                        Eliminar
                                    </a>

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
<!-- MODAL EDITAR CURSO -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="editarcursos.php" method="POST">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Curso</h5>
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
                            <?php
                            $categorias->data_seek(0);
                            while ($cat = $categorias->fetch_assoc()):
                            ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Modalidad</label>
                        <select name="modalidad_id" id="edit-modalidad" class="form-control" required>
                            <?php
                            $modalidades->data_seek(0);
                            while ($mod = $modalidades->fetch_assoc()):
                            ?>
                                <option value="<?= $mod['id'] ?>"><?= $mod['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" id="edit-fecha" class="form-control" required>
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
                        <select name="estado" id="edit-estado" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
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
<!-- MODAL AGREGAR CURSO -->
<!-- ========================================================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="addcursos.php" method="POST">

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
                            <?php
                            $categorias->data_seek(0);
                            while ($cat = $categorias->fetch_assoc()):
                            ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Modalidad</label>
                        <select name="modalidad_id" class="form-control" required>
                            <?php
                            $modalidades->data_seek(0);
                            while ($mod = $modalidades->fetch_assoc()):
                            ?>
                                <option value="<?= $mod['id'] ?>"><?= $mod['nombre'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
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
                        <select name="estado" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Registrar curso</button>
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
        let button = $(event.relatedTarget);

        $('#edit-id').val(button.data('id'));
        $('#edit-nombre').val(button.data('nombre'));
        $('#edit-categoria').val(button.data('categoria'));
        $('#edit-modalidad').val(button.data('modalidad'));
        $('#edit-fecha').val(button.data('fecha'));
        $('#edit-cupos').val(button.data('cupos'));
        $('#edit-precio').val(button.data('precio'));
        $('#edit-estado').val(button.data('estado'));
    });
</script>