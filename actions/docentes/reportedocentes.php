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

            // 1️⃣ TOTAL DOCENTES
            $total_docentes = $conn->query("SELECT COUNT(*) FROM docente")->fetch_row()[0];

            // 2️⃣ CURSOS ASIGNADOS
            $cursos_asignados = $conn->query("
                SELECT COUNT(*) 
                FROM curso 
                WHERE docente_id IS NOT NULL
            ")->fetch_row()[0];

            // 3️⃣ DOCENTE MÁS ACTIVO (más cursos asignados)
            $sql_popular = $conn->query("
                SELECT d.nombre, COUNT(c.id) AS total
                FROM docente d
                LEFT JOIN curso c ON c.docente_id = d.id
                GROUP BY d.id
                ORDER BY total DESC
                LIMIT 1
            ");

            $docente_popular = $sql_popular->num_rows > 0
                ? $sql_popular->fetch_assoc()['nombre']
                : "Sin datos";

            // 4️⃣ DOCENTES SIN CURSOS
            $docentes_sin_cursos = $conn->query("
                SELECT COUNT(*) 
                FROM docente d
                LEFT JOIN curso c ON d.id = c.docente_id
                WHERE c.id IS NULL
            ")->fetch_row()[0];

            /* ==============================
               📊 GRÁFICO: Cursos por Docente (Barras)
               ============================== */

            $sql_barras = "
                SELECT d.nombre AS docente, COUNT(c.id) AS total
                FROM docente d
                LEFT JOIN curso c ON c.docente_id = d.id
                GROUP BY d.id
                ORDER BY docente ASC
            ";

            $res_barras = $conn->query($sql_barras);

            $docentes_barras = [];
            $cursos_barras = [];

            while ($row = $res_barras->fetch_assoc()) {
                $docentes_barras[] = $row['docente'];
                $cursos_barras[] = intval($row['total']);
            }

            /* ==============================
               🍩 GRÁFICO: Cursos por Especialidad (Donut)
               ============================== */

            $sql_pie = "
                SELECT d.especialidad, COUNT(c.id) AS total
                FROM docente d
                LEFT JOIN curso c ON d.id = c.docente_id
                GROUP BY d.especialidad
            ";

            $res_pie = $conn->query($sql_pie);

            $especialidades = [];
            $cantidad_especialidades = [];

            while ($row = $res_pie->fetch_assoc()) {
                $especialidades[] = $row['especialidad'];
                $cantidad_especialidades[] = intval($row['total']);
            }
            ?>

            <div class="container-fluid">

                <!-- TÍTULO -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Reporte General de Docentes</h1>
                </div>

                <!-- CARDS -->
                <div class="row">

                    <!-- Total Docentes -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Docentes Totales</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_docentes ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Cursos Asignados -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cursos Asignados</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_asignados ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Docente más activo -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Docente Más Activo</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $docente_popular ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Docentes sin cursos -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Docentes Sin Cursos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $docentes_sin_cursos ?></div>
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
                                <h6 class="m-0 font-weight-bold text-primary">Cursos por Docente</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartCursosDocente"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- DONUT -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Cursos por Especialidad</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartEspecialidades"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* === BARRAS: CURSOS POR DOCENTE === */
    new Chart(document.getElementById('chartCursosDocente').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($docentes_barras) ?>,
            datasets: [{
                label: 'Cursos asignados',
                data: <?= json_encode($cursos_barras) ?>,
                backgroundColor: 'rgba(78,115,223,0.6)',
                borderColor: '#4e73df',
                borderWidth: 2
            }]
        }
    });

    /* === DONUT: CURSOS POR ESPECIALIDAD === */
    new Chart(document.getElementById('chartEspecialidades').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($especialidades) ?>,
            datasets: [{
                data: <?= json_encode($cantidad_especialidades) ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
            }]
        }
    });
</script>