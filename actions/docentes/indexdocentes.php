<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper"> <!-- 🔥 ABRIR WRAPPER -->

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <h3 class="text-primary mb-3">Lista de Docentes</h3>

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
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nombre="<?= $row['nombre'] ?>"
                                        data-especialidad="<?= $row['especialidad'] ?>"
                                        data-dni="<?= $row['dni'] ?>">
                                        Editar
                                    </button>

                                    <a href="deletedocente.php?id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que desea eliminar?');">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

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

</div> <!-- 🔥 CERRAR WRAPPER -->

<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $('#editModal').on('show.bs.modal', function(event) {
        let b = $(event.relatedTarget);
        $('#edit-id').val(b.data('id'));
        $('#edit-nombre').val(b.data('nombre'));
        $('#edit-especialidad').val(b.data('especialidad'));
        $('#edit-dni').val(b.data('dni'));
    });
</script>