<?php
$pageTitle = 'Pagina En Blanco';
$activePage = 'dashboard';
include 'includes/header.php';
?>
<?php include 'includes/sidebar.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <?php include 'includes/topbar.php'; ?>
        <!-- End of Topbar -->

        <!-- Inicio de contenido de la Paguina Principal-->
        <div class="container-fluid">

            <!-- Encabezado de página -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Página en blanco</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-download fa-sm text-white-50"></i> Generar Reporte</a>
            </div>
            <div class="row">
                <div class="col">
                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                        <label for="file" class="form-label">Seleccione un archivo:</label>
                        <input class="form-control mb-3" name="uploaded_file" type="file" id="file">
                        <button type="submit" class="btn btn-danger w-100">Subir Archivo</button>
                    </form>
                </div>
            </div>

        </div>
        <!-- Fin del contenido prinicpal/.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; Tu Sistema Web 2025</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

<?php include 'includes/footer.php'; ?>