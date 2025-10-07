<?php
$timeout_duration = 10800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Session has expired
    session_unset();
    session_destroy();
    header("Location: ../../index.php?timeout=1");
    exit();
}
// 3. Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();
?>