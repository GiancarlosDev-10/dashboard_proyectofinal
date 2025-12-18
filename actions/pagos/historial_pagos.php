<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Solo admin puede acceder
$rol_usuario = $_SESSION['admin_rol'] ?? 'alumno';
if ($rol_usuario !== 'admin') {
    header("Location: ../../index2.php");
    exit;
}

include(__DIR__ . '/../../includes/header.php');
?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <div class="container mt-4">

                <!-- ALERTA DE ÉXITO -->
                <?php if (isset($_GET['success']) && $_GET['success'] === 'ok'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Estado actualizado correctamente.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => location.href = "historial_pagos.php", 1500);
                    </script>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0">
                        <i class="fas fa-receipt"></i> Historial de Pagos
                    </h3>
                </div>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar por ticket, alumno o estado...">
                </div>

                <?php
                include("../../db.php");

                $registros = 10;
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                if ($pagina < 1) $pagina = 1;
                $inicio = ($pagina - 1) * $registros;

                $total = $conn->query("SELECT COUNT(*) FROM pagos")->fetch_row()[0];
                $totalPaginas = ceil($total / $registros);

                $sql = "
                    SELECT p.id, p.numero_ticket, p.total, p.fecha_emision, p.estado,
                           a.nombre AS alumno_nombre, a.id AS alumno_id
                    FROM pagos p
                    INNER JOIN alumno a ON p.alumno_id = a.id
                    ORDER BY p.fecha_emision DESC
                    LIMIT $inicio, $registros
                ";
                $result = $conn->query($sql);
                ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-list"></i> Todos los Tickets Generados
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nº Ticket</th>
                                        <th>Alumno</th>
                                        <th>Total</th>
                                        <th>Fecha Emisión</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary"><?= htmlspecialchars($row['numero_ticket'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($row['alumno_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><strong>S/. <?= number_format((float)$row['total'], 2) ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_emision'])) ?></td>
                                            <td>
                                                <?php if ($row['estado'] === 'Pendiente'): ?>
                                                    <span class="badge badge-warning badge-pill">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-success badge-pill">
                                                        <i class="fas fa-check-circle"></i> Pagado
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <!-- RE-IMPRIMIR TICKET -->
                                                <button class="btn btn-info btn-sm"
                                                    onclick="reimprimirTicket(<?= (int)$row['alumno_id'] ?>, '<?= htmlspecialchars($row['numero_ticket'], ENT_QUOTES, 'UTF-8') ?>')">
                                                    <i class="fas fa-print"></i> Re-imprimir
                                                </button>

                                                <!-- MARCAR COMO PAGADO (solo si está Pendiente) -->
                                                <?php if ($row['estado'] === 'Pendiente'): ?>
                                                    <button class="btn btn-success btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#marcarPagadoModal"
                                                        data-id="<?= (int)$row['id'] ?>"
                                                        data-ticket="<?= htmlspecialchars($row['numero_ticket'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <i class="fas fa-check"></i> Marcar Pagado
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINACIÓN -->
                        <?php if ($totalPaginas > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                            <a class="page-link" href="historial_pagos.php?pagina=<?= $i ?>"><?= $i ?></a>
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
<!-- MODAL MARCAR COMO PAGADO -->
<!-- ========================================================= -->
<div class="modal fade" id="marcarPagadoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> Confirmar Pago
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center py-4">
                <h5 class="mb-3">¿Confirmar que este ticket fue pagado?</h5>
                <div class="alert alert-info">
                    <strong>Ticket: <span id="modal-ticket"></span></strong>
                </div>
                <input type="hidden" id="modal-pago-id">
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarPago">
                    Sí, marcar como Pagado
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- SCRIPTS -->
<!-- ========================================================= -->

<script>
    // Buscador en tiempo real
    document.getElementById('busqueda').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        document.querySelectorAll('table tbody tr').forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });

    // Re-imprimir ticket
    function reimprimirTicket(alumnoId, numeroTicket) {
        window.open('/admin_php/reportespdf/ticketpago.php?alumno_id=' + alumnoId + '&ticket=' + numeroTicket, '_blank');
    }

    // Cargar datos en modal
    $('#marcarPagadoModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        $('#modal-pago-id').val(button.data('id'));
        $('#modal-ticket').text(button.data('ticket'));
    });

    // Confirmar pago
    $('#btnConfirmarPago').click(function() {
        const id = $('#modal-pago-id').val();
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: 'marcar_pagado.php',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    $('#marcarPagadoModal').modal('hide');
                    location.href = 'historial_pagos.php?success=ok';
                } else {
                    alert(resp.message || 'No se pudo actualizar el estado.');
                    $('#btnConfirmarPago').prop('disabled', false).html('Sí, marcar como Pagado');
                }
            },
            error: function() {
                alert('Error de conexión. Intenta nuevamente.');
                $('#btnConfirmarPago').prop('disabled', false).html('Sí, marcar como Pagado');
            }
        });
    });
</script>

<?php include(__DIR__ . '/../../includes/footer.php'); ?>