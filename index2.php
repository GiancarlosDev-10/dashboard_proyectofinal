<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include 'includes/header.php'; ?>

<!-- WRAPPER PRINCIPAL -->
<div id="wrapper">

    <!-- SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- CONTENIDO PRINCIPAL -->
        <div id="content">

            <!-- TOPBAR -->
            <?php include 'includes/topbar.php'; ?>

            <!-- CONTENEDOR DEL DASHBOARD (BODY) -->
            <div class="container-fluid">
                <?php include 'includes/body.php'; ?>
            </div>

        </div>
        <!-- FIN DEL CONTENIDO PRINCIPAL -->

        <!-- FOOTER -->
        <?php include 'includes/footer.php'; ?>

    </div>
    <!-- FIN CONTENT WRAPPER -->

</div>
<!-- FIN WRAPPER -->

<!-- CHARTS JS -->
<script>
    /* === AREA CHART === */
    var ctxArea = document.getElementById('myAreaChart').getContext('2d');

    var myAreaChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: <?= json_encode($meses) ?>,
            datasets: [{
                label: 'Ganancias por Mes',
                data: <?= json_encode($montos) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true
            }]
        }
    });

    /* === PIE CHART === */
    var ctxPie = document.getElementById('myPieChart').getContext('2d');

    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($categorias) ?>,
            datasets: [{
                data: <?= json_encode($ingresos) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
            }]
        }
    });
</script>