<?php
session_start();
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

            // TOTAL ALUMNOS
            $total_alumnos = $conn->query("SELECT COUNT(*) FROM alumno")->fetch_row()[0];

            // CURSOS CON ALUMNOS
            $cursos_totales = $conn->query("
                SELECT COUNT(DISTINCT curso_id)
                FROM matricula
            ")->fetch_row()[0];

            // MATRÍCULAS ACTIVAS (Matriculado)
            $matriculas_activas = $conn->query("
                SELECT COUNT(*) 
                FROM matricula 
                WHERE estado = 'Matriculado'
            ")->fetch_row()[0];

            // ÚLTIMO ALUMNO REGISTRADO
            $ultimo_alumno = $conn->query("
                SELECT nombre 
                FROM alumno 
                ORDER BY id DESC 
                LIMIT 1
            ")->fetch_row()[0];

            /* ==============================
               📊 GRÁFICO: ALUMNOS POR MES
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
               PIE CHART: ALUMNOS POR CURSO
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
                    <h1 class="h3 mb-0 text-gray-800">Reporte General de Alumnos</h1>
                </div>

                <!-- CARDS -->
                <div class="row">

                    <!-- Total Alumnos -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Alumnos Totales</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_alumnos ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Cursos con alumnos -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cursos con Matrículas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_totales ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Matrículas activas -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Matrículas Activas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $matriculas_activas ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Último alumno -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Último Alumno Registrado</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $ultimo_alumno ?></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- GRÁFICOS -->
                <div class="row">

                    <!-- Área -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Alumnos por Mes</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Pie -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Alumnos por Curso</h6>
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
<!-- CHART.JS (🔥 obligatorio para que dibuje los gráficos) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* === AREA CHART === */
    var ctxArea = document.getElementById('myAreaChart').getContext('2d');

    var myAreaChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: <?= json_encode($meses) ?>,
            datasets: [{
                label: 'Alumnos inscritos',
                data: <?= json_encode($montos) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.05)',
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