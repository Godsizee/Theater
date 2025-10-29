<?php
// Dies ist die "View" für den UserController::index()
// Alle Variablen ($page_title, $users, $message etc.) werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<div class="page-wrapper admin-dashboard-wrapper">
    <h1 class="main-title">Benutzerverwaltung</h1>
    <div class="dashboard-grid">
        <?php include __DIR__ . '/partials/_sidebar_nav.php'; ?>
        <main class="dashboard-content">
            <div class="dashboard-section">
                <h3>Alle Benutzer</h3>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <?php if (count($users) > 0): ?>
                    <div class="table-responsive">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th><a href="?sort=UserId&order=<?php echo $next_order; ?>">ID</a></th>
                                    <th><a href="?sort=Username&order=<?php echo $next_order; ?>">Benutzername</a></th>
                                    <th><a href="?sort=EMail&order=<?php echo $next_order; ?>">E-Mail</a></th>
                                    <th><a href="?sort=Rolle&order=<?php echo $next_order; ?>">Rolle</a></th>
                                    <th><a href="?sort=Birthday&order=<?php echo $next_order; ?>">Geburtstag</a></th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr id="user-row-<?php echo htmlspecialchars($user['UserId']); ?>">
                                        <td><?php echo htmlspecialchars($user['UserId']); ?></td>
                                        <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['EMail']); ?></td>
                                        <td><?php echo htmlspecialchars($user['Rolle']); ?></td>
                                        <td><?php echo !empty($user['Birthday']) ? htmlspecialchars(date('d.m.Y', strtotime($user['Birthday']))) : 'N/A'; ?></td>
                                        <td class="actions">
                                            <a href="<?php echo $config['base_url']; ?>/admin/edit_user/<?php echo htmlspecialchars($user['UserId']); ?>" class="action-btn edit small-btn">Bearbeiten</a>
                                            <a href="#" class="action-btn delete small-btn" data-id="<?php echo htmlspecialchars($user['UserId']); ?>" data-type="user">Löschen</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="message info">Keine Benutzer in der Datenbank gefunden.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php
include dirname(__DIR__, 2) . '/templates/footer.php';
?>