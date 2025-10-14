<?php

require_once 'includes/config.inc.php';
try {
    $pdo = new PDO(DBCONNSTRING);
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}
?>
