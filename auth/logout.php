<?php
// auth/logout.php
require_once __DIR__ . '/../config/functions.php';
startSession();
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;
