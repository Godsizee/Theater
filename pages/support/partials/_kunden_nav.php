<div class="profile-content-header">
    <h1>Kundenprofil: <?php echo htmlspecialchars($customer['Vorname'] . ' ' . $customer['Nachname']); ?></h1>
    <p class="meta-info">
        Kundennummer: <?php echo htmlspecialchars($customer['KundeId']); ?> | 
        Benutzername: <?php echo htmlspecialchars($customer['Username'] ?? 'N/A'); ?>
    </p>
</div>

<nav class="sub-nav">
    <a href="<?php echo $config['base_url']; ?>/support/kundendetails/<?php echo $customer['UserId']; ?>" class="sub-nav-item <?php echo (str_contains($_SERVER['REQUEST_URI'], 'kundendetails')) ? 'active' : ''; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span>Übersicht</span>
    </a>
    <a href="<?php echo $config['base_url']; ?>/support/kundendaten_bearbeiten/<?php echo $customer['UserId']; ?>" class="sub-nav-item <?php echo (str_contains($_SERVER['REQUEST_URI'], 'kundendaten_bearbeiten')) ? 'active' : ''; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
        <span>Profildaten bearbeiten</span>
    </a>
    <a href="<?php echo $config['base_url']; ?>/support/kundenbestellungen/<?php echo $customer['UserId']; ?>" class="sub-nav-item <?php echo (str_contains($_SERVER['REQUEST_URI'], 'kundenbestellungen')) ? 'active' : ''; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        <span>Bestellungen</span>
    </a>
</nav>