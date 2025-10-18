<?php
// File: api/portfolio.php
// Returns a user's portfolio as JSON (uses 'amount' as the quantity column).

chdir('..'); // so includes/ works from /api
require_once 'includes/db.inc.php';
header('Content-Type: application/json');

// require ?ref=<userId>
if (!isset($_GET['ref']) || $_GET['ref'] === '') {
  echo json_encode(array("error" => "Missing ref (user id)"));
  exit;
}

$uid = $_GET['ref'];

// Base rows: user's holdings joined with company info; quantity is 'amount'
$sql = "
  SELECT p.userId AS userId,
         p.symbol  AS symbol,
         p.amount  AS qty,
         c.name    AS name,
         c.sector  AS sector
  FROM portfolio p
  JOIN companies c ON c.symbol = p.symbol
  WHERE p.userId = '" . $uid . "'
  ORDER BY p.symbol
";

$st = $pdo->query($sql);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// For each symbol, fetch latest close and compute value
$result = array();
for ($i = 0; $i < count($rows); $i++) {
  $sym = $rows[$i]['symbol'];
  $qty = (float)$rows[$i]['qty'];

  $hsql = "SELECT close FROM history WHERE symbol = '" . $sym . "' ORDER BY date DESC LIMIT 1";
  $hst  = $pdo->query($hsql);
  $hrow = $hst->fetch(PDO::FETCH_ASSOC);

  $last_close = $hrow ? (float)$hrow['close'] : 0.0;
  $value = $qty * $last_close;

  $rows[$i]['last_close'] = $last_close;
  $rows[$i]['value'] = $value;

  $result[] = $rows[$i];
}

echo json_encode($result);
