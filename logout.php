<?php
include('tokenizr.php');
if (isset($_COOKIE['token'])) {
	killToken($_COOKIE['token']);
    unset($_COOKIE['token']);
    setcookie('token', '', time() - 3600); // empty value and old timestamp
}
header("location: index.php");
?>
