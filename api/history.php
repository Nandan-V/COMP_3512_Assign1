<?php
chdir('..');
require_once 'includes/db.inc.php';
header('Content-Type: application/json');



$sym  = $_GET['ref'];
$sql = "
  SELECT date, open, high, low, close, volume
  FROM history
  WHERE symbol = '" . $sym . "'
  ORDER BY date ASC
";

$st = $pdo->query($sql);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
