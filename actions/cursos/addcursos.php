<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include '../../includes/topbar.php'; ?>

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">Lista de Cursos</h3>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus"></i> Agregar curso
                </button>
            </div>
            <div class="mb-3">
                <input type="text" id="busqueda" class="form-control" placeholder="Buscar curso...">
            </div>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Duración</th>
                        <th>Cupos</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("../../db.php");
                    // Paginación
                    $registrosPorPagina = 10;
                    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    $inicio = ($pagina - 1) * $registrosPorPagina;
                    // Total de registros
                    $totalRegistros = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];
                    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
                    // Consulta con LIMIT
                    $result = $conn->query("SELECT * FROM curso LIMIT $inicio, $registrosPorPagina");
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['fecha_inicio'] ?></td>
                            <td><?= $row['duracion'] ?></td>
                            <td><?= $row['cupos'] ?></td>
                            <td><?= $row['precio'] ?></td>
                            <td><?= $row['estado'] ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-nombre="<?= $row['nombre'] ?>"
                                    data-fecha="<?= $row['fecha_inicio'] ?>"
                                    data-duracion="<?= $row['duracion'] ?>"
                                    data-cupos="<?= $row['cupos'] ?>"
                                    data-precio="<?= $row['precio'] ?>"
                                    data-estado="<?= $row['estado'] ?>">
                                    Editar
                                </button>
                                <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro de eliminar este curso?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <!-- Modal de edición -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="editar.php" method="POST" class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Editar Curso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Fecha de Inicio</label>
                            <input type="text" name="fecha" id="edit-fecha" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Duración</label>
                            <input type="text" name="duracion" id="edit-duracion" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Cupos</label>
                            <input type="text" name="cupos" id="edit-cupos" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Precio</label>
                            <input type="text" name="precio" id="edit-precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <input type="text" name="estado" id="edit-estado" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Guardar cambios</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para agregar curso -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="agregar.php" method="POST" class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white">Agregar Curso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Fecha de Inicio</label>
                            <input type="text" name="fecha" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Duración</label>
                            <input type="text" name="duracion" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Cupos</label>
                            <input type="text" name="cupos" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Precio</label>
                            <input type="text" name="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <input type="text" name="estado" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Registrar curso</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Cargar datos en el modal de edición
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('edit-id').value = button.getAttribute('data-id');
                document.getElementById('edit-nombre').value = button.getAttribute('data-nombre');
                document.getElementById('edit-fecha_inicio').value = button.getAttribute('data-fecha');
                document.getElementById('edit-duracion').value = button.getAttribute('data-duracion');
                document.getElementById('edit-cupos').value = button.getAttribute('data-cupos');
                document.getElementById('edit-precio').value = button.getAttribute('data-precio');
                document.getElementById('edit-estado').value = button.getAttribute('data-estado');
            });

            // Búsqueda en la tabla de alumnos
            document.getElementById('busqueda').addEventListener('input', function() {
                const filtro = this.value.toLowerCase();
                const filas = document.querySelectorAll('table tbody tr');
                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    fila.style.display = texto.includes(filtro) ? '' : 'none';
                });
            });
        </script>
    </div>
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/chart.js/Chart.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
</div>