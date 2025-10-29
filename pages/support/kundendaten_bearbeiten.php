<?php
// Logik befindet sich jetzt in SupportController@editCustomerData und updateCustomerData
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Kundencenter</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <?php include 'partials/_kunden_nav.php'; ?>
        <div class="dashboard-section" style="margin-top: 20px;">
            <h3>Profildaten bearbeiten</h3>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="<?php echo $config['base_url']; ?>/support/kundendaten_bearbeiten/<?php echo htmlspecialchars($customer['UserId']); ?>/update" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <h4>Login-Daten</h4>
                        <p><label for="username">Benutzername</label><input type="text" id="username" name="username" value="<?php echo htmlspecialchars($customer['Username'] ?? ''); ?>" required></p>
                        <p><label for="email">E-Mail-Adresse</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['EMail'] ?? ''); ?>" required></p>
                        <p>
                            <label for="role">Rolle</label>
                            <?php if (in_array($_SESSION['user_role'], ['admin', 'co-admin'])): ?>
                                <select id="role" name="role" required>
                                    <?php foreach ($valid_roles as $role): ?>
                                        <option value="<?php echo htmlspecialchars($role); ?>" <?php if (($customer['Rolle'] ?? '') === $role) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $role))); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" value="<?php echo htmlspecialchars(ucfirst($customer['Rolle'] ?? '')); ?>" readonly class="form-static-value">
                                <input type="hidden" name="role" value="<?php echo htmlspecialchars($customer['Rolle'] ?? ''); ?>">
                            <?php endif; ?>
                        </p>
                        <p><label for="password">Neues Passwort setzen</label><input type="password" id="password" name="password" placeholder="(leer lassen für keine Änderung)" minlength="8"></p>
                    </div>
                    <div class="form-column">
                        <h4>Persönliche Daten</h4>
                        <p><label for="vorname">Vorname</label><input type="text" id="vorname" name="vorname" value="<?php echo htmlspecialchars($customer['Vorname'] ?? ''); ?>"></p>
                        <p><label for="nachname">Nachname</label><input type="text" id="nachname" name="nachname" value="<?php echo htmlspecialchars($customer['Nachname'] ?? ''); ?>"></p>
                        <p><label for="birthday">Geburtstag</label><input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($customer['Birthday'] ?? ''); ?>"></p>
                        <p><label for="telefon">Telefon</label><input type="tel" id="telefon" name="telefon" value="<?php echo htmlspecialchars($customer['Telefon'] ?? ''); ?>"></p>
                    </div>
                </div>
                <h4 style="margin-top: 30px; border-top: 1px solid var(--color-border); padding-top: 20px;">Anschrift</h4>
                <div class="form-grid">
                    <div class="form-column">
                       <p><label for="strasse">Straße</label><input type="text" id="strasse" name="strasse" value="<?php echo htmlspecialchars($customer['Strasse'] ?? ''); ?>"></p>
                       <p><label for="plz">PLZ</label><input type="text" id="plz" name="plz" value="<?php echo htmlspecialchars($customer['PLZ'] ?? ''); ?>"></p>
                    </div>
                    <div class="form-column">
                         <p><label for="hausnummer">Nr.</label><input type="text" id="hausnummer" name="hausnummer" value="<?php echo htmlspecialchars($customer['Hausnummer'] ?? ''); ?>"></p>
                         <p><label for="ort">Wohnort</label><input type="text" id="ort" name="ort" value="<?php echo htmlspecialchars($customer['Ort'] ?? ''); ?>"></p>
                    </div>
                </div>
                <div class="form-actions" style="margin-top: 30px;">
                    <input type="submit" value="Änderungen speichern" class="btn btn-warning">
                    <a href="<?php echo $config['base_url']; ?>/support/kundendetails/<?php echo $customer['UserId']; ?>" class="btn btn-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include dirname(__DIR__, 2) .'/templates/footer.php'; ?>