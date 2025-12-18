<?php
session_start();
?>

<?php include(__DIR__ . '/../../includes/header.php'); ?>

<div id="wrapper">

    <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include(__DIR__ . '/../../includes/topbar.php'); ?>
            <?php include("../../db.php"); ?>

            <?php
            /* ================================
               📌 CARDS: MÉTRICAS DE CURSOS
               ================================ */

            // 1. CURSOS TOTALES
            $cursos_totales = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];

            // 2. CURSOS ACTIVOS
            $cursos_activos = $conn->query("
                SELECT COUNT(*) 
                FROM curso 
                WHERE estado = 'Activo'
            ")->fetch_row()[0];

            // 3. MODALIDADES DISPONIBLES
            $modalidades_total = $conn->query("
                SELECT COUNT(DISTINCT modalidad_id) 
                FROM curso
            ")->fetch_row()[0];

            // 4. CURSO MÁS POPULAR (más matrículas)
            $curso_popular = $conn->query("
                SELECT c.nombre, COUNT(*) AS total
                FROM matricula m
                JOIN curso c ON c.id = m.curso_id
                GROUP BY m.curso_id
                ORDER BY total DESC
                LIMIT 1
            ")->fetch_row()[0] ?? "Sin datos";

            /* ================================
               📊 GRÁFICO: MATRICULADOS POR CURSO
               ================================ */

            $query_barras = $conn->query("
                SELECT c.nombre AS curso, COUNT(*) AS total
                FROM matricula m
                JOIN curso c ON m.curso_id = c.id
                GROUP BY c.id
                ORDER BY total DESC
            ");

            $cursos_barras = [];
            $matriculados_barras = [];

            while ($row = $query_barras->fetch_assoc()) {
                $cursos_barras[] = $row['curso'];
                $matriculados_barras[] = intval($row['total']);
            }

            /* ================================
               🧁 GRÁFICO: CURSOS POR MODALIDAD
               ================================ */

            $query_modalidad = $conn->query("
                SELECT mo.nombre AS modalidad, COUNT(*) AS total
                FROM curso c
                JOIN modalidad mo ON mo.id = c.modalidad_id
                GROUP BY mo.id
            ");

            $modalidades_labels = [];
            $modalidades_cantidad = [];

            while ($row = $query_modalidad->fetch_assoc()) {
                $modalidades_labels[] = $row['modalidad'];
                $modalidades_cantidad[] = intval($row['total']);
            }
            ?>

            <div class="container-fluid">

                <!-- TÍTULO -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Reporte General de Cursos</h1>
                </div>

                <!-- CARDS -->
                <div class="row">

                    <!-- Cursos Totales -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cursos Totales</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_totales ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Activos -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cursos Activos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_activos ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modalidades -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Modalidades Disponibles</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $modalidades_total ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Curso más popular -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Curso Más Popular</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $curso_popular ?></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- GRÁFICOS -->
                <div class="row">

                    <!-- BARRAS -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Matriculados por Curso</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartBarrasCursos"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- DONUT -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Cursos por Modalidad</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartModalidades"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- container -->

        </div>
    </div>

</div>

<!-- LIBRERÍAS -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ===============================
     📊 GRÁFICOS JS
     =============================== -->
<script>
    /* === BARRAS: MATRICULADOS POR CURSO === */
    new Chart(document.getElementById('chartBarrasCursos').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($cursos_barras) ?>,
            datasets: [{
                label: 'Matriculados',
                data: <?= json_encode($matriculados_barras) ?>,
                backgroundColor: 'rgba(78,115,223,0.6)',
                borderColor: '#4e73df',
                borderWidth: 2
            }]
        }
    });

    /* === DONUT: CURSOS POR MODALIDAD === */
    new Chart(document.getElementById('chartModalidades').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($modalidades_labels) ?>,
            datasets: [{
                data: <?= json_encode($modalidades_cantidad) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
            }]
        }
    });
</script>