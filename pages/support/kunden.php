<?php
// Logik befindet sich jetzt in SupportController@listCustomers
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<h1 class="main-title">Kundenübersicht</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <?php if (!empty($search_term)): ?>
                <h3>Suchergebnisse für "<?php echo htmlspecialchars($search_term); ?>"</h3>
            <?php else: ?>
                <h3>Alle Kunden</h3>
            <?php endif; ?>

            <?php if (empty($customers)): ?>
                <div class="message info">
                    <?php if (!empty($search_term)): ?>
                        Es wurden keine Kunden gefunden, die Ihrer Suche entsprechen.
                    <?php else: ?>
                        Es sind keine Kunden in der Datenbank vorhanden.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Kunden-ID</th>
                                <th>Name</th>
                                <th>E-Mail</th>
                                <th>Benutzername</th>
                                <th>Ort</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['KundeId']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['Vorname'] . ' ' . $customer['Nachname']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['EMail']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['Username']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['Ort'] ?? 'N/A'); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $config['base_url']; ?>/support/kundendetails/<?php echo $customer['UserId']; ?>" class="action-btn edit small-btn">Profil ansehen</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include dirname(__DIR__, 2) . '/templates/footer.php'; ?>