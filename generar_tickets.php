<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

include(__DIR__ . '/db.php');

// ============================================
// AJAX: OBTENER CURSOS DEL ALUMNO
// ============================================
if (isset($_GET['obtener_cursos_ajax'])) {
    header('Content-Type: application/json');

    $alumnoId = intval($_GET['alumno_id'] ?? 0);
    $response = ['success' => false, 'cursos' => [], 'alumno' => [], 'message' => ''];

    if ($alumnoId > 0) {
        try {
            // Obtener datos del alumno
            $sqlAlumno = "SELECT nombre, dni, email FROM alumno WHERE id = ?";
            $stmtAlumno = $conn->prepare($sqlAlumno);
            $stmtAlumno->bind_param("i", $alumnoId);
            $stmtAlumno->execute();
            $resultAlumno = $stmtAlumno->get_result();

            if ($resultAlumno->num_rows > 0) {
                $response['alumno'] = $resultAlumno->fetch_assoc();
            }
            $stmtAlumno->close();

            // Obtener cursos matriculados
            $sqlCursos = "SELECT c.nombre, c.precio, m.nombre as modalidad
                        FROM matricula mat
                        INNER JOIN curso c ON mat.curso_id = c.id
                        INNER JOIN modalidad m ON c.modalidad_id = m.id
                        WHERE mat.alumno_id = ?";

            $stmtCursos = $conn->prepare($sqlCursos);
            $stmtCursos->bind_param("i", $alumnoId);
            $stmtCursos->execute();
            $resultCursos = $stmtCursos->get_result();

            while ($row = $resultCursos->fetch_assoc()) {
                $response['cursos'][] = [
                    'nombre' => $row['nombre'],
                    'modalidad' => $row['modalidad'],
                    'precio' => $row['precio']
                ];
            }

            $response['success'] = true;
            $stmtCursos->close();
        } catch (Exception $e) {
            error_log("Error al obtener cursos: " . $e->getMessage());
            $response['message'] = 'Error al cargar los cursos.';
        }
    } else {
        $response['message'] = 'ID de alumno inválido.';
    }

    $conn->close();
    echo json_encode($response);
    exit;
}

include(__DIR__ . '/includes/header.php');
?>

<div id="wrapper">

    <?php include(__DIR__ . '/includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/includes/topbar.php'); ?>

            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">
                        <i class="fas fa-ticket-alt"></i> Generar Tickets de Pago
                    </h3>
                </div>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="busqueda" class="form-control form-control-lg"
                        placeholder="Buscar alumno por nombre, DNI o email...">
                </div>

                <!-- Tabla de Alumnos -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-users"></i> Selecciona un Alumno
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Email</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
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
                                            <td><?= (int)$row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-ver-cursos"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-dni="<?= htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <i class="fas fa-eye"></i> Ver Cursos
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <?php if ($totalPaginas > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                            <a class="page-link" href="generar_tickets.php?pagina=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

            </div> <!-- container -->

        </div> <!-- content -->
    </div> <!-- content-wrapper -->

</div> <!-- wrapper -->

<!-- ========================================================= -->
<!-- MODAL VER CURSOS Y GENERAR TICKET                        -->
<!-- ========================================================= -->
<div class="modal fade" id="cursosModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-graduation-cap"></i> Cursos Matriculados
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Datos del Alumno -->
                <div class="alert alert-info">
                    <h6 class="mb-2"><strong>Alumno:</strong> <span id="modal-alumno-nombre"></span></h6>
                    <small><strong>DNI:</strong> <span id="modal-alumno-dni"></span></small> |
                    <small><strong>Email:</strong> <span id="modal-alumno-email"></span></small>
                </div>

                <!-- Loading -->
                <div id="loading-cursos" class="text-center py-4" style="display: none;">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Cargando cursos...</p>
                </div>

                <!-- Sin cursos -->
                <div id="sin-cursos" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> Este alumno no tiene cursos matriculados.
                </div>

                <!-- Tabla de Cursos -->
                <div id="tabla-cursos-container" style="display: none;">
                    <h6 class="font-weight-bold mb-3">Detalle de Cursos:</h6>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Curso</th>
                                <th>Modalidad</th>
                                <th class="text-right">Precio</th>
                            </tr>
                        </thead>
                        <tbody id="cursos-body">
                            <!-- Se llenará con AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="table-success font-weight-bold">
                                <td colspan="2" class="text-right">TOTAL A PAGAR:</td>
                                <td class="text-right">S/. <span id="total-footer">0.00</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" id="modal-alumno-id">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success" id="btn-generar-ticket" style="display: none;">
                    <i class="fas fa-file-pdf"></i> Generar Ticket de Pago
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- SCRIPTS NECESARIOS                                       -->
<!-- ========================================================= -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {

        // ============================================
        // BUSCADOR EN TABLA (igual que indexalumno)
        // ============================================
        document.getElementById('busqueda').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(fila => {
                fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            });
        });

        // ============================================
        // BOTÓN VER CURSOS
        // ============================================
        $('.btn-ver-cursos').click(function() {
            const alumnoId = $(this).data('id');
            const nombre = $(this).data('nombre');
            const dni = $(this).data('dni');
            const email = $(this).data('email');

            // Guardar datos en el modal
            $('#modal-alumno-id').val(alumnoId);
            $('#modal-alumno-nombre').text(nombre);
            $('#modal-alumno-dni').text(dni);
            $('#modal-alumno-email').text(email);

            // Mostrar loading
            $('#loading-cursos').show();
            $('#sin-cursos').hide();
            $('#tabla-cursos-container').hide();
            $('#btn-generar-ticket').hide();

            // Abrir modal
            $('#cursosModal').modal('show');

            // Cargar cursos con AJAX
            $.ajax({
                url: 'generar_tickets.php',
                type: 'GET',
                data: {
                    obtener_cursos_ajax: 1,
                    alumno_id: alumnoId
                },
                dataType: 'json',
                success: function(response) {
                    $('#loading-cursos').hide();

                    if (response.success) {
                        if (response.cursos.length === 0) {
                            $('#sin-cursos').show();
                        } else {
                            let html = '';
                            let total = 0;

                            response.cursos.forEach(function(curso) {
                                html += `
                                <tr>
                                    <td>${curso.nombre}</td>
                                    <td>${curso.modalidad}</td>
                                    <td class="text-right">S/. ${parseFloat(curso.precio).toFixed(2)}</td>
                                </tr>
                            `;
                                total += parseFloat(curso.precio);
                            });

                            $('#cursos-body').html(html);
                            $('#total-footer').text(total.toFixed(2));
                            $('#tabla-cursos-container').show();
                            $('#btn-generar-ticket').show();
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    $('#loading-cursos').hide();
                    alert('Error al cargar los cursos del alumno.');
                }
            });
        });

        // ============================================
        // GENERAR TICKET PDF
        // ============================================
        $('#btn-generar-ticket').click(function() {
            const alumnoId = $('#modal-alumno-id').val();
            window.open('reportespdf/ticketpago.php?alumno_id=' + alumnoId, '_blank');
        });

    });
</script>

<?php include(__DIR__ . '/includes/footer.php'); ?>