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

        <!-- FOOTER (NO MODIFICAR) -->
        <?php include 'includes/footer.php'; ?>

    </div>
    <!-- FIN CONTENT WRAPPER -->

</div>
<!-- FIN WRAPPER -->

<!-- ============================== -->
<!--   SCRIPT DE GRÁFICOS - ÚNICO   -->
<!-- ============================== -->

<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* =====================
           AREA CHART (LINE)
           ===================== */
        const areaCanvas = document.getElementById('myAreaChart');
        if (areaCanvas) {
            const ctxArea = areaCanvas.getContext('2d');

            new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: <?= json_encode($meses) ?>,
                    datasets: [{
                        label: 'Ganancias por Mes',
                        data: <?= json_encode($montos) ?>,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78,115,223,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return "S/. " + value;
                                }
                            }
                        },
                        x: {}
                    }
                }
            });
        }

        /* =====================
           PIE / DONUT CHART
           ===================== */
        const pieCanvas = document.getElementById('myPieChart');
        if (pieCanvas) {
            const ctxPie = pieCanvas.getContext('2d');

            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($categorias) ?>,
                    datasets: [{
                        data: <?= json_encode($ingresos) ?>,
                        backgroundColor: [
                            '#4e73df',
                            '#1cc88a',
                            '#36b9cc',
                            '#f6c23e',
                            '#ff6384',
                            '#8e44ad'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        }

    });
</script>