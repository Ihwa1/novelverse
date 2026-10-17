<?php
require_once __DIR__ . '/../includes/init.php';

// Allow Admins only.
if (
    !$nv_user ||
    (int)($nv_user['is_admin'] ?? 0) !== 1
) {
    header('Location: /novelverse/public/index.php');
    exit;
}
?>
