<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>

            <?php
            include("../../db.php");

            /* ==============================
               📌 CARDS DINÁMICAS
               ============================== */

            // MATRÍCULAS TOTALES
            $total_matriculas = $conn->query("SELECT COUNT(*) FROM matricula")->fetch_row()[0];

            // CURSOS CON MATRÍCULAS
            $cursos_con_matriculas = $conn->query("
                SELECT COUNT(DISTINCT curso_id)
                FROM matricula
            ")->fetch_row()[0];

            // MATRÍCULAS ACTIVAS
            $matriculas_activas = $conn->query("
                SELECT COUNT(*)
                FROM matricula
                WHERE estado = 'Matriculado'
            ")->fetch_row()[0];

            // ÚLTIMA MATRÍCULA (nombre del alumno)
            $ultimo_alumno = $conn->query("
                SELECT a.nombre
                FROM matricula m
                JOIN alumno a ON a.id = m.alumno_id
                ORDER BY m.id DESC
                LIMIT 1
            ")->fetch_row()[0];

            /* ==============================
               📊 GRÁFICO: MATRÍCULAS POR MES
               ============================== */

            $sql_mes = "
                SELECT MONTH(fecha_inscripcion) AS mes, COUNT(*) AS total
                FROM matricula
                GROUP BY MONTH(fecha_inscripcion)
                ORDER BY mes
            ";

            $res_mes = $conn->query($sql_mes);

            $meses = [];
            $montos = [];

            $nombre_meses = [
                "",
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ];

            while ($row = $res_mes->fetch_assoc()) {
                $meses[] = $nombre_meses[$row['mes']];
                $montos[] = intval($row['total']);
            }

            /* ==============================
               PIE CHART: MATRÍCULAS POR CURSO
               ============================== */

            $sql_cursos = "
                SELECT c.nombre AS curso, COUNT(*) AS total
                FROM matricula m
                JOIN curso c ON m.curso_id = c.id
                GROUP BY c.id
            ";

            $res_cursos = $conn->query($sql_cursos);

            $categorias = [];
            $ingresos = [];

            while ($row = $res_cursos->fetch_assoc()) {
                $categorias[] = $row['curso'];
                $ingresos[] = intval($row['total']);
            }
            ?>

            <div class="container-fluid">

                <!-- TÍTULO -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Reporte General de Matrículas</h1>
                </div>

                <!-- CARDS -->
                <div class="row">

                    <!-- Matrículas Totales -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Matrículas Totales
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_matriculas ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Cursos con Matrículas -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Cursos con Matrículas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_con_matriculas ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Matrículas Activas -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Matrículas Activas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $matriculas_activas ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Último Alumno Matriculado -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Último Alumno Matriculado
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $ultimo_alumno ? $ultimo_alumno : "—" ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- GRÁFICOS -->
                <div class="row">

                    <!-- Línea -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Matrículas por Mes</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Donut -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Matrículas por Curso</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="myPieChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- container -->

        </div>
    </div>

</div>

<!-- CHARTS JS -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* === GRÁFICO LÍNEA === */
    var ctxArea = document.getElementById('myAreaChart').getContext('2d');
    var myAreaChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: <?= json_encode($meses) ?>,
            datasets: [{
                label: 'Matrículas registradas',
                data: <?= json_encode($montos) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.05)',
                fill: true
            }]
        }
    });

    /* === GRÁFICO DONUT === */
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