<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <h3 class="text-primary mb-3">Lista de Matrículas</h3>

                <?php
                include("../../db.php");

                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM matricula")->fetch_row()[0];
                $paginas = ceil($total / $registros);

                $sql = "
                SELECT 
                    m.*,
                    a.nombre AS alumno,
                    c.nombre AS curso
                FROM matricula m
                JOIN alumno a ON m.alumno_id = a.id
                JOIN curso c ON m.curso_id = c.id
                LIMIT $inicio, $registros
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
                            <th>Fecha</th>
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

                                    <a href="deletematriculas.php?id=<?= $row['id'] ?>"
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
                                    <a class="page-link" href="indexmatriculas.php?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div> <!-- Cerrar wrapper -->

<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>