<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'includes/topbar.php'; ?>
        <?php include 'includes/body.php'; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>

<script>
    // Gráfico de Resumen de Ganancias
    var ctx = document.getElementById('myAreaChart').getContext('2d');
    var myAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Ganancias',
                data: [0, 10000, 8000, 20000, 15000, 30000],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true
            }]
        }
    });

    // Gráfico de Fuentes de Ingresos
    var ctxPie = document.getElementById('myPieChart').getContext('2d');
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Directo', 'Social', 'Referido'],
            datasets: [{
                data: [55, 30, 15],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            }]
        }
    });
</script>