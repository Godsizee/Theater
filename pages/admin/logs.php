<?php
require_once dirname(__DIR__, 2) .'/init.php';
\App\Core\Security::requireAdmin();

$page_title = 'Aktivitätsprotokoll';
$body_class = 'admin-dashboard-body';

// Repositories instanziieren
$auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);
$userRepository = new \App\Repositories\UserRepository($pdo);

// Daten für die Filter-Dropdowns holen
$staff_users = $userRepository->getSupportAndAdminUsers();
$action_types = $auditLogRepository->getDistinctActionTypes();

// Filter-Parameter aus der URL verarbeiten
$filters = [
    'user' => filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT),
    'action' => trim($_GET['action'] ?? '')
];
// Entfernt leere Filterwerte aus dem Array
$active_filters = array_filter($filters);

// Gefilterte Logs über das Repository holen
$logs = $auditLogRepository->getFilteredLogs($active_filters);

// KORREKTUR: Erstelle einen sauberen Query-String nur mit den aktiven Filtern für den Export-Link
$filter_query = http_build_query($active_filters);


include dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Aktivitätsprotokoll</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h3>Protokoll filtern</h3>
            <form action="<?php echo $config['base_url']; ?>/admin/logs" method="get" class="search-filter-form" style="margin-bottom: 30px;">
                <div class="form-group">
                    <select name="user" class="form-control">
                        <option value="">Alle Mitarbeiter</option>
                        <?php foreach ($staff_users as $staff): ?>
                            <option value="<?php echo $staff['UserId']; ?>" <?php if(($filters['user'] ?? '') == $staff['UserId']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($staff['Username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="action" class="form-control">
                        <option value="">Alle Aktionen</option>
                        <?php foreach ($action_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php if(($filters['action'] ?? '') == $type) echo 'selected'; ?>>
                                <?php echo htmlspecialchars(str_replace('_', ' ', $type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group button-group">
                    <button type="submit" class="btn btn-primary">Filtern</button>
                    <a href="<?php echo $config['base_url']; ?>/admin/logs" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <a href="<?php echo $config['base_url']; ?>/admin/logs/export?<?php echo $filter_query; ?>" class="btn btn-success" style="width: auto; float: right; margin-bottom: 20px;">Als TXT exportieren</a>
        </div>
        <div class="dashboard-section">
            <h3>Protokolleinträge</h3>
            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Zeitstempel</th>
                            <th>Mitarbeiter</th>
                            <th>Aktion</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logs->rowCount() === 0): ?>
                            <tr><td colspan="4" style="text-align: center;">Keine Protokolleinträge für die gewählten Filter gefunden.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('d.m.Y H:i:s', strtotime($log['Timestamp']))); ?></td>
                                    <td><?php echo htmlspecialchars($log['Username']); ?></td>
                                    <td><?php echo htmlspecialchars(str_replace('_', ' ', $log['Aktionstyp'])); ?></td>
                                    <td>
                                        <?php
                                        $details = json_decode($log['Details'], true);
                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            switch($log['Aktionstyp']) {
                                                case 'FILM_ERSTELLT':
                                                    echo "Der Film '<strong>" . htmlspecialchars($details['Moviename']) . "</strong>' (ID: " . htmlspecialchars($details['MovieId']) . ") wurde hinzugefügt.";
                                                    break;
                                                case 'FILM_GELOESCHT':
                                                    echo "Der Film '<strong>" . htmlspecialchars($details['Moviename']) . "</strong>' (ID: " . htmlspecialchars($details['MovieId']) . ") wurde gelöscht.";
                                                    break;
                                                case 'SERIE_ERSTELLT':
                                                    echo "Die Serie '<strong>" . htmlspecialchars($details['SeriesTitle']) . "</strong>' (ID: " . htmlspecialchars($details['SeriesId']) . ") wurde hinzugefügt.";
                                                    break;
                                                case 'SERIE_GELOESCHT':
                                                    echo "Die Serie '<strong>" . htmlspecialchars($details['SeriesTitle']) . "</strong>' (ID: " . htmlspecialchars($details['SeriesId']) . ") wurde gelöscht.";
                                                    break;
                                                case 'BESTELLUNG_STORNIERT':
                                                    echo "Die Bestellung mit der Ticket-ID #<strong>" . htmlspecialchars($details['TicketId']) . "</strong> wurde storniert.";
                                                    break;
                                                case 'PASSWORT_GEAENDERT':
                                                    echo "Das Passwort für Benutzer #<strong>" . htmlspecialchars($details['BetroffenerUserId']) . "</strong> wurde geändert.";
                                                    break;
                                                case 'KUNDENDATEN_GEAENDERT':
                                                    echo "Feld '<strong>" . htmlspecialchars($details['Feld']) . "</strong>' für Benutzer #<strong>" . htmlspecialchars($details['BetroffenerUserId']) . "</strong> geändert.<br>";
                                                    echo "<small style='color: #666;'>Alter Wert: '" . htmlspecialchars($details['AlterWert'] ?? 'N/A') . "'</small><br>";
                                                    echo "<small style='color: #666;'>Neuer Wert: '" . htmlspecialchars($details['NeuerWert'] ?? 'N/A') . "'</small>";
                                                    break;
                                                default:
                                                    echo '<pre style="white-space: pre-wrap; word-break: break-all; margin: 0; font-size: 0.8rem;">' . htmlspecialchars(json_encode($details, JSON_PRETTY_PRINT)) . '</pre>';
                                            }
                                        } else {
                                            echo htmlspecialchars($log['Details']);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php
include dirname(__DIR__, 2) .'/templates/footer.php';
?>