<?php
session_start();
session_destroy();
header('Location: admin_Login.php');
exit();
?>
