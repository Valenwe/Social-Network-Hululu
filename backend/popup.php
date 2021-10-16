<?php if (!empty($errors)) : ?>
    <div class="error">
        <?php foreach ($errors as $error) : ?>
            <p><?php echo $error ?></p>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?php if (!empty($_SESSION['success'])) : ?>
    <div class="success">
        <?php foreach ($_SESSION['success'] as $suc) : ?>
            <p><?php echo $suc ?></p>
        <?php endforeach ?>
    </div>
<?php endif ?>