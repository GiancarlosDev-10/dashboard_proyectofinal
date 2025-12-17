<?php
// includes/footer.php
?>

</div> <!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts JS (ajusta rutas) -->
<script src="/admin_php/vendor/jquery/jquery.min.js"></script>
<script src="/admin_php/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/admin_php/vendor/chart.js/Chart.min.js"></script>
<script src="/admin_php/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="/admin_php/js/sb-admin-2.min.js"></script>
<!-- Tus scripts personalizados -->
<script>
    // Diagnóstico rápido: muestra estado de jQuery/Bootstrap y existencia del toggle
    try {
        console.log('DEBUG: jQuery disponible:', typeof jQuery !== 'undefined' ? jQuery.fn.jquery : 'no');
        console.log('DEBUG: $.fn.modal:', (typeof jQuery !== 'undefined' && jQuery.fn && typeof jQuery.fn.modal === 'function'));
        console.log('DEBUG: sidebarToggleTop existe:', document.getElementById('sidebarToggleTop') ? true : false);
    } catch (e) {
        console.warn('DEBUG: error al ejecutar diagnóstico', e);
    }
    // Manejo AJAX de formularios de perfil: envía a upload.php y muestra modal con resultado
    $(function() {
        function handleFormSubmit(e) {
            e.preventDefault();
            var $form = $(this);
            var fd = new FormData(this);
            // Indicamos que es AJAX
            var action = $form.attr('action') || window.location.href;

            $.ajax({
                url: action,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: 'json'
            }).done(function(resp) {
                var ok = resp.success;
                var msg = resp.message || (ok ? 'La foto se cargó correctamente.' : 'Ocurrió un error.');
                $('#uploadModalMessage').text(msg);
                // Si hay foto, actualizar la imagen en la card
                if (resp.foto) {
                    var userId = $form.data('user-id');
                    var img = $('#foto-user-' + userId);
                    if (img.length) {
                        img.attr('src', '/admin_php/img/fotos/' + resp.foto + '?t=' + Date.now());
                    }
                }
                // Actualizar texto de descripción
                var userId = $form.data('user-id');
                var newDesc = $form.find('textarea[name="descripcion"]').val();
                $('#desc-user-' + userId).html(newDesc ? (newDesc.replace(/\n/g, '<br>')) : '');

                $('#uploadResultModal').modal('show');
            }).fail(function() {
                $('#uploadModalMessage').text('Error en la petición. Intenta de nuevo.');
                $('#uploadResultModal').modal('show');
            });
        }

        // Delegación para formularios con clase .perfil-form
        $(document).on('submit', '.perfil-form', handleFormSubmit);
    });
</script>
<script>
    // Diagnóstico adicional: comprobar elementos que usan data-toggle y clicks
    (function() {
        try {
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DIAG: elementos con data-toggle=collapse:', document.querySelectorAll('[data-toggle="collapse"]').length);
                console.log('DIAG: elementos con data-toggle=modal:', document.querySelectorAll('[data-toggle="modal"]').length);
                console.log('DIAG: $.fn.modal disponible:', (typeof jQuery !== 'undefined' && jQuery.fn && typeof jQuery.fn.modal === 'function'));

                var btn = document.getElementById('sidebarToggleTop');
                if (btn) {
                    btn.addEventListener('click', function(e) {
                        console.log('DIAG: sidebarToggleTop clicked');
                    });
                } else {
                    console.log('DIAG: sidebarToggleTop no existe en DOM');
                }
            });
        } catch (e) {
            console.warn('DIAG error', e);
        }
    })();
</script>
</body>

</html>