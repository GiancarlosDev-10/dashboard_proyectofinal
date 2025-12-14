<!-- sidebar placeholder -->
<?php
// includes/sidebar.php
// Espera $activePage definido por la página que incluye (e.g. 'dashboard', 'usuarios')
if (!isset($activePage)) $activePage = '';

// Verificar que exista la sesión y el rol
$rol_usuario = $_SESSION['admin_rol'] ?? 'alumno';
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/admin_php/index2.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">CERSA <sup></sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Documentación (TODOS) -->
    <li class="nav-item active">
        <a class="nav-link" href="/admin_php/reportespdf/documentacion.php">
            <i class="fas fa-file"></i>
            <span>Documentación</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        INICIO
    </div>

    <!-- Nav Item - Alumnos (SOLO ADMIN) -->
    <?php if ($rol_usuario === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAlumnos"
                aria-expanded="false" aria-controls="collapseAlumnos">
                <i class="fas fa-user-graduate"></i>
                <span>Alumnos</span>
            </a>
            <div id="collapseAlumnos" class="collapse" aria-labelledby="headingAlumnos" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Nueva Acción</h6>
                    <a class="collapse-item" href="/admin_php/actions/alumnos/indexalumno.php">Ver Alumnos</a>
                    <a class="collapse-item" href="/admin_php/actions/alumnos/reportealumno.php">Reporte General</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <!-- Nav Item - Cursos (TODOS) -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDocente"
            aria-expanded="true" aria-controls="collapseDocente">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Cursos</span>
        </a>
        <div id="collapseDocente" class="collapse" aria-labelledby="headingDocente"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Nueva Acción</h6>
                <a class="collapse-item" href="/admin_php/actions/cursos/indexcursos.php">Ver Cursos</a>
                <a class="collapse-item" href="/admin_php/actions/cursos/reportecursos.php">Reporte General</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Docentes (TODOS) -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCursos"
            aria-expanded="true" aria-controls="collapseCursos">
            <i class="fas fa-book"></i>
            <span>Docentes</span>
        </a>
        <div id="collapseCursos" class="collapse" aria-labelledby="headingCursos"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Nueva Acción</h6>
                <a class="collapse-item" href="/admin_php/actions/docentes/indexdocentes.php">Ver Docentes</a>
                <a class="collapse-item" href="/admin_php/actions/docentes/reportedocentes.php">Reporte General</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Matrículas (SOLO ADMIN) -->
    <?php if ($rol_usuario === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMatriculas"
                aria-expanded="true" aria-controls="collapseMatriculas">
                <i class="fas fa-address-card"></i>
                <span>Matrículas</span>
            </a>
            <div id="collapseMatriculas" class="collapse" aria-labelledby="headingMatriculas"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Nueva Acción</h6>
                    <a class="collapse-item" href="/admin_php/actions/matriculas/indexmatriculas.php">Ver Matrículas</a>
                    <a class="collapse-item" href="/admin_php/actions/matriculas/reportematriculas.php">Reporte General</a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Resumen de reportes
    </div>

    <!-- Nav Item - Reportes PDF (SOLO ADMIN) -->
    <?php if ($rol_usuario === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReportes"
                aria-expanded="true" aria-controls="collapseReportes">
                <i class="fas fa-fw fa-folder"></i>
                <span>Reportes</span>
            </a>
            <div id="collapseReportes" class="collapse" aria-labelledby="headingReportes" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Elige una opción</h6>
                    <a class="collapse-item" href="/admin_php/reportespdf/reportealumnospdf.php">Reporte de Alumno</a>
                    <a class="collapse-item" href="/admin_php/reportespdf/reportedocentespdf.php">Reporte de Docente</a>
                    <a class="collapse-item" href="/admin_php/reportespdf/reportecursospdf.php">Reporte de Cursos</a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Verificar Estado (SOLO ADMIN) -->
        <li class="nav-item">
            <a class="nav-link" href="charts.html">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Verificar Estado</span></a>
        </li>

        <!-- Nav Item - Almacenar Recibos (SOLO ADMIN) -->
        <li class="nav-item">
            <a class="nav-link" href="tables.html">
                <i class="fas fa-fw fa-table"></i>
                <span>Almacenar Recibos</span></a>
        </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->