<?php
// Logik befindet sich jetzt in SupportController@index
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<h1 class="main-title">Kundendienst Dashboard</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h3>Kundensuche</h3>
            <p>Geben Sie einen Namen, eine E-Mail-Adresse oder eine Kundennummer ein, um einen Kunden zu finden.</p>
            <form action="<?php echo $config['base_url']; ?>/support/kunden" method="get" class="search-filter-form" style="margin-top: 20px;">
                <div class="form-group search-group" style="position: relative; flex-basis: 100%;">
                    <label for="support-search" class="visually-hidden">Kundensuche</label>
                    <svg class="search-icon" xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" viewBox="0 0 24 24" fill="currentColor"><path d="M10 18a8 8 0 1 1 8-8a8.009 8.009 0 0 1-8 8zm0-14a6 6 0 1 0 6 6a6.007 6.007 0 0 0-6-6z"/><path d="M20.707 19.293l-4-4a1 1 0 0 0-1.414 1.414l4 4a1 1 0 0 0 1.414-1.414z"/></svg>
                    <input type="search" id="support-search" name="search" class="form-control" placeholder="Kunde suchen..." required>
                </div>
                <div class="form-group button-group">
                    <button type="submit" class="btn btn-primary">Suchen</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include dirname(__DIR__, 2) . '/templates/footer.php'; ?>