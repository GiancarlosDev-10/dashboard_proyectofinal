<?php include(__DIR__ . '/../../includes/header.php'); ?>
<?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

        <div class="container mt-4">

            <!-- ALERTA: Alumno eliminado -->
            <?php if (isset($_GET['delete']) && $_GET['delete'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Alumno eliminado correctamente.
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>

                <script>
                    setTimeout(() => {
                        window.location.href = "indexalumno.php";
                    }, 1500);
                </script>
            <?php endif; ?>

            <!-- ALERTA: Error al eliminar -->
            <?php if (isset($_GET['delete_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['delete_error']) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>

                <script>
                    setTimeout(() => {
                        window.location.href = "indexalumno.php";
                    }, 2000);
                </script>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">Lista de Alumnos</h3>

                <!-- Botón para abrir modal de agregar -->
                <button class="btn btn-success" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i> Agregar alumno
                </button>
            </div>

            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" id="busqueda" class="form-control" placeholder="Buscar alumno...">
            </div>

            <!-- Tabla -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include("../../db.php");

                    $registrosPorPagina = 10;
                    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    if ($pagina < 1) $pagina = 1;
                    $inicio = ($pagina - 1) * $registrosPorPagina;

                    $totalRegistros = $conn->query("SELECT COUNT(*) FROM alumno")->fetch_row()[0];
                    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

                    $result = $conn->query("SELECT * FROM alumno LIMIT $inicio, $registrosPorPagina");

                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['dni'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['celular'] ?></td>
                            <td>
                                <!-- Botón para EDITAR -->
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editModal"
                                    data-id="<?= $row['id'] ?>"
                                    data-nombre="<?= $row['nombre'] ?>"
                                    data-dni="<?= $row['dni'] ?>"
                                    data-email="<?= $row['email'] ?>"
                                    data-celular="<?= $row['celular'] ?>">
                                    Editar
                                </button>

                                <!-- Eliminar -->
                                <a href="/admin_php/actions/alumnos/deletealumno.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro de eliminar este alumno?');">
                                    Eliminar
                                </a>
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
                                <a class="page-link" href="indexalumno.php?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="editaralumno.php" method="POST" class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Alumno</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">

                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>DNI</label>
                    <input type="text" name="dni" id="edit-dni" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Celular</label>
                    <input type="text" name="celular" id="edit-celular" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL AGREGAR -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="addalumno.php" method="POST" class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Agregar Alumno</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>DNI</label>
                    <input type="text" name="dni" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Celular</label>
                    <input type="text" name="celular" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Registrar alumno</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    // Cargar modal editar
    $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);

        $('#edit-id').val(button.data('id'));
        $('#edit-nombre').val(button.data('nombre'));
        $('#edit-dni').val(button.data('dni'));
        $('#edit-email').val(button.data('email'));
        $('#edit-celular').val(button.data('celular'));
    });

    // Buscador
    document.getElementById('busqueda').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('table tbody tr');

        filas.forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });
</script>