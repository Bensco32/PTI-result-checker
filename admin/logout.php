<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
session_unset();
session_destroy();
session_start();
session_regenerate_id(true);
redirect('login.php');
