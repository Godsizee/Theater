<?php
// Dies ist die "View" für den MediaController::confirmDelete()
include dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title"><?php echo htmlspecialchars($page_title); ?></h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section text-center">
            <h2>Löschen bestätigen</h2>
            <p style="font-size: 1.1rem; line-height: 1.6;">
                Sind Sie sicher, dass Sie den Inhalt<br>
                <strong style="font-size: 1.3rem; color: var(--color-danger); display: inline-block; margin: 10px 0;">"<?php echo htmlspecialchars($media['title']); ?>"</strong><br>
                endgültig löschen möchten?
            </p>
            <form action="<?php echo $config['base_url']; ?>/admin/delete/<?php echo htmlspecialchars($media_type); ?>/<?php echo htmlspecialchars($id); ?>" method="post" style="margin-top: 20px;">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
                <div class="btn-group btn-group-equal">
                    <button type="submit" class="btn btn-danger">Ja, endgültig löschen</button>
                    <a href="<?php echo htmlspecialchars($redirect_url); ?>" class="btn btn-secondary">Nein, abbrechen</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include dirname(__DIR__, 2) .'/templates/footer.php';
?>