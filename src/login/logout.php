<?php
session_start();
session_unset(); 
session_destroy();
header("Location: /lavorato-page/src/login/login.php"); 
exit();
?>
