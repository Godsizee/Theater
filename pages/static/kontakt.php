<?php
// Logik befindet sich jetzt in StaticPageController@showKontakt und handleKontakt
// Variablen werden vom Controller bereitgestellt.
include dirname(__DIR__, 2) . '/templates/header.php';
?>

<div class="container container-lg" style="margin-top: 100px;">
    <h1 class="main-title text-center">Kontaktieren Sie uns</h1>
    
    <?php if ($message_sent): ?>
        <div class="message success">
            Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.
        </div>
    <?php else: ?>
        <?php if (!empty($error_message)): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        
        <p class="text-center" style="max-width: 600px; margin: 0 auto 30px auto;">
            Haben Sie Fragen oder Anregungen? Nutzen Sie das untenstehende Formular, um uns eine Nachricht zu senden.
        </p>
        
        <form action="<?php echo $config['base_url']; ?>/kontakt/send" method="post" style="max-width: 600px; margin: 0 auto;">
            <p>
                <label for="name">Ihr Name</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </p>
            <p>
                <label for="email">Ihre E-Mail-Adresse</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </p>
            <p>
                <label for="subject">Betreff</label>
                <input type="text" id="subject" name="subject" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
            </p>
            <p>
                <label for="message">Ihre Nachricht</label>
                <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </p>
            <p>
                <input type="submit" value="Nachricht senden" class="btn btn-primary" style="width: 100%;">
            </p>
        </form>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__, 2) . '/templates/footer.php'; ?>