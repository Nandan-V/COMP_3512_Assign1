<?php
// Move up one folder so relative paths (includes/...) work from /api
chdir('..');
require_once 'includes/db.inc.php';
// Tell the browser this endpoint returns JSON
header('Content-Type: application/json');

// If a symbol is provided (?ref=AAPL), return just that company.
// Otherwise, return a list of all companies (basic fields).
if (isset($_GET['ref']) && $_GET['ref'] !== '') {
  // One company by symbol
  $sym = $_GET['ref'];
  $sql = "SELECT symbol, name, exchange, sector, description
          FROM companies
          WHERE symbol = '" . $sym . "'
          ORDER BY symbol";
} else {
  // All companies (no description to keep it lighter)
  $sql = "SELECT symbol, name, exchange, sector
          FROM companies
          ORDER BY symbol";
}

// Run the query and fetch everything as associative arrays
$st = $pdo->query($sql);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// Output JSON 
echo json_encode($rows);
?>
