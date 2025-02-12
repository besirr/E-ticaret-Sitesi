<?php
session_start();
session_destroy();  //oturumu kapat/öldür
header('location: giris.php');
?>
