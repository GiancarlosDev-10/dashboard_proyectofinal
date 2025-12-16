<?php
session_start();
$pageTitle = 'Perfil';
include __DIR__ . '/includes/header.php';
?>
<div id="wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Perfil</h1>

                <?php
                include __DIR__ . '/db.php';

                $stmt = $conn->query("SELECT * FROM admin WHERE rol IN ('admin','alumno') ORDER BY rol DESC, id ASC");
                $users = [];
                while ($row = $stmt->fetch_assoc()) {
                    $users[] = $row;
                }
                ?>

                <div class="row">
                    <?php foreach ($users as $user): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $foto = !empty($user['foto']) ? '/admin_php/img/fotos/' . $user['foto'] : '/admin_php/img/undraw_profile.svg';
                                        ?>
                                        <img id="foto-user-<?= intval($user['id']) ?>" src="<?= $foto ?>" class="rounded-circle mr-4" alt="Foto" style="width:120px;height:120px;object-fit:cover;">
                                        <div>
                                            <h5 class="mb-0"><?= htmlspecialchars($user['nombre']) ?></h5>
                                            <div class="text-muted"><?= htmlspecialchars($user['email']) ?></div>
                                            <small class="text-secondary">Rol: <?= htmlspecialchars($user['rol']) ?></small>
                                        </div>
                                    </div>

                                    <hr>

                                    <p id="desc-user-<?= intval($user['id']) ?>"><?= nl2br(htmlspecialchars($user['descripcion'] ?? '')) ?></p>

                                    <form class="perfil-form" action="/admin_php/upload.php" method="post" enctype="multipart/form-data" data-user-id="<?= intval($user['id']) ?>">
                                        <input type="hidden" name="id" value="<?= intval($user['id']) ?>">
                                        <div class="form-group">
                                            <label for="descripcion_<?= $user['id'] ?>">Descripción</label>
                                            <textarea name="descripcion" id="descripcion_<?= $user['id'] ?>" class="form-control" rows="3"><?= htmlspecialchars($user['descripcion'] ?? '') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Foto (opcional)</label>
                                            <input type="file" name="uploaded_file" class="form-control-file">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modal para resultado de subida -->
                <div class="modal fade" id="uploadResultModal" tabindex="-1" role="dialog" aria-labelledby="uploadResultModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadResultModalLabel">Resultado</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="uploadModalMessage">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>

</div>