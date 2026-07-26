<?php if (!empty($mensajes)): ?>
    <div class="mensajes">
        <?php foreach ($mensajes as $tipo => $lista): ?>
            <?php foreach ($lista as $mensaje): ?>
                <div class="mensaje mensaje--<?php echo htmlspecialchars((string) $tipo, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars((string) $mensaje, ENT_QUOTES); ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
