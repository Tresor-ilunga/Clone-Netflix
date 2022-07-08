<?php

session_start(); // Initialiser
session_unset(); // Desactive
session_destroy(); // Detruire

setcookie('auth', '', time() - 1);

header('location: index.php');
exit();