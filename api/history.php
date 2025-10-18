<?php

chdir('..');


require_once 'includes/db.inc.php';

// Tell the client we return JSON
header('Content-Type: application/json');

// Require a stock symbol (?ref=SYMBOL). If missing, return a small JSON error and stop.
if (!isset($_GET['ref']) || $_GET['ref'] === '') {
  echo json_encode(array('error' => 'Missing ref (symbol)'));
  exit;
}

// Read inputs
$sym  = $_GET['ref'];                     // required
$from = isset($_GET['from']) ? $_GET['from'] : ''; // optional start date YYYY-MM-DD
$to   = isset($_GET['to'])   ? $_GET['to']   : ''; // optional end date   YYYY-MM-DD

// Build the SQL. Start with all rows for the symbol...
$sql = "SELECT date, open, high, low, close, volume
        FROM history
        WHERE symbol = '" . $sym . "'";

// ...and if both dates are provided, add a simple range filter.
if ($from !== '' && $to !== '') {
  $sql .= " AND date >= '" . $from . "' AND date <= '" . $to . "'";
}

// Always order by ascending date (matches lab examples)
$sql .= " ORDER BY date ASC";

// Run the query and fetch all rows as associative arrays
$st = $pdo->query($sql);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// Output JSON
echo json_encode($rows);
