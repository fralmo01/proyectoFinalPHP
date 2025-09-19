<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../app/core/Router.php';
$router = new Router();
$router->run();
?>