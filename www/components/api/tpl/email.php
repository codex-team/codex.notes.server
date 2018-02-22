<?php if (isset($invited_username)): ?>
Greetings, <?= $invited_username ?>!
<?php else: ?>
Greetings!
<?php endif; ?>

<?= $owner_username ?> invited you to the folder "<?= $folder_title ?>"

If you would like to access, please follow the link: <?= $join_link ?>