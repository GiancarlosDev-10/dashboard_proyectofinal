<?php
include(__DIR__ . '/../db.php');


/* ==== CARDS DINÁMICAS ==== */

// Alumnos totales
$alumnos_total = $conn->query("SELECT COUNT(*) FROM alumno")->fetch_row()[0];

// Cursos totales
$cursos_total = $conn->query("SELECT COUNT(*) FROM curso")->fetch_row()[0];

// Docentes totales
$docentes_total = $conn->query("SELECT COUNT(*) FROM docente")->fetch_row()[0];

// Ganancias totales (Opción B: precio del curso cuando el alumno se matricula)
$ganancias_total = $conn->query("
    SELECT SUM(c.precio) 
    FROM matricula m 
    JOIN curso c ON m.curso_id = c.id 
    WHERE m.estado = 'Matriculado'
")->fetch_row()[0];

if (!$ganancias_total) {
    $ganancias_total = 0;
}

/* ==== GRÁFICO DE ÁREA: GANANCIAS POR MES ==== */
$area_result = $conn->query("
    SELECT 
        MONTH(m.fecha_inscripcion) AS mes,
        SUM(c.precio) AS total
    FROM matricula m
    JOIN curso c ON m.curso_id = c.id
    WHERE m.estado = 'Matriculado'
    GROUP BY MONTH(m.fecha_inscripcion)
    ORDER BY mes
");

$meses = [];
$montos = [];
$nombre_meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

while ($row = $area_result->fetch_assoc()) {
    $meses[]  = $nombre_meses[$row['mes']];
    $montos[] = $row['total'];
}

/* ==== PIE CHART: INGRESOS POR CATEGORÍA ==== */
$pie_result = $conn->query("
    SELECT cat.nombre AS categoria, SUM(c.precio) AS total
    FROM matricula m
    JOIN curso c ON m.curso_id = c.id
    JOIN categoria cat ON c.categoria_id = cat.id
    WHERE m.estado = 'Matriculado'
    GROUP BY cat.id
");

$categorias = [];
$ingresos   = [];

while ($row = $pie_result->fetch_assoc()) {
    $categorias[] = $row['categoria'];
    $ingresos[]   = $row['total'];
}
?>

<!-- Título y botón -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Panel</h1>
</div>

<!-- Cards de estadísticas -->
<div class="row">

    <!-- Alumnos Totales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Alumnos Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $alumnos_total ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cursos Totales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cursos Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $cursos_total ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Docentes Totales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Docentes Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $docentes_total ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ganancias Totales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ganancias Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            S/. <?= number_format($ganancias_total, 2) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Gráficos -->
<div class="row">

    <!-- Área -->
    <div class="col-xl-7 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Resumen de Ganancias</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie -->
    <div class="col-xl-5 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ingresos por Categoría</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>